<?php

/**
 * Review this class in accord to https://www.php-fig.org/psr/psr-12/.
 * This is the class responsible to control send of Scrutini and read of them since Divo database.
 */

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Rxsezioni;
use App\Entity\Rxvotinonvalidi;
use App\Entity\Circoscrizioni;
use App\Entity\Confxvotanti;
use App\Entity\Candidatiprincipali;
use App\Entity\Listapreferenze;

use App\Service\AppProxyRest;
use App\Service\RTServicesProvider;
use App\Service\ORMManager;
use App\Service\RTDivoDataMiner;
use App\Service\StatesService;

/**
 * This is the controller that sends data to RT regarding "Scrutini" (Final Results).
 * It orchestrates also navigation between send data and read data for this specific argument.
 * Section is the smaller unit of data managed by this controller.
 */
class RTInvioScrutiniController extends DivoController
{

    private $rxscrutini;
    private $rxscrutiniliste;
    private $rxvotinonvalidi;
    private $rxvotanti;

    private $displayData;
    
    /**
     * It's responsible to push 'Scrutini' communication from Divo to Target System 
     * 
     * @Route("/service/send/scrutini/{sezid}", name="pushScrutini")
     */
    public function pushScrutini(Request $request, $sezid)
    {
        //it prepares the list of parameters.
        $params = [];

        //if user has requested to push data for a specific Section
        if (!$sezid == '') {
            $reply = $this->sendDatatoRT($sezid);
            $params = [ 
                'esito' => $reply,
            ];
        }
        
        return $this->redirectToRoute('readScrutini', $params);
    }

    /**
     * It's responsible to push 'Scrutini' communication from Divo to Target System 
     * By JSON Ajax mode.
     * @Route("/service/post/scrutini/{sezid}", name="postScrutinio")
     */
    public function postScrutinio(Request $request, $sezid)
    {
        $reply = $this->sendDatatoRTWrapped($sezid);

        $params = [ 
            'esito' => $reply,
        ];
        return new JsonResponse($params);
    }

    /**
     * It's responsible to push a range of 'Scrutini' from Divo to Target System 
     * By JSON Ajax mode.
     * @deprecated it was used before than multiple-AJAX
     * @Route("/service/post/range/scrutini", name="postScrutiniRange")
     */
    public function postScrutiniRange(Request $request)
    {
        //it prepares the list of parameters.
        $params = [];
        //$reply = null;
        //get starting and final section values provided for the range 
        $startSec=$request->get('start');
        $finalSec=$request->get('end');
        $errorMessage = $this->validityCheck($startSec, $finalSec);
        $reply = $this->deliverMultipleSections($errorMessage, $this->getArraySequence($startSec, $finalSec) );

        $params = [ 
            'esito' => $reply,
        ];
       
        return new JsonResponse($params);
    }


    /**
     * It's responsible to push a changed 'Scrutini' from Divo to Target System 
     * By JSON Ajax mode.
     * 
     * @Route("{eventid}/scrutini/sezioni/changed", name="getScrutiniChanged")
     */
    public function getArrayScrutiniChanged(Request $request, $eventid)
    {
        //if display-Data are already stored it takes them, otherwise it computes again
        if (!isset($this->displayData)) {
            $this->displayData = $this->prepareToRender();
        }
        //take the array of items (event, section, changed flag)
        $data = $this->displayData[$eventid]->array;

        $targetArray = array();
        foreach($data as $item) {
            //it is enough that 1 vote record has changed in order to re-send the entire section data
            if ($item->changed == 1 or $item->changedVotanti==1) {
                array_push($targetArray, [
                    'id' => $item->object['sezione']->getId(),
                    'desc' => $item->object['sezione']->getDescrizione(),
                    ]);
            }
        }

        $response = [
            'esito' => $this->getEsitoMessage(1,''),
            'array' => $targetArray,
        ];
       
        return new JsonResponse($response);
    }

