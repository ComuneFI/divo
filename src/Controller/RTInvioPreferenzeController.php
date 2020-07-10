<?php

/**
 * Review this class in accord to https://www.php-fig.org/psr/psr-12/.
 * This is the class responsible to control send of Preferences and read of them since Divo database.
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
use App\Entity\Rxpreferenze;

use App\Service\AppProxyRest;
use App\Service\RTServicesProvider;
use App\Service\ORMManager;
use App\Service\RTDivoDataMiner;
use App\Service\StatesService;

/**
 * This is the controller that sends data to RT regarding "Preferenze" (as Final Results).
 * It orchestrates also navigation between send data and read data for this specific argument.
 */
class RTInvioPreferenzeController extends DivoController
{

    private $rxpreferenze;

    private $displayData;

    /**
     * It's responsible to push 'Preferences' communication from Divo to Target System 
     * 
     * @Route("/service/send/preferenze/{sectionid}", name="pushPreferenze")
     */
    public function pushPreferenze(Request $request, string $sectionid)
    {
        $params = [];

        //if user has requested to push data for a specific Section
        $reply = $this->sendDatatoRT($sectionid);
        $params = [ 
            'esito' => $reply,
        ];
        
        return $this->redirectToRoute('readPreferenze', $params);
    }

    /**
     * It's responsible to push 'Preferenze' from Divo to Target System 
     * By JSON Ajax mode.
     * @Route("/service/post/preferenze/{sezid}", name="postPreferenza")
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
     * It's responsible to push a changed 'Preferenze' from Divo to Target System 
     * By JSON Ajax mode.
     * 
     * @Route("{eventid}/preferenze/sezioni/changed", name="getPreferenzeChanged")
     */
    public function getArrayPreferenzeChanged(Request $request, $eventid)
    {

        //if display-Data are already stored it takes them, otherwise it computes again
        if (!isset($this->displayData)) {
            $this->displayData = $this->prepareToRender();
        }
        //take the array of items (event, section, changed flag)
        //$data = $this->displayData;
        $data = $this->displayData[$eventid]->array;
        
        $targetArray = array();
        foreach($data as $item) {
            //it is enough that 1 vote record has changed in order to re-send the entire section data
            if ($item->changed == 1) {
                array_push($targetArray, [
                    'id' => $item->sezione['sezione']->getId(),
                    'desc' => $item->sezione['sezione']->getDescrizione(),
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
     * It's responsible to push a changed 'Preferenze' from Divo to Target System 
     * By JSON Ajax mode.
     * 
     * @deprecated in place of this one we use orchestrate on JS side (see divoSlicer.js)
     * @Route("/service/post/event/{eventid}/preferenze", name="postPreferenzeChanged")
     */
    public function postPreferenzeChanged(Request $request, $eventid)
    {
        //it prepares the list of parameters.
        $params = [];

        //if display-Data are already stored it takes them, otherwise it computes again
        if (!isset($this->displayData)) {
            $this->displayData = $this->prepareToRender();
        }
        //take the array of items (event, section, changed flag)
        //$data = $this->displayData;
        $data = $this->displayData[$eventid]->array;
        
        $targetArray = array();
        foreach($data as $item) {
            //it is enough that 1 vote record has changed in order to re-send the entire section data
            if ($item->changed == 1) {
                array_push($targetArray, $item->sezione['sezione']->getId());
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
     * It's responsible to push a range of 'Preferenze' from Divo to Target System 
     * By JSON Ajax mode.
     * 
     * @deprecated in place of this one we use orchestrate on JS side (see divoSlicer.js)
     * @Route("/service/post/range/preferenze", name="postPreferenzeRange")
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
     * @Route("/divodb/preferenze", name="readPreferenze")
     */
    public function readPreferenze(Request $request) 
    {
        $template = "source/source.preferenze.html.twig";
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

        return $this->render($template, $template_par);
    }

    /**
     * It prepares data retrieved by database and set to render on twig template 
     * source/source.preferenze.html.twig
     */
    private function prepareToRender(): array
    {
        $map = array();
        //$data = array();
        $serviceUser = $this->ORMmanager->getServiceUser();
        //my visible sections
        $sections = $this->divoMiner->getPrecookedSezioni(); 
        //execute report about votes including them that have changed compared to the last send
        $results = $this->divoMiner->getReportPreferenzeAllBitnew($serviceUser->getEnteId());

        //set-up an array map for wf status descriptions and limit database accesses
        $map_desc = array();
        foreach ($sections as $sectionItem) {
            $object = new \StdClass();
            $object->sezione = $sectionItem;
            $object->totaleVoti = 0;
            $object->preferenze = [];
            $object->changed = '';
            $wfCode = $sectionItem['sezione']->getStatoWf();
            if (!isset($map_desc[$wfCode])) {
                $sectionItem['sezione']->storeStatoWfDesc($this->wfService);
                $map_desc[$wfCode] = $sectionItem['sezione']->getStatoWfDesc();
            }
            else {
                $sectionItem['sezione']->setStatoWfDesc($map_desc[$wfCode]);
            }
            $event = $sectionItem['evento'];
            if (!isset($map[$event->getId()])) {
                $mappet = new \StdClass();
                $mappet->array = array();
                $mappet->event = $event;
                $map[$event->getId()] = $mappet;
                //$map[$event->getId()]->event = $event;
            }
            $map[$event->getId()]->array[$sectionItem['sezione']->getId()] = $object;
            //$data[$sectionItem['sezione']->getId()] = $object;
        }

        foreach ($results as $result) {
            /*array_push($data[$result['rxsezione_id']]->preferenze, $result);
            $data[$result['rxsezione_id']]->totaleVoti += $result['numero_voti'];
            if($result['bitnew']==1) {
                $data[$result['rxsezione_id']]->changed = 1;
            }*/
            array_push($map[$result['evento_id']]->array[$result['rxsezione_id']]->preferenze, $result);
            $map[$result['evento_id']]->array[$result['rxsezione_id']]->totaleVoti += $result['numero_voti'];
            if($result['bitnew']==1) {
                $map[$result['evento_id']]->array[$result['rxsezione_id']]->changed = 1;
            }
        }
        return $map;
    }


    /**
     * Create the payload needed in order to send Preferenze values to the RT service
     */
    private function appendPayload($payload, Rxsezioni $section, Confxvotanti $communication) 
    {
        //ACTIVATE TO SEE TIME OF EXECUTION   $start = microtime(true);
        //get circoscrizione where section belongs to
        $circoscrizione = $section->getCircoscrizioni();

        $message = new \StdClass();
        //append circoscrizione to the message
        $message->circoscrizione = $this->setMessageCircoscrizione($circoscrizione);
        //append sezione to the message
        $message->sezione = $this->setMessageSezione($section);
        //append votanti to the message
        
        //which is the list of preferences for that section
        $vListe = array();
        $rxpreferenze = $this->divoMiner->getPreferenze($section);

        $this->rxpreferenze = array_merge($this->rxpreferenze, $rxpreferenze );

        foreach ($rxpreferenze as $preferenza) {         
            $lista = $preferenza->getListaPreferenze();
            $listaObj = $this->setMessageLista($preferenza, $lista);

            if (!isset($vListe[$listaObj->id])){
                $vListe[$listaObj->id] = $listaObj;
            }

            $candObj = $this->setMessageCandidato($preferenza, $lista);
            array_push($vListe[$listaObj->id]->listaCandidatiPreferenze, $candObj);
            $vListe[$listaObj->id]->votiTotaliPreferenzaCandidati += $candObj->votiPreferenzaCandidato;
            sort($vListe[$listaObj->id]->listaCandidatiPreferenze);
        }

        //apppend voti non validi
        $message->preferenzeListe = array_values($vListe);
        sort($message->preferenzeListe);

        $payload['preferenzeSezione'] = $message;
        //CONTROL TIME OF ELABORATION $time_elapsed_secs = microtime(true) - $start;
        //return final additional payload*/

        return $payload;
    }

    /**
     * Perform the action to send data to RT using provided REST API
     */
    protected function sendDatatoRT($sectionid) 
    {
        //retrieve the Section object
        $section = $this->ORMmanager->getEntityById(Rxsezioni::class , $sectionid);
        //get event where section has linked
        $event = $section->getCircoscrizioni()->getEventi();

        //get the communication of affluences related to final communication ( code of event and final communication are the same)
        $communication = $this->ORMmanager->getActiveEntityPop( RTServicesProvider::ENT_COMUNICAZIONI, [
            'comunicazione_codice' => $event->getCodiceEvento(), 
            'evento_id' => $event->getId(), 
        ]);

        //which service we need to invoke
        $serviceURL = $this->RTServicesProvider->getRT_Preferenze();
        //ask for payload to ORMmanager if already available, otherwise it return a new one
        $payload = $this->RTServicesProvider->getServiceUserPayload($serviceURL, $event);

        //Prepare arrays to receive elements to be updated (sent=1, action log)
        $this->rxpreferenze = array();

        //append additional payload to the request
    
        $payload = $this->appendPayload($payload, $section, $communication);

        //Execute the request to the RT exposed service
        //include array as JSON payload and perform POST call
        $reply = null;
        try {            
            //include array as JSON payload and perform POST call
            //It must be outside of transaction in order to track action log
            $proxyResponse = $this->AppProxyREST->doPOST($serviceURL, $payload);

            $this->ORMmanager->beginTransaction();
            $reply = $proxyResponse['json'];
            $actionLogId = $proxyResponse['key'];
            if ($reply->esito->codice == '1') {
                //move the event to the next stage
                $this->moveWfOn($event, StatesService::ENT_EVENT);
                $this->moveWfOn($section, StatesService::ENT_SECTION);
                //set as sent records
                $this->setArraySent($this->rxpreferenze, $actionLogId);
            }
            else {
                $this->setArrayLog($this->rxpreferenze, $actionLogId);
            }
            //this should update also event entity
            $this->ORMmanager->updateEntity();
            $this->ORMmanager->commit();
        }
        catch(\Exception $e) {
            $this->ORMmanager->rollback();
            throw $e;
        }
        return $reply;
    }

    /**
     * Move the event, or section, to the next stage if possible
     */
    private function moveWfOn($item, $entityType) {
        $advanced = false;
        $cap = $this->wfService->getCapPreferences();
        if ($item->getStatoWf() != $cap) {
            $this->wfService->moveNextState($item, $entityType);
            $advanced = true;
        }
        return $advanced;
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
     * Set the message for each single candidate
     */
    private function setMessageCandidato(Rxpreferenze $preferenza, Listapreferenze $lista) 
    {
        $candObj = new \StdClass();
        $candidatoSecondario = $preferenza->getCandidatiSecondari();
        $candObj->id = $candidatoSecondario->getIdTarget();
        $candObj->cognome = $candidatoSecondario->getCognome();
        $candObj->nome = $candidatoSecondario->getNome();
        $candObj->luogoNascita = $candidatoSecondario->getLuogoNascita();
        $candObj->sesso = $candidatoSecondario->getSesso();
        $candObj->posizione = $candidatoSecondario->getPosizione($this->ORMmanager, $this->RTServicesProvider, $lista->getId() );
        $candObj->votiPreferenzaCandidato = $preferenza->getNumeroVoti();
        return $candObj;
    }

    /**
     * Set the message for each List linked to a secondary candidate.
     * It retrieves votes collected on the single list for secondary candidates
     */
    private function setMessageLista(Rxpreferenze $preferenza, Listapreferenze $lista) 
    {
        $listaObj = new \StdClass();
        $lista = $preferenza->getListaPreferenze();
        $listaObj->id = $lista->getIdTarget();
        $listaObj->descrizione = $lista->getListaDesc();
        $listaObj->posizione = $lista->getPosizione( $this->ORMmanager );
        $listaObj->votiTotaliPreferenzaCandidati = 0;
        $listaObj->listaCandidatiPreferenze = array();
        return $listaObj;
    }
}