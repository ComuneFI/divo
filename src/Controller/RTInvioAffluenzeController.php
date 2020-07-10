<?php

/**
 * This file contains Controller for sending Affluenze.
 */

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;

use App\Service\AppProxyRest;
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
use App\Entity\Rxsezioni;
use App\Entity\Rxvotanti;

use App\Service\RTServicesProvider;
use App\Service\ORMManager;
use App\Service\RTDivoDataMiner;
use App\Service\StatesService;
use App\Service\RTSentableInterface;

/**
 * This is the controller managing navigation of app.
 */
class RTInvioAffluenzeController extends DivoController
{

    /**
     * It's responsible to post 'Affluenze' communication from Divo to Target System 
     * By JSON Ajax mode.
     * @Route("/service/post/votanti/{commid}", name="postVotanti")
     */
    public function postVotanti(Request $request, $commid)
    {
        //it prepares the list of parameters.
        $params = [];

        //if user has requested to push data for a specific Section
        if (isset($commid)) {
            $reply = $this->sendDatatoRT($commid);
            $params = [ 
                'esito' => $reply,
            ];
        }
        return new JsonResponse($params);
    }



    /**
     * It's responsible to post 'Affluenze' communication from Divo to Target System 
     * By JSON Ajax mode.
     * @Route("/service/post/votantiChanged/{commid}", name="votantiChanged")
     */
    public function votantiChanged(Request $request, $commid)
    {
        //it prepares the list of parameters.
        $params = [];

        //if user has requested to push data for a specific Section
        if (isset($commid)) {
            $reply = $this->sendDataChangedtoRT($commid);
            $params = [ 
                'esito' => $reply,
            ];
        }
        return new JsonResponse($params);
    }
    /**
     *  @Route("/service/send/votanti/{commid}", name="pushVotanti")
     */
    public function pushVotanti(Request $request, $commid)
    {
        $params = [];

        //if a selection has done...
        if (isset($commid)) {
            $reply = $this->sendDatatoRT($commid);
            $params = [
                'esito' => $reply,
            ];
        }
        
        return $this->redirectToRoute('readVotanti', $params);
    }

     /**
     *  @Route("/service/send/votantiChanged/{commid}", name="pushVotanti")
     */
    public function pushVotantiChanged(Request $request, $commid)
    {
        $params = [];

        //if a selection has done...
        if (isset($commid)) {
            $reply = $this->sendDataChangedtoRT($commid);
            $params = [
                'esito' => $reply,
            ];
        }
        
        return $this->redirectToRoute('readVotanti', $params);
    }

    /**
     * It performs a massive update for sent flag and actionlogs_id foreing key
     */
    private function updateFlags(RTSentableInterface $entityInterface, array $elements, $key_log, $success=true) 
    {
        $keys = array();
        foreach($elements as $element) {
            array_push($keys, $element->getId());
        }
        $parameters = [
            'sent' => 1,
            'actionlogs_id' => $key_log,
        ];
        if (!$success) {
            $parameters['sent'] = 0;
        }
        $outcomes = $this->ORMmanager->updateAllEntitiesByKeys($entityInterface, $parameters, 'id', $keys);
    }