    /**
     * It's responsible to push a changed 'Scrutini' from Divo to Target System 
     * By JSON Ajax mode.
     * @deprecated it was used before than AJAX-multiple send
     * @Route("/service/post/event/{eventid}/scrutini", name="postScrutiniChanged")
     */
    public function postScrutiniChanged(Request $request, $eventid)
    {
        //it prepares the list of parameters.
        $params = [];

        //if display-Data are already stored it takes them, otherwise it computes again
        if (!isset($this->displayData)) {
            $this->displayData = $this->prepareToRender();
        }
        //take the array of items (event, section, changed flag)
        $data = $this->displayData[$eventid]->array;

        $targetArray = array();
        foreach($data as $item) {
            //it is enough that 1 vote record has changed in order to re-send the entire section data
            if ($item->changed == 1) {
                array_push($targetArray, $item->object['sezione']->getId());
            }
        }

        //It proceeds to deliver multiple messages to the target service thank to the computed target-Array
        $reply = $this->deliverMultipleSections(null, $targetArray );

        $params = [ 
            'esito' => $reply,
        ];
       
        return new JsonResponse($params);
    }

    /**
     * @Route("/divodb/scrutini", name="readScrutini")
     */
    public function readScrutini(Request $request) 
    {
        $template = "source/source.scrutini.html.twig";
        $template_par = [];

        $data = $this->prepareToRender();
        $this->displayData = $data;

        $template_par = [
            'data' => $data,
        ];

        $reply = $request->get('esito');
        if (isset($reply)) {
            $template_par['communication_esito'] = $reply;          
        }


   
       $template_par['sections_valid']= $this->divoMiner->getSectionsEnabledToSend('scrutini');

       return $this->render($template, $template_par);
    }

    /**
     * Set status wf desc into section, using a map in order to do the less possible accesses to the database
     */
    private function setSectionStatus(array $map, Rxsezioni $section): array
    {
        if(isset($map[$section->getStatoWf()])) {
            $section->setStatoWfDesc($map[$section->getStatoWf()]);
        }
        else {
            $section->storeStatoWfDesc($this->wfService);
            $map[$section->getStatoWf()] = $section->getStatoWfDesc();
        }
        return $map;
    }

    /**
     * It prepares data retrieved by database and set to render on twig template.
     * It uses a report in order to speed up the retrieve process.
     * source/source.scrutini.html.twig
     */
    
     private function prepareToRender(): array
    {
        $data = array();
        //my visible sections
        $sezioni = $this->divoMiner->getPrecookedSezioni();
        //execute report about votes including them that have changed compared to the last send
        $results = $this->divoMiner->getReportScrutiniAllBitnew();
        //find not valid votes including them that have changed compared to the last send
        $notValidVotes = $this->divoMiner->getReportScrutiniVotiNonValidiBitnew();

        

        //this is final array of events
        //$v_eventi = array();
        //each item of this array it will be composed by 1 event and an array of sections
        //each section wil have an array of results
        //map of status descriptions
        $desc_map = array();

        //so, I extract events and associate sections
        foreach ($sezioni as $sezione) {
            $evento = $sezione['evento'];
            if (!isset($data[$evento->getId()])) {
                $item = new \stdClass();
                $evento->storeStatoWfDesc($this->wfService);
                $item->object = $evento;
                $item->array = array();
                $data[$evento->getId()] = $item;
            }
            $secItem = new \stdClass();
            $secItem->object = $sezione;
            //set status wf desc
            $desc_map = $this->setSectionStatus($desc_map, $sezione['sezione']);
            $secItem->array = array();
            $secItem->changed = 0;
            $secItem->changedVotanti = 0;
            $secItem->schede_bianche = '';
            $secItem->schede_nulle = '';
            $secItem->schede_contestate = '';
            $secItem->num_votanti_maschi = '';
            $secItem->num_votanti_femmine = '';
            $secItem->num_votanti_totali = '';
            $data[$evento->getId()]->array[$sezione['sezione']->getId()] = $secItem;
        }

        //append results to vec
        foreach ($results as $result) {
            array_push($data[$result['evento_id']]->array[$result['sezione_id']]->array, $result);
            if($result['bitnew']==1) {
                $data[$result['evento_id']]->array[$result['sezione_id']]->changed = 1;
            }
        }
        $serviceUser = $this->ORMmanager->getServiceUser();
        $affluenceRecords = $this->divoMiner->getReportAffluenceFinalBitnew($serviceUser->getEnti()->getId());
   
        //append not valid votes to the main structure and set as changed if no others changes
        foreach ($notValidVotes as $notValid) {
            if( isset ($data[$notValid['evento_id']])) {
                $data[$notValid['evento_id']]->array[$notValid['sezione_id']]->schede_bianche = $notValid['schede_bianche'];
                $data[$notValid['evento_id']]->array[$notValid['sezione_id']]->schede_nulle = $notValid['schede_nulle'];;
                $data[$notValid['evento_id']]->array[$notValid['sezione_id']]->schede_contestate = $notValid['schede_contestate'];
                $data[$notValid['evento_id']]->array[$notValid['sezione_id']]->validi_presidente = $notValid['validi_presidente'];
                if($notValid['bitnew']==1) {
                    $data[$notValid['evento_id']]->array[$notValid['sezione_id']]->changed = 1;
                }
            }
        }

        foreach ($affluenceRecords as $affluenceRecord) {
            if( isset ($data[$affluenceRecord['evento_id']])) {
                $data[$affluenceRecord['evento_id']]->array[$affluenceRecord['rxsezione_id']]->num_votanti_maschi = $affluenceRecord['num_votanti_maschi'];
                $data[$affluenceRecord['evento_id']]->array[$affluenceRecord['rxsezione_id']]->num_votanti_femmine = $affluenceRecord['num_votanti_femmine'];
                $data[$affluenceRecord['evento_id']]->array[$affluenceRecord['rxsezione_id']]->num_votanti_totali = $affluenceRecord['num_votanti_totali'];
                if($affluenceRecord['bitnew']==1) {
                    $data[$affluenceRecord['evento_id']]->array[$affluenceRecord['rxsezione_id']]->changedVotanti = 1;
                }
            }
        }
        
        return $data;
    }

