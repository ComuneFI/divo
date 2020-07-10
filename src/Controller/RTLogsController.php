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
use App\Service\AppProxyRest;

use App\Entity\Actionlogs;
use App\Service\RTServicesProvider;
use App\Service\ORMManager;
use App\Service\RTDivoDataMiner;

/**
 * This is the controller managing navigation of app
 */
class RTLogsController extends DivoController
{

    /**
     * @Route("/divodb/logs", name="readLogs")
     */
    public function readLogs(Request $request) {
        $template = "divo/divo.logs.html.twig";
        $page=$request->get('page');
        $success=$request->get('success');
        $error=$request->get('error');
        $activeFilter=$request->get('activeFilter');
        $filter=null;
       
        if($activeFilter==1){
            if($success!=null & $error==null) $filter="a.codice_esito='1'";
            if($success==null & $error!=null) $filter="a.codice_esito!='1'";
            if($success==null & $error==null) {
                $success='on';
                $error='on';
            }
        }else{
            $success='on';
            $error='on';
        }
 
        if($page==0) $page =1;
        $divoMiner = $this->divoMiner;
      
        $limit = 150;
        $pagination = $divoMiner->getLogsPaginator($page, $limit,$filter);

        # ArrayIterator
        $logs = $pagination->getIterator();
     
        $maxPages = ceil($pagination->count() / $limit);
        if($page>$maxPages){ 
            $page =1;
            $pagination = $divoMiner->getLogsPaginator($page, $limit,$filter);
            $logs = $pagination->getIterator();
        }
      
        $template_par = [
            'logs' => $logs,
            'maxPages' => $maxPages,
            'thisPage' => $page,
            'path'=>'readLogs',
            'success'=>$success,
            'error'=>$error,

         
        ];

        return $this->render($template, $template_par );
    }

    /**
     * @Route("/divodb/logs/{logid}", name="readLog")
     */
    public function readSingleLog(Request $request, $logid) {
        $template = "divo/divo.log.html.twig";
        //get utils objects for our transaction
        $ORMmanager = $this->ORMmanager;
        $log = $ORMmanager->getEntityById( Actionlogs::class, $logid);

        $template_par = [
            'log' => $log,
        ];

        //dump($template_par); exit;
        return $this->render($template, $template_par );
    }


    private function getAllSez($elements) {
        $sez=[];
        foreach($elements as $elem){
            $id=$elem->getRxsezioni()->getId();
            $desc=$elem->getRxsezioni()->getDescrizione();
            $sez[$id]=$desc;
            
        }
        return $sez;
    }

    /**
     * @Route("/divodb/logs/detail/{logid}", name="readDetailRecordLog")
     */
    public function readDetailRecordLog(Request $request, $logid) {
       
        $template = "divo/divo.log.html.twig";
        //get utils objects for our transaction
        $ORMmanager = $this->ORMmanager;
        $serviceProvider=new RTServicesProvider($ORMmanager);
        $log = $ORMmanager->getEntityById( Actionlogs::class, $logid);
        $services=$log->getRequestedws();
        $parameters=['actionlogs_id'=>$logid];
        $order=['id'=>'ASC'];
        $records=[];
        $records['elenco_sezioni']=[];
     
        switch($services){
            case $serviceProvider->getRT_Votanti(): 
        
                $records['votanti'] = $ORMmanager->getAllEntitiesByParameters( $serviceProvider->getSeedRxAffluenze() ,$parameters,$order);
                $records['elenco_sezioni']=$this->getAllSez( $records['votanti']);
                $template = "divo/divo.log.dbrecord.affluenze.html.twig";
                break;

             case $serviceProvider->getRT_Scrutini(): 
                $records['votanti'] = $ORMmanager->getAllEntitiesByParameters( $serviceProvider->getSeedRxAffluenze() ,$parameters,$order);
                $records['elenco_sezioni']=$this->getAllSez( $records['votanti']);
                $records['scrutinicandidato'] = $ORMmanager->getAllEntitiesByParameters( $serviceProvider->getSeedRxScrutini() ,$parameters,$order);
                $records['scrutiniliste'] = $ORMmanager->getAllEntitiesByParameters( $serviceProvider->getSeedRxScrutiniListe() ,$parameters,$order);
                $records['votinonvalidi'] = $ORMmanager->getAllEntitiesByParameters( $serviceProvider->getSeedRxVotiNonValidi() ,$parameters,$order);
                $listaxprincipale = $ORMmanager->getActiveEntityObjects( $serviceProvider->getSeedListaPrincipale() ,[],$order);
                $listaxprincipale_array=[];
                $poscandidato=[];
               
               foreach($listaxprincipale as $lista) {
                   $id_lista=$lista->getListapreferenze()->getId();
                   $candidato=$lista->getCandidatiprincipali()->getNome().' '.$lista->getCandidatiprincipali()->getCognome();
                   $idcandidato=$lista->getCandidatiprincipali()->getId();
                   $posizione=$lista->getPosizione();
                   $listaxprincipale_array[$id_lista]=$candidato;
                   $poscandidato[$idcandidato]=$posizione;
                  
               }
                $records['listaxprincipale']=$listaxprincipale_array;
                $records['poscandidato']=$poscandidato;
                $template = "divo/divo.log.dbrecord.scrutini.html.twig";
                break;
            case $serviceProvider->getRT_Preferenze(): 
                $records['preferenze'] = $ORMmanager->getAllEntitiesByParameters( $serviceProvider->getSeedRxPreferenze() ,$parameters,$order);
                $records['elenco_sezioni']=$this->getAllSez(  $records['preferenze']);    
                $posizione = $ORMmanager->getActiveEntityObjects( $serviceProvider->getSeedSecondarioLista() ,[],$order);
                $posizione_array=[];

                foreach($posizione as $posizioneItem) {
                    $id_lista=$posizioneItem->getListapreferenze()->getId();
                    $id_candidato=$posizioneItem->getCandidatiSecondari()->getId();
                    $posizione=$posizioneItem->getPosizione();
                    $posizione_array[$id_lista][$id_candidato]=$posizione;  
                }
                $records['posizionicandidatosec']=$posizione_array;
              
                $template = "divo/divo.log.dbrecord.preferenze.html.twig";
                
                break;
            default:   $template = "divo/divo.log.html.twig"; break;
        }
        if(count($records['elenco_sezioni'])==0 )$template = "divo/divo.log.html.twig"; 
     
        $template_par = [
            'log' => $log,
            'records' => $records,
        ];

        //dump($template_par); exit;
        return $this->render($template, $template_par );
    }


}



