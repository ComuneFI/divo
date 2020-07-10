<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\StatesServiceWorkflow;
use App\Service\StatesService;
use App\Service\ServiceProvider;
use App\Service\ORMmanager;
use App\Service\RTServicesProvider;
use App\Entity\Eventi;
class TestStatoController  extends DivoController {

    /**
     * Matches / exactly
     *
     * @Route("/testStato", name="testStato")
     */
    public function testStato() {
        
     
       $serviceUser = $this->ORMmanager->getServiceUser();
       
       $stateService=new StatesService($this->ORMmanager, $this->RTServicesProvider);
       
       $ente_id=$serviceUser->getEnti()->getId();
       $param_filter=['ente_id' => $serviceUser->getEnti()->getId()];
       $param_order= [];
       //test funzionamento per Eventi
       $serviceURLEneteEventi = $this->RTServicesProvider->getSeedEnteEvento();
       $listEnteEventi = $this->ORMmanager->getActiveEntityObjects($serviceURLEneteEventi,$param_filter,$param_order);
       foreach($listEnteEventi as $entxevent){
            echo 'Test per Evento <br>';
            $event= $entxevent->getEventi();
            $actual_state=$stateService->getActualState($event);
            echo 'Stato attuale: '.$actual_state.'<br>';
            $valuenext=$stateService->getNextState($event,'Eventi');
            echo 'Stato successivo: '.$valuenext.'<br>';
       
            $exist=$stateService->getIfExists($valuenext,'Eventi'); 
            echo 'esiste almeno uno '.$exist.'<br>';exit();
            $exist=$stateService->getIfExists('dsada','Eventi');
            echo 'esiste almeno uno '.$exist.'<br>';
            $updated=$stateService->updateState($event,'Eventi',$valuenext,false);
            if($updated==1){
              echo 'Aggiornamento a stato successivo avvenuto con successo<br>';
              $this->ORMmanager->getManager()->flush();
            }else{
              echo 'Errore aggiornamento<br>';
            }
            echo 'stato attuale'. $stateService->getActualStateDesc($event,'Eventi').'<br>';
            $actual_state=$stateService->getActualState($event);
            echo 'Stato attuale: '.$actual_state.'<br>';
        

           
       }

       //test funzionamento per RxSezioni

       $serviceURLRxSez = $this->RTServicesProvider->getSeedRxSezioni();
     
       $listRxSezioni = $this->ORMmanager->getActiveEntityObjects($serviceURLRxSez,[],$param_order);
      
       foreach($listRxSezioni as $rxsezioneitem){
         
           $actual_state=$stateService->getActualState($rxsezioneitem);
           if($actual_state!=''){
              echo 'Test per Sezione <br>';
              echo 'Stato attuale: '.$actual_state.'<br>';
              $valuenext=$stateService->getNextState($rxsezioneitem,'RxSezioni');
              echo 'Stato successivo: '.$valuenext.'<br>';
              $updated=$stateService->updateState($rxsezioneitem,'RxSezioni',$valuenext,false);
              $exist=$stateService->getIfExists($valuenext,'RxSezioni');
              echo 'esiste almeno uno '.$exist.'<br>';
              //$exist=$stateService->getIfExists('dsada','RxSezioni');
              echo 'esiste almeno uno '.$exist.'<br>';
              if($updated==1){
                echo 'Aggiornamento a stato successivo avvenuto con successo<br>';
                $this->ORMmanager->getManager()->flush();
              }
              if($updated==-1){
                echo 'Errore aggiornamento grant non presenti<br>';
              }
              if($updated==2){
                echo 'Enabled a false è possibile forzare l\'esecuzione<br>';
              }
              $actual_state=$stateService->getActualState($rxsezioneitem);
              echo 'Stato attuale: '.$actual_state.'<br>';
           }

           
       }

       exit();
    }

}
