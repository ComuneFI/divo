<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use \Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ORMManager;
use App\Service\AppProxyRest;
use App\Service\RTDivoDataMiner;
use App\Service\RTServicesProvider;
use App\Service\StatesService;

use App\Entity\RxSezioni;

class DivoController extends AbstractController {

    protected $ORMmanager;
    protected $AppProxyREST;
    protected $divoMiner;
    protected $RTServicesProvider;

    public function __construct(ORMManager $manager, RTServicesProvider $RTServicesProvider, AppProxyREST $AppProxyREST, RTDivoDataMiner $divoMiner, StatesService $wfService) {
        $this->ORMmanager = $manager;
        $this->RTServicesProvider = $RTServicesProvider;
        $this->AppProxyREST = $AppProxyREST;
        $this->divoMiner = $divoMiner;
        $this->wfService = $wfService;
    }

    /**
     * Set for the given array flag sent =1 and track it with related action log record.
     * This is an utility to be used when rx* records are already sent
     */
    protected function setArraySent(array &$array, $key_log) 
    {
        foreach ($array as $item) {
            $item->setActionlogsId($key_log);
            $item->setSent(1);
        }
    }

    /**
     * Set for the given array track it with related action log record.
     * This is an utility to be used when rx* records received an error during the transmission
     */
    protected function setArrayLog(array &$array, $key_log) 
    {
        foreach ($array as $item) {
            $item->setActionlogsId($key_log);
        }
    }

    /**
     * It checks validity of given range data of sections.
     * In case of some error return a not null variable
     */
    protected function validityCheck($startSec, $finalSec) 
    {
        $errorMessage = null;
        if (is_numeric($startSec) && is_numeric($finalSec)) {
            if ($finalSec-$startSec < 0) {
                $errorMessage = 'Deve essere selezionata una sezione Finale più grande della sezione Iniziale';
            }
        }
        else {
            $errorMessage = 'Devono essere selezionate due sezioni valide';
        }
        return $errorMessage;
    }

    /**
     * Given an initial key value and a final key value, it return the array of inner key values, including also given keys.
     * It supposes that section keys are sequencial and it will not work in case of different order og their primary keys.
     */
    protected function getArraySequence($startSec, $finalSec): array 
    {
        $results = $this->divoMiner->getSectionsInterval($startSec, $finalSec);
        $array = array();
        foreach ($results as $result) {
            array_push($array, [
               'id' => $result['id'],
               'desc' => $result['descrizione']
            ]);
        }
        return $array;
    }


    /**
     * It performs a controlled request to the send data method.
     * In case of exception or fatal error it returns a code equal to 0.
     */
    protected function sendDatatoRTWrapped($sectionCode)
    {
        $errorReply = $this->getEsitoMessage(0,'Divo: Invio dei dati non possibile');
        try {
            $reply = $this->sendDatatoRT($sectionCode);
        }
        catch(\Exception $e) {
            $reply = $errorReply;
        }
        catch(\Throwable $t) {
            $reply = $errorReply;
            //we really need yet this rollback? In case of not active transaction throws an error
            //$this->ORMmanager->rollback(); 
        }
        return $reply;
    }

    /**
     * Orchestrate the multiple delivery of request to the target.
     */
    protected function deliverMultipleSections($errorMessage, array $arraySequence) 
    {
        //set execution time limit for this script as 6 minutes
        set_time_limit(360);
        //set memory limit for this script to 256 MB
        ini_set('memory_limit','256M');
        
        $reply = null;
        if (isset($errorMessage)) {
            $reply =  $this->getEsitoMessage(405,$errorMessage);
        }
        else {
            $counter = sizeof($arraySequence);
            $wellDone = 0;
            $errors = '<ul>';
            foreach($arraySequence as $sequence) {
                $replyItem = $this->sendDatatoRTWrapped($sequence);            
                if ($replyItem->esito->codice == 1) {
                    $wellDone++;
                }
                else {
                    $sectionObj = $this->ORMmanager->getEntityById(Rxsezioni::class, $sequence);
                    if (isset($sectionObj)) {
                        $errors = $errors.'<li>'.$sectionObj->getDescrizione().': '.$replyItem->esito->descrizione.'</li>';
                    }
                    else {
                        $errors = $errors.'<li>'.$replyItem->esito->descrizione.'</li>';
                    }
                }
                $sequence = null;
                unset($sequence);
                $sectionObj = null;
                unset($sectionObj);
                $replyItem = null;
                unset($replyItem);
                gc_collect_cycles();
            }
            $errors = $errors.'</ul>';
            if( $wellDone == $counter ) {
                $reply = $this->getEsitoMessage(1, 'Dati inviati correttamente! ('.$wellDone.'/'.$counter.' sezioni)');
            }
            else {
                $reply = $this->getEsitoMessage(99, ''.$counter-$wellDone.'/'.$counter.' sezioni non inviate:<br>'.$errors);
            }
        }
        return $reply;
    }

    /**
     * Let possible to make a message readable by other twig and controllers
     */
    protected function getEsitoMessage($codice, $descrizione) 
    {
        $reply = new \stdClass();
        $esito = new \stdClass();
        $esito->codice = $codice;
        $esito->descrizione = $descrizione;
        $reply->esito = $esito;
        return $reply;
    }

    protected function sendDatatoRT($sectionCode) {
        return $this->getEsitoMessage(405, 'Override sendDataRT() into your Controller');
    }


        /**
     * It's responsible to return an array of keys belonging to the given interval
     * 
     * @Route("{eventid}/sezioni/range", name="getRange")
     */
    public function getArrayRange(Request $request)
    {
        //get starting and final section values provided for the range 
        $startSec=$request->get('start');
        $finalSec=$request->get('end');
        //TODO: check the interval
        $errorMessage = $this->validityCheck($startSec, $finalSec);
        $esito = null;
        $targetArray = [];
        if ( isset($errorMessage) ) {
            $esito = $this->getEsitoMessage(405,$errorMessage);
        }
        else {
            $esito = $this->getEsitoMessage(1,'');
            $targetArray = $this->getArraySequence($startSec, $finalSec);
        }

        $response = [
            'esito' => $esito,
            'array' => $targetArray,
        ];

        return new JsonResponse($response);
    }

}