    /**
     * Create the payload needed in order to send Scrutini values to the RT service
     */
    private function appendPayload($payload, Rxsezioni $section, Confxvotanti $communication) 
    {
        //read configurations
        $configurazioni = json_decode($communication->getConfigurazioni());
        $confVotiDiCui = $configurazioni->gestioneVotiDiCui;
        $confAffluenzeMF = $configurazioni->gestioneAffluenzaMF;
        $acquisizioneListe = $configurazioni->acquisizioneListe;
        
        //ACTIVATE TO SEE TIME OF EXECUTION   $start = microtime(true);
        //get circoscrizione where section belongs to
        $circoscrizione = $section->getCircoscrizioni();

        $message = new \StdClass();
        //append circoscrizione to the message
        $message->circoscrizione = $this->setMessageCircoscrizione($circoscrizione);
        //append sezione to the message
        $message->sezione = $this->setMessageSezione($section);
        //append votanti to the message
        $message->votanti = $this->setMessageVotanti($communication, $section, $confAffluenzeMF);

        //which is the list of main candidates for that circoscrizione
        $mainCandidates = $this->divoMiner->getMainCandidates($circoscrizione);
      

        //fetch non valid votes for that section
        $notValids = $this->divoMiner->getVotiNonValidi($section);

        //fetch lists for each main candidate
        $candidati = array();
        foreach($mainCandidates as $candidate) {
            //iterate main candidate list in order to produce payload.scrutinioCandidato
            $scrutinioCandidatoItem = new \StdClass();
            $scrutinioCandidatoItem->scrutinioCandidato = $this->setMessageCandidato($circoscrizione, $section, $candidate, $confVotiDiCui);

            $liste_array = array();
            $prefLists = $this->divoMiner->getListe( $candidate );

           
            //for each list it extracts votes and prepare cooked payload section
            if( $acquisizioneListe == 1) {
                foreach($prefLists as $list) {
                    array_push($liste_array, $this->setMessageLista($section, $candidate, $list));
                }            
                $scrutinioCandidatoItem->listaScrutinioListe = $liste_array;
            }
            array_push($candidati, $scrutinioCandidatoItem);
        }
        $message->listaScrutinioCandidatiListe = $candidati;
        //we insert the total amount of valid votes for the main candidate
        if ($confVotiDiCui === 0) {
            $value = $notValids->getTotVotiDicuiSoloCandidato();
            $message->votiTotaliDiCuiCandidato = (isset($value)) ? $value : 0;
        }
   

        //apppend voti non validi
        $message->votiNonValidi = $this->setMessageVotiNonValidi($section, $notValids);

        $payload['scrutinioSezione'] = $message;
    
        //CONTROL TIME OF ELABORATION $time_elapsed_secs = microtime(true) - $start;
        //return final additional payload
        return $payload;
    }