    /**
     * Perform the action to send data to RT using provided REST API
     */
    protected function sendDatatoRT( $commCode ) 
    {
        $ORMmanager = $this->ORMmanager;
        //we have to manage the payload creation in order to send first values
        $serviceURL = $this->RTServicesProvider->getRT_Votanti();
        $communication = $ORMmanager->getEntityById(Confxvotanti::class , $commCode);

        //get Payload in order to perform the request
        $payload = $this->RTServicesProvider->getServiceUserPayload($serviceURL, $communication->getEventi());

        $datamine = $this->divoMiner;
        $sections = $datamine->getSectionsFromCommunication($communication); 
         $rxRecords = $this->divoMiner->getAffluenzeFromCommunication($communication);
        //get additional payload to append to the request
         $json_votes = $datamine->getPrecookedAffluenze($communication);
    
        $payload['listaComunicazioneVotantiSezioni'] = $json_votes;

     

        $reply = null;
        try {
            $this->ORMmanager->beginTransaction();
           
            //include array as JSON payload and perform POST call
            $proxyResponse = $this->AppProxyREST->doPOST($serviceURL, $payload);
            $reply = $proxyResponse['json'];
            $key_log = $proxyResponse['key'];
            if ($reply->esito->codice == '1') {
                $event = $communication->getEventi();
                //move the event to the next stage
                $this->moveWfOn($event, StatesService::ENT_EVENT);
                $this->updateFlags($this->RTServicesProvider->getSeedRxAffluenze(), $rxRecords, $key_log);
                $this->updateWf($this->RTServicesProvider->getSeedRxSezioni(), $sections);
            }
            else {
                $this->updateFlags($this->RTServicesProvider->getSeedRxAffluenze(), $rxRecords, $key_log, false);
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
     * Perform the action to send data to RT using provided REST API
     */
    protected function sendDataChangedtoRT( $commCode ) 
    {
        $ORMmanager = $this->ORMmanager;
        //we have to manage the payload creation in order to send first values
        $serviceURL = $this->RTServicesProvider->getRT_Votanti();
        $communication = $ORMmanager->getEntityById(Confxvotanti::class , $commCode);

        //get Payload in order to perform the request
        $payload = $this->RTServicesProvider->getServiceUserPayload($serviceURL, $communication->getEventi());

        $datamine = $this->divoMiner;
        $sections = $datamine->getSectionsFromCommunication($communication); 
      
        $return_array = $datamine->getPrecookedAffluenzeOnlyChanged($communication);
        $json_votes=$return_array['json_votes'];
        $rxRecords=$return_array['rxRecords'];
       
        $payload['listaComunicazioneVotantiSezioni'] = $json_votes;

     

        $reply = null;
        try {
            $this->ORMmanager->beginTransaction();
           
            //include array as JSON payload and perform POST call
            $proxyResponse = $this->AppProxyREST->doPOST($serviceURL, $payload);
            $reply = $proxyResponse['json'];
            $key_log = $proxyResponse['key'];
            if ($reply->esito->codice == '1') {
                $event = $communication->getEventi();
                //move the event to the next stage
                $this->moveWfOn($event, StatesService::ENT_EVENT);
                $this->updateFlags($this->RTServicesProvider->getSeedRxAffluenze(), $rxRecords, $key_log);
                $this->updateWf($this->RTServicesProvider->getSeedRxSezioni(), $sections);
            }
            else {
                $this->updateFlags($this->RTServicesProvider->getSeedRxAffluenze(), $rxRecords, $key_log, false);
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
        $cap = $this->wfService->getCapAffluences($entityType);
        if ($item->getStatoWf() != $cap) {
            $this->wfService->moveNextState($item, $entityType);
            $advanced = true;
        }
        return $advanced;
    }

    /**
     * It performs a massive update for states of wf
     */
    private function updateWf(RTSentableInterface $entityInterface, array $elements) 
    {
        $map = $this->wfService->getMapAffluences();
        $matrix = array();

        foreach($map as $key => $mapItem) {
            $matrix[$key] = array();
        }

        $keys = array();
        foreach($elements as $element) {
            if (isset($matrix[$element->getStatoWf()])) {
                array_push($matrix[$element->getStatoWf()], $element->getId());
            }
        }

        foreach($matrix as $key => $matItem) {
            $parameters = [
                'stato_wf' => $map[$key]->getCode(),
            ];
            $outcomes = $this->ORMmanager->updateAllEntitiesByKeys($entityInterface, $parameters, 'id', $matItem);
        }
    }

    /**
     * 
     * @Route("/divodb/votanti", name="readVotanti")
     */
    public function readVotanti(Request $request) 
    {
        //$start = microtime(true);
        $template = "source/source.quick.affluenze.html.twig";

        $divoMiner = $this->divoMiner;
        $eventCommunications =  $divoMiner->readComunicazioniEventiArray();

        $data = [];
        foreach($eventCommunications as $communication) {
            //retrieve votes for each communication
         //  $data_votes = $divoMiner->getReportAffluenze($communication);
            $configurazioni = json_decode( $communication->getConfigurazioni() );
            $includeAffluenzaMF = $configurazioni->gestioneAffluenzaMF;
          
            $data_votes = $divoMiner->getReportAffluenzeBitnew($communication);
          
            $evento = $communication->getEventi();
            $evento->storeStatoWfDesc($this->wfService);
            array_push( $data, [
                'eventoid' => $evento->getId(),
                'evento' => $evento->getEvento(),
                'eventodesc' => $communication->getEventi()->getDescrizioneEvento(),
                'eventostatus' => $evento->getStatoWfDesc(),
                'comunicazione' => $communication,
                'includeAffluenzaMF' => $includeAffluenzaMF,
                'affluenze' => $data_votes,
            ] ); 

        }

        $template_par = [
            'data' => $data,
        ];

        $reply = $request->get('esito');
        if (isset($reply)) {
            $template_par['communication_esito'] = $reply;          
        }

        return $this->render($template, $template_par );
    }

}



