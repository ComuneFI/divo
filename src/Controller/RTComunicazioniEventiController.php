<?php

// src/Controller/DummyController.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
//in order to let possible render templates
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Eventi;
use App\Entity\Confxvotanti;
use App\Entity\Utenti;
use App\Entity\Enti;
use App\Entity\Entexevento;

use App\Service\AppProxyRest;
use App\Service\RTServicesProvider;
use App\Service\ORMManager;
use App\Service\StatesService;

/**
 * This is the controller managing navigation of app
 */
class RTComunicazioniEventiController extends DivoController {

    /**
     * @Route("/service/comunicazionieventi", name="downComunicazionieventi")
     */
    public function getComunicazioniEventi() 
    {
        $serviceURL = $this->RTServicesProvider->getRT_ComunicazioniEventi();

        $payload = $this->RTServicesProvider->getServiceUserPayload($serviceURL);

        $config_keys = explode(',', getenv('RT_EVENT_CONFIG_LIST'));

        //include array as JSON payload and perform POST call
        $proxyResponse = $this->AppProxyREST->doPOST($serviceURL, $payload);
        $phpObj = $proxyResponse['json'];

        //prepare the array of events that I have to move away on workflow if all operations are fine
        $eventi = [];

        try {

            $this->ORMmanager->beginTransaction();
            //set already existent records to off
            $this->disableAlreadyExistentData();
            //insert new events (they can be more than 1)
            foreach ($phpObj->listaComunicazioniEvento as $comunicazione) {
                //if comunicazione is a VOTI type, then is an event
                if ($comunicazione->tipoComunicazione == 'voti') {
                    $evento = new Eventi();
                    $evento->setEvento($comunicazione->evento);
                    $evento->setDescrizioneEvento($comunicazione->descrizione);
                    $evento->setCodiceEvento($comunicazione->codice);
                    $evento->setDataEvento(new \DateTime($comunicazione->data));
                    //it proceeds to generate JSON configuration object
                    $config = [];
                    foreach ($config_keys as $key) {
                        $config[$key] = $comunicazione->$key;
                    }
                    $config_obj = json_encode($config);
                    $evento->setConfigurazioni($config_obj);  
                    //move the event to the next stage
                    $this->wfService->moveNextState($evento, StatesService::ENT_EVENT);
                    //push entity on database
                    $this->ORMmanager->insertEntity($evento);
                    //$eventi [ $comunicazione->evento ] =  $evento->getId();
                    $eventi [$comunicazione->evento] = $evento;

                    //we proceed to link event to ente
                    $entexevento = new Entexevento();
                    $entexevento->setEventi($evento);
                    $serviceUser = $this->ORMmanager->getServiceUser();
                    $entexevento->setEnti($serviceUser->getEnti());

                    $this->ORMmanager->insertEntity($entexevento);
                }
            }

            //insert communications linked to each event 
            foreach ($phpObj->listaComunicazioniEvento as $comunicazione) {
                if ($comunicazione->tipoComunicazione == 'votanti') {
                    $entity_comunicazione = new Confxvotanti();
                    $entity_comunicazione->setComunicazioneDesc($comunicazione->descrizione);
                    $entity_comunicazione->setComunicazioneFinal($comunicazione->finale);
                    $entity_comunicazione->setComunicazioneCodice($comunicazione->codice);

                    //it proceeds to generate JSON configuration object
                    $config = [];
                    foreach ($config_keys as $key) {
                        $config[$key] = $comunicazione->$key;
                    }
                    $config_obj = json_encode($config);
                    $entity_comunicazione->setConfigurazioni($config_obj);
                    $entity_comunicazione->setEventi($eventi[$comunicazione->evento]);

                    $this->ORMmanager->insertEntity($entity_comunicazione);
                }
            }
            //we can proceed to commit all operation
            $this->ORMmanager->commit();

        } catch (\Exception $e) {
            $this->ORMmanager->rollback();
            throw $e;
        }

        return $this->redirectToRoute('readComunicazioniEventi');
    }

    /**
     * It set to OFF already existent data
     */
    private function disableAlreadyExistentData() 
    {
        //Read items visible by logged service user / ente
        $communications = $this->divoMiner->readComunicazioniEventiArray();
        $enteevento = $this->divoMiner->getEventsLinksByEnte($this->ORMmanager->getServiceUser());
        $events = $this->divoMiner->getEventsFromLinks($enteevento);
        $this->ORMmanager->setOffArray($events);
        $this->ORMmanager->setOffArray($communications);
        $this->ORMmanager->setOffArray($enteevento);
    }

    /**
     * @Route("/divodb/comunicazionieventi", name="readComunicazioniEventi")
     */
    public function readFromDB() 
    {
        $template = "regione/regione.down.comunicazionieventi.html.twig";
        $template_par = [];
        $serviceUser = $this->ORMmanager->getServiceUser();

        //$entexevento_arr = $this->ORMmanager->getEntityObjects('App\Entity\Entexevento');
        $entexevento_arr = $this->ORMmanager->getActiveEntityObjects( $this->RTServicesProvider->getSeedEnteEvento() );
        $visible_events = [];
        foreach ($entexevento_arr as $entexevento) {
            if ($entexevento->getEnteId() == $serviceUser->getEnti()->getId()) {
                $event = $entexevento->getEventi();
                $vec = [
                    'evento' => $event,
                    'stato' => $this->wfService->getActualStateDesc($event, StatesService::ENT_EVENT),
                    'comunicazioni' => $this->ORMmanager->getEntityObjects(RTServicesProvider::ENT_COMUNICAZIONI, [
                        'evento_id' => $event->getId(),
                    ]),
                ];
                array_push($visible_events, $vec);
            }
        };

        $template_par = [
            'visible_couples' => $visible_events,
        ];


        return $this->render($template, $template_par);
    }

}