    /**
     * Move the event, or section, to the next stage if possible
     */
    private function moveWfOn($item, $entityType) {
        $advanced = false;
        $cap = $this->wfService->getCapPools();
        if ($item->getStatoWf() != $cap) {
            $this->wfService->moveNextState($item, $entityType);
            $advanced = true;
        }
        return $advanced;
    }

    /**
     * Perform the action to send data to RT using provided REST API.
     * Move away event and section to the next state, if they didn't reach final one.
     */
    protected function sendDatatoRT($sectionCode) 
    {
        //retrieve the Section object
        $section = $this->ORMmanager->getEntityById(Rxsezioni::class , $sectionCode);
        //get event where section has linked
        $event = $section->getCircoscrizioni()->getEventi();

        //get the communication of affluences related to final communication ( code of event and final communication are the same)
        //TODO change the usage of this deprecated method
        //TODO clarify that code of last communication is the same of event code
         $communication = $this->ORMmanager->getActiveEntityPop( RTServicesProvider::ENT_COMUNICAZIONI, [
            'comunicazione_codice' => $event->getCodiceEvento(), 
            'evento_id' => $event->getId(), 
        ]);

        //which service we need to invoke
        $serviceURL = $this->RTServicesProvider->getRT_Scrutini();
        //ask for payload to ORMmanager if already available, otherwise it return a new one
        $payload = $this->RTServicesProvider->getServiceUserPayload($serviceURL, $event);


        //Prepare arrays to receive elements to be updated (sent=1, action log)
        $this->rxscrutini = array();
        $this->rxscrutiniliste = array();
        $this->rxvotinonvalidi = array();
        $this->rxvotanti = array();
        //Execute the request to the RT exposed service
        //include array as JSON payload and perform POST call
        $reply = null;
        try { 
            //append additional payload to the request
            $payload = $this->appendPayload($payload, $section, $communication);
            $this->ORMmanager->beginTransaction();
            //include array as JSON payload and perform POST call
            $proxyResponse = $this->AppProxyREST->doPOST($serviceURL, $payload);
            $reply = $proxyResponse['json'];
            $actionLogId = $proxyResponse['key'];
            
            

            if ($reply->esito->codice == '1') {
                //move the event to the next stage
                $this->moveWfOn($event, StatesService::ENT_EVENT);
                $this->moveWfOn($section, StatesService::ENT_SECTION);

                //set as sent records
                $this->setArraySent($this->rxscrutiniliste, $actionLogId);
                $this->setArraySent($this->rxscrutini, $actionLogId);
                $this->setArraySent($this->rxvotinonvalidi, $actionLogId);
                $this->setArraySent($this->rxvotanti, $actionLogId);
            }
            else {
                //set as sent records
                $this->setArrayLog($this->rxscrutiniliste, $actionLogId);
                $this->setArrayLog($this->rxscrutini, $actionLogId);
                $this->setArrayLog($this->rxvotinonvalidi, $actionLogId);
                $this->setArrayLog($this->rxvotanti, $actionLogId);
            }
            //this should update also event entity
            $this->ORMmanager->updateEntity();
            $this->ORMmanager->commit();
        }
        catch(\Exception $e) {
            $this->ORMmanager->rollback();
            throw $e;
            //throw new \RuntimeException('Could not load file.');
            //return $this->getEsitoMessage(31, 'Errore da dentro sendRT!');
        }
        return $reply;
    }

    /**
     * prepare cooked object for payload.circoscrizione
     */
    private function setMessageCircoscrizione(Circoscrizioni $circoscrizione): \StdClass
    {
        $circox = new \StdClass();
        $circox->id = $circoscrizione->getIdTarget();
        $circox->descrizione = $circoscrizione->getCircDesc();
        return $circox;
    }

