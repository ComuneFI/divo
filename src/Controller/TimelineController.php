<?php
// src/Controller/DummyController.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
//in order to let possible render templates

use App\Service\StatesService;



/**
 * This is the controller managing navigation of app
 */
class TimelineController extends DivoController
{


    /**
     *  @Route("/timelineEventi", name="timelineEventi")
     */
    public function getTimeLineEventi()
    {
       
        $template = "timeline/timelineevent.html.twig";

     
        $states=$this->wfService->getAllState('Eventi');
   
        $array_states=[];
        foreach($states as $state){
            $array_states[$state->getCode()]['next']=$state->getNextstate();
            $array_states[$state->getCode()]['descr']=$state->getDescr();
        }
       

        $serviceUser = $this->ORMmanager->getServiceUser();
        $ente_id=$serviceUser->getEnti()->getId();
        $param_filter=['ente_id' => $serviceUser->getEnti()->getId()];
        $param_order= [];
        $serviceURLEneteEventi = $this->RTServicesProvider->getSeedEnteEvento();
        $listEnteEventi = $this->ORMmanager->getActiveEntityObjects($serviceURLEneteEventi,$param_filter,$param_order);
      
       
        $template_par = [
            'array_states' => $array_states,
            'start_state'=>'START',
            'end_state'=>'CLOSE',
            'listEnteEventi'=>$listEnteEventi,
            
        ];

        return $this->render($template, $template_par);
    }

     /**
     *  @Route("/timelineRxSezioni", name="timelineRxSezioni")
     */
    public function getTimeLineSez()
    {
        $template = "timeline/timelinesez.html.twig";
        
        $states=$this->wfService->getAllState(StatesService::ENT_SECTION);

        $array_states=[];
        foreach($states as $state){
            $array_states[$state->getCode()]['next']=$state->getNextstate();
            $array_states[$state->getCode()]['descr']=$state->getDescr();
        }

        $serviceUser = $this->ORMmanager->getServiceUser();
        $ente_id=$serviceUser->getEnti()->getId();
        $param_filter=['ente_id' => $serviceUser->getEnti()->getId()];
        $param_order= [];
        $serviceURLEneteEventi = $this->RTServicesProvider->getSeedEnteEvento();
        $listEnteEventi = $this->ORMmanager->getActiveEntityObjects($serviceURLEneteEventi, $param_filter, $param_order);
        $listRxCircoscrizioni_array=[];
        $descEventoArray=[];
        foreach ($listEnteEventi as $singleEvent){
            $event=$singleEvent->getEventi()->getId();
    
            $descEventoArray[$event]=$singleEvent->getEventi()->getEvento();
            $sezioni=$records= $this->divoMiner->getSectionsByEvent($event);
            $sezioni= $sezioni['array'];
            ksort($sezioni);
            $sezioni_array[$event]=$sezioni;
            
        }

        $template_par = [
            'array_states' => $array_states,
            'start_state'=>'READY',
            'end_state'=>'END',
            
            'sezioni'=>$sezioni_array,
            'descEventoArray'=>$descEventoArray,
            
        ];

        return $this->render($template, $template_par);
    }



}



