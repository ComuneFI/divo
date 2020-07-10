<?php

// src/Controller/DummyController.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
//in order to let possible render templates
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Eventi;
use App\Entity\Confxvotanti;
use App\Entity\Utenti;
use App\Entity\Enti;
use App\Entity\Candidatisecondari;
use App\Entity\Candidatiprincipali;
use App\Entity\Secondarioxlista;
use App\Entity\Listaxprincipale;
use App\Entity\Listapreferenze;
use App\Entity\Circoscrizioni;
use App\Entity\Circoxcandidato;

use App\Service\AppProxyRest;
use App\Service\RTServicesProvider;
use App\Service\RTDivoDataMiner;
use App\Service\ORMManager;
use App\Service\StatesService;

/**
 * This is the controller managing navigation of app
 */
class RTCandidatiListeController extends DivoController {

    /**
     * @Route("/service/candidatiliste", name="downCandidatiListe")
     */
    public function getCandidatiListe() {
        //get utils objects for our transaction
        $serviceUser = $this->ORMmanager->getServiceUser();
        $serviceURL = $this->RTServicesProvider->getRT_Liste();

        //create an array of payloads for each specific event
        $entexevento_arr = $this->ORMmanager->getEntityObjects('App\Entity\Entexevento');
        $payloads = [];
        foreach ($entexevento_arr as $entexevento) {
            if ($entexevento->getEnteId() == $serviceUser->getEnti()->getId()) {
                $vect = [
                    'evento' => $entexevento->getEventi(),
                    'payload' => $this->RTServicesProvider->getServiceUserPayload($serviceURL, $entexevento->getEventi()),
                ];
                array_push($payloads, $vect);
            }
        };

        //Semaphore to control the disabling of all already existent on database
        $offSemaphore = true;

        //for each payload/event we invoke the service to retrieve candidates and lists
        foreach ($payloads as $payload) {
            $proxyResponse = $this->AppProxyREST->doPOST($serviceURL, $payload['payload']);
            $phpObj = $proxyResponse['json'];
            $evento = $payload['evento'];
            //move the event to the next stage
            $this->wfService->moveNextState($evento, StatesService::ENT_EVENT);
            try {
                $this->ORMmanager->beginTransaction();
                //only at first iteration entities existent on database are disabled
                if ($offSemaphore) {
                    $offSemaphore = false;
                    //disabling previous records already inserted for this ente
                    $this->disablingEntities($this->ORMmanager);
                }
                //this should update also event entity
                $this->ORMmanager->updateEntity();

                $circoscrizioni = $phpObj->listaCircoscrizioni;
                foreach ($circoscrizioni as $circoscrizione) {
                    //Insert received circoscrizioni
                    $circDivo = new Circoscrizioni();
                    $circDivo->setCircDesc($circoscrizione->descrizione)
                            ->setIdTarget($circoscrizione->id)
                            ->setEventi($payload['evento']);
                    $this->ORMmanager->insertEntity($circDivo);

                    //Proceed to read the list of main candidates, .....
                    $candidati = $circoscrizione->listaCandidati;
                    foreach ($candidati as $candidato) {

                        $candidate = new Candidatiprincipali();
                        $candidate->createFromTarget($candidato);
                        $this->ORMmanager->insertEntity($candidate);

                        //where it's declared the position of candidate inside the circoscrizione
                        $candidateOnCirc = new Circoxcandidato();
                        $candidateOnCirc->setPosizione($candidato->posizione)
                                ->setCandidatiprincipali($candidate)
                                ->setCircoscrizioni($circDivo);
                        $this->ORMmanager->insertEntity($candidateOnCirc);

                        $listeCandidato = $candidato->listaListeCandidato;
                        foreach ($listeCandidato as $listaCandidato) {
                            $listaPreferenze = new Listapreferenze();
                            $listaPreferenze->setIdTarget($listaCandidato->id)
                                    ->setListaDesc($listaCandidato->descrizione);
                            $this->ORMmanager->insertEntity($listaPreferenze);

                            //Link each Preference list with its position to the main candidate
                            $listaxprincipale = new Listaxprincipale();
                            $listaxprincipale->setCandidatiprincipali($candidate)
                                    ->setListapreferenze($listaPreferenze)
                                    ->setPosizione($listaCandidato->posizione);
                            $this->ORMmanager->insertEntity($listaxprincipale);

                            $listaCandidatiPreferenze = $listaCandidato->listaCandidatiPreferenze;
                            foreach ($listaCandidatiPreferenze as $candidatoPreferenze) {
                                $secondaryCandidate = new Candidatisecondari();
                                $secondaryCandidate->createFromTarget($candidatoPreferenze);
                                $this->ORMmanager->insertEntity($secondaryCandidate);

                                $secondaryForList = new Secondarioxlista();
                                $secondaryForList->setCandidatisecondari($secondaryCandidate)
                                        ->setPosizione($candidatoPreferenze->posizione)
                                        ->setListapreferenze($listaPreferenze);
                                $this->ORMmanager->insertEntity($secondaryForList);
                            }
                        }
                    }
                }
                $this->ORMmanager->commit();
            } catch (\Exception $e) {
                $this->ORMmanager->rollback();
                throw $e;
            }
        }
        return $this->redirectToRoute('readCandidatiListe');
    }

    /**
     * Disabling set of entities, needed for insertion of new results.
     */
    private function disablingEntities() 
    {
        //Read items visible by logged service user / ente
        $mainCandidates = $this->divoMiner->getAllMainCandidates();
        //Preference list visible to the user
        $preferenceLists = $this->divoMiner->readListe();
        //secondary candidates
        $secondaryCandidates = $this->divoMiner->readCandidatiSecondari();
        //links circoscrizione-candidate
        $circoxcandidates = $this->divoMiner->getCircoCandidateFromCandidates($mainCandidates);
        //circoscrizioni
        $circoscrizioni = $this->divoMiner->getCircoscrizioniFromCircoCandidateLinks($circoxcandidates);
        //main candidates - list links
        $mainListLinks = $this->divoMiner->getLinkListaFromCandidates($mainCandidates);        
        //secondary candidates - list links
        $secondaryListLinks = $this->divoMiner->getLinkListaFromSecondaryCandidates($secondaryCandidates);

        $this->ORMmanager->setOffArray($mainCandidates);
        $this->ORMmanager->setOffArray($preferenceLists);
        $this->ORMmanager->setOffArray($secondaryCandidates);
        $this->ORMmanager->setOffArray($circoxcandidates);
        $this->ORMmanager->setOffArray($circoscrizioni);
        $this->ORMmanager->setOffArray($mainListLinks);
        $this->ORMmanager->setOffArray($secondaryListLinks);
    }

    /**
     * @Route("/divodb/candidatiliste", name="readCandidatiListe")
     */
    function readFromDB() {
        $template = "regione/regione.down.candidatiliste.html.twig";
        $template_par = [];

        $visible_events = $this->divoMiner->readCandidatiListe();

        $template_par = [
            'visible_objects' => $visible_events,
        ];

        return $this->render($template, $template_par);
    }

}