    /**
     * prepare cooked object for payload.sezione
     */
    private function setMessageSezione(Rxsezioni $section) 
    {
        $sezione = new \StdClass();
        $sezione->numero = $section->getNumero();
        $sezione->descrizione = $section->getDescrizione();
        return $sezione;
    }

    /**
     * prepare cooked object for payload.sezione
     */
    private function setMessageVotiNonValidi(Rxsezioni $section, &$NotValids) 
    {
        $votiNonValidi = new \StdClass();
        //moved upper level
        //$result = $this->divoMiner->getVotiNonValidi($section);

        if (isset($NotValids)) {
            $votiNonValidi->schedeBianche = $NotValids->getNumeroSchedeBianche();
            $votiNonValidi->schedeNulle = $NotValids->getNumeroSchedeNulle();
            $votiNonValidi->votiContestatiCoalizioni = $NotValids->getNumeroSchedeContestate();
        }

        array_push($this->rxvotinonvalidi, $NotValids);

        return $votiNonValidi;
    }

    /**
     * prepare cooked object for payload.votanti
     */
    private function setMessageVotanti(Confxvotanti $communication, Rxsezioni $section, $confAffluenzeMF) 
    {
        $votanti = new \StdClass();
        //read from database record about votes
        $votes = $this->divoMiner->getAffluenze($communication, $section);

        //considering includeAffluenzaMF configuration
        $votanti->idComunicazioneElettorale = $communication->getComunicazioneCodice();
        $votanti->descrizione = $communication->getComunicazioneDesc(); 
        $votanti->numeroVotantiTotali = $votes->getNumVotantiTotali();
        if ($confAffluenzeMF == 1) {
            $votanti->numeroVotantiMaschi = $votes->getNumVotantiMaschi();
            $votanti->numeroVotantiFemmine = $votes->getNumVotantiFemmine();
            $votanti->numeroVotantiTotali = $votanti->numeroVotantiMaschi + $votanti->numeroVotantiFemmine;
        }

        array_push($this->rxvotanti, $votes);

        return $votanti;
    }

    /**
     * Set the message for each single candidate.
     * Store each managed scrutinio into local private variable.
     */
    private function setMessageCandidato(Circoscrizioni $circoscrizione, Rxsezioni $section, Candidatiprincipali $candidate, $confVotiDiCui) 
    {
        $candObj = new \StdClass();
        $candObj->votiTotaleCandidato = '';

        $candObj->idCandidato = $candidate->getIdTarget();
        //retrieve position of candidate for that circoscrizione
        $candObj->posizioneCandidato = $candidate->getPosizione($this->ORMmanager, $circoscrizione->getId());
        $candObj->nomeCandidato = $candidate->getNome();
        $candObj->cognomeCandidato = $candidate->getCognome();

        //Look for final votes
        $scrutinio = $this->divoMiner->getScrutiniCandidato($section, $candidate);
        array_push($this->rxscrutini, $scrutinio);
        if (isset($scrutinio)) {
            $candObj->votiTotaleCandidato = $scrutinio->getVotiTotaleCandidato();
            //value of voti di cui solo candidato
            if ($confVotiDiCui == 1) {
                $candObj->votiDiCuiSoloCandidato = $scrutinio->getVotiDicuiSoloCandidato();
            }
        }
        return $candObj;
    }

    /**
     * Set the message for each List linked to a main candidate.
     * It retrieves votes collected on the single list.
     */
    private function setMessageLista(Rxsezioni $section, Candidatiprincipali $candidate, Listapreferenze $list) 
    {
        $lista = new \StdClass();
        $lista->votiTotaleLista = ''; 
        $lista->idLista = $list->getIdTarget();
        //retrieve position of list for that candidate
        $lista->posizioneLista = $list->getPosizione($this->ORMmanager, $candidate->getId());
        $lista->nomeLista = $list->getListaDesc();

        $scrutinioLista = $this->divoMiner->getScrutiniListaCandidato($section, $list);        
        if (isset($scrutinioLista)) {
            array_push($this->rxscrutiniliste, $scrutinioLista);
            $lista->votiTotaleLista = $scrutinioLista->getVotiTotLista(); 
        }
        //pollings not existent for lists
        else {
            $lista->votiTotaleLista = 0;
        }
        return $lista;
    }



}