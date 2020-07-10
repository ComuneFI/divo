<?php
namespace App\Controller;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
//in order to let possible render templates
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Entity\Candidatiprincipali;
use App\Entity\ListaPreferenze;
use App\Entity\Rxsezioni;
use App\Entity\Rxvotanti;
use App\Entity\Rxscrutinicandidati;
use App\Entity\Confxvotanti;
use App\Entity\Candidatisecondari;
use App\Entity\Rxscrutiniliste;
use App\Entity\Rxvotinonvalidi;
use App\Entity\Rxpreferenze;
use App\Service\ORMManager;
use App\Service\RTServicesProvider;




/**
* FunctionsAjax controller.
*
*/

class FunctionsAjaxController extends DivoController{

    /**
     * @Route("/config/saveconfig", name="saveconfig")
     */
    public function saveconfig(Request $request) {
      
        $elementEvent = $this->ORMmanager->getManager()->getRepository('App\Entity\Eventi');
        $tabledb=$request->get('tabledb');
        $map=$request->get('map');
        $nextState=$request->get('nextstate');
   
       
        $response = array(
            'status' => '200',
            'msg' =>'Aggiornamento avvenuto con successo',
        );
      
        try{ 
            foreach($nextState as $keyStatus=>$itemStatus){
                $elementEvent->find($keyStatus)->setStatoWf($itemStatus);
            }
       
            $element = $this->ORMmanager->getManager()->getRepository('App\\Entity\\'.$tabledb);
            
            foreach($map as $key=>$item){
             
              $element->find($key)->setIdSource($item);
              
            }
           
              
            $this->ORMmanager->getManager()->flush();
      
        }catch(\Exception $e) {
            
            $response = array(
                'status' => '500',
                'msg' =>'Si è verificato un errore ' .$e,
            );
            //throw $e;     
         }
        
         return new JsonResponse($response);

    }

    /**
     * @Route("/checkEnabled", name="checkEnabled")
     */
    public function checkEnabled(Request $request) {
        $nextstate=$request->get('nextstate');
        $entityref=$request->get('entityref');
        $results=$this->wfService->getIfExists($nextstate,$entityref);
        return new JsonResponse($results);

    }

    /**
     * @Route("/savesourcedate", name="savesourcedate")
     */
    public function savesourcedate(Request $request) {
     
        $listRxVotanti =  $this->RTServicesProvider->getSeedRxAffluenze(); 
        $listRxScrutiniCandidati = $this->RTServicesProvider->getSeedRxScrutini();
        $listRxScrutiniListe = $this->RTServicesProvider->getSeedRxScrutiniListe();
        $listRxpreferenze= $this->RTServicesProvider->getSeedRxPreferenze();
        $listRxvotinonvalidi= $this->RTServicesProvider->getSeedRxVotiNonValidi();
        $voti_non_validi=false;
        $tabledb=$request->get('tabledb');
        $inputdata=$request->get('inputdata');
        $this->ORMmanager->beginTransaction();
        
        $errore=false;
        $response = array(
            'status' => '200',
            'msg' =>'Aggiornamento avvenuto con successo',
        );
        try{ 
        foreach($inputdata as $record){
            switch($tabledb){
                case 'Rxvotanti':
                    if($record['num_votanti_maschi']!='' || $record['num_votanti_femmine']!='' || $record['num_votanti_totali']!=''){
                        $rxvotanti =new Rxvotanti();
                        $sezione= $this->ORMmanager->getEntityById(Rxsezioni::class,$record['rxsezione_id']);
                        $cofxvotanti= $this->ORMmanager->getEntityById(Confxvotanti::class,$record['confxvotanti_id']);        
                        $rxvotanti->setRxsezioni($sezione);
                        $rxvotanti->setConfxvotanti($cofxvotanti);
                        if($record['num_votanti_maschi']!='')
                            $rxvotanti->setNumVotantiMaschi($record['num_votanti_maschi']);
                        if($record['num_votanti_femmine']!='')
                            $rxvotanti->setNumVotantiFemmine($record['num_votanti_femmine']);
                        if($record['num_votanti_totali']!='')
                        $rxvotanti->setNumVotantiTotali($record['num_votanti_totali']);
                        $rxvotanti->setOff('false');
                        $rxvotanti->setSent('0');
                        $rxvotanti->setTimestamp(new \DateTime("now"));
                        $rxvotanti->setInsDate(new \DateTime("now"));
                        $param=['confxvotanti_id'=>$record['confxvotanti_id'], 'rxsezione_id'=>$record['rxsezione_id']];
                        $this->ORMmanager->setOffAllEntitiesByParameters($listRxVotanti,$param);
                        $this->ORMmanager->insertEntity($rxvotanti);
                           
                       
                    } 
                        break;

                case 'Rxscrutinicandidati':

                    if($record['voti_totale_candidato']!=''){
                        if($record['candidato_principale_id']!=0){
                            $rxscrutinicandidati =new Rxscrutinicandidati();
                            $sezione= $this->ORMmanager->getEntityById(Rxsezioni::class,$record['rxsezione_id']);
                            $candidatoprincipale= $this->ORMmanager->getEntityById(Candidatiprincipali::class,$record['candidato_principale_id']);        
                            $rxscrutinicandidati->setRxsezioni($sezione);
                            $rxscrutinicandidati->setCandidatiprincipali($candidatoprincipale);
                            $rxscrutinicandidati->setVotiTotaleCandidato($record['voti_totale_candidato']);
                            $rxscrutinicandidati->setOff('false');
                            $rxscrutinicandidati->setSent('0');
                            $rxscrutinicandidati->setTimestamp(new \DateTime("now"));
                            $rxscrutinicandidati->setInsDate(new \DateTime("now"));
                            $param=['rxsezione_id'=>$record['rxsezione_id'],'candidato_principale_id'=>$record['candidato_principale_id']];
                            $this->ORMmanager->setOffAllEntitiesByParameters($listRxScrutiniCandidati,$param);
                            $this->ORMmanager->insertEntity($rxscrutinicandidati);
                        }else{
                            if(!$voti_non_validi){
                                $rxvotinonvalidi =new Rxvotinonvalidi();
                                $sezione= $this->ORMmanager->getEntityById(Rxsezioni::class,$record['rxsezione_id']);     
                                $rxvotinonvalidi->setRxsezioni($sezione);
                                $rxvotinonvalidi->setOff('false');
                                $rxvotinonvalidi->setSent('0');
                                $rxvotinonvalidi->setTimestamp(new \DateTime("now"));
                                $rxvotinonvalidi->setInsDate(new \DateTime("now"));
                                $param=['rxsezione_id'=>$record['rxsezione_id']];  
                                $this->ORMmanager->setOffAllEntitiesByParameters($listRxvotinonvalidi,$param);
                                $voti_non_validi=true;
                            }
                            if($record['candidato_principale_nome']=='__BIANCHE__')
                            $rxvotinonvalidi->setNumeroSchedeBianche($record['voti_totale_candidato']);
                            if($record['candidato_principale_nome']=='__NULLE__')
                            $rxvotinonvalidi->setNumeroSchedeNulle($record['voti_totale_candidato']);
                            if($record['candidato_principale_nome']=='__CONTESTATE__')
                            $rxvotinonvalidi->setNumeroSchedeContestate($record['voti_totale_candidato']);
                            
                        }
                           //inserire voti_vuoti
                         }
                       break;
                case'Rxscrutiniliste':
                    if($record['voti_tot_lista']!=''){
                        $rxscrutiniliste =new Rxscrutiniliste();
                        $sezione= $this->ORMmanager->getEntityById(Rxsezioni::class,$record['rxsezione_id']);
                        $listapreferenza= $this->ORMmanager->getEntityById(Listapreferenze::class,$record['lista_preferenze_id']);        
                        $rxscrutiniliste->setRxsezioni($sezione);
                        $rxscrutiniliste->setListapreferenze($listapreferenza);
                        $rxscrutiniliste->setVotiTotLista($record['voti_tot_lista']);
                        $rxscrutiniliste->setOff('false');
                        $rxscrutiniliste->setSent('0');
                        $rxscrutiniliste->setTimestamp(new \DateTime("now"));
                        $rxscrutiniliste->setInsDate(new \DateTime("now"));
                        $param=['rxsezione_id'=>$record['rxsezione_id'],'lista_preferenze_id'=>$record['lista_preferenze_id']];  
                        $this->ORMmanager->setOffAllEntitiesByParameters($listRxScrutiniListe,$param);
                        $this->ORMmanager->insertEntity($rxscrutiniliste);

                    }
                 break;
                case'Rxpreferenze': 
                    if($record['numero_voti']!=''){
                        $rxpreferenze =new Rxpreferenze();  
                        $sezione= $this->ORMmanager->getEntityById(Rxsezioni::class,$record['rxsezione_id']);
                        $listapreferenza= $this->ORMmanager->getEntityById(Listapreferenze::class,$record['lista_preferenze_id']);        
                        $candidatisecondari= $this->ORMmanager->getEntityById(Candidatisecondari::class,$record['candidato_secondario_id']);        
                        $rxpreferenze->setRxsezioni($sezione);
                        $rxpreferenze->setListapreferenze($listapreferenza);
                        $rxpreferenze->setCandidatisecondari($candidatisecondari);
                        $rxpreferenze->setNumeroVoti($record['numero_voti']);
                        $rxpreferenze->setOff('false');
                        $rxpreferenze->setSent('0');
                        $rxpreferenze->setTimestamp(new \DateTime("now"));
                        $rxpreferenze->setInsDate(new \DateTime("now"));
                        $param=['rxsezione_id'=>$record['rxsezione_id'],
                        'candidato_secondario_id'=>$record['candidato_secondario_id'],
                        'listapreferenze_id'=>$record['lista_preferenze_id']
                         ];  
                        $this->ORMmanager->setOffAllEntitiesByParameters($listRxpreferenze,$param);
                        $this->ORMmanager->insertEntity($rxpreferenze);
                      
                    }
               
                   
                    break;
                case'Rxvotinonvalidi': 
                    if($record['numero_schede_bianche']!='' || $record['numero_schede_nulle']!='' || $record['numero_schede_contestate']!=''){
                        $rxvotinonvalidi =new Rxvotinonvalidi();
                        $sezione= $this->ORMmanager->getEntityById(Rxsezioni::class,$record['rxsezione_id']);     
                        $rxvotinonvalidi->setRxsezioni($sezione);
                        $rxvotinonvalidi->setNumeroSchedeBianche($record['numero_schede_bianche']);
                        $rxvotinonvalidi->setNumeroSchedeNulle($record['numero_schede_nulle']);
                        $rxvotinonvalidi->setNumeroSchedeContestate($record['numero_schede_contestate']);
                        $rxvotinonvalidi->setOff('false');
                        $rxvotinonvalidi->setSent('0');
                        $rxvotinonvalidi->setTimestamp(new \DateTime("now"));
                        $rxvotinonvalidi->setInsDate(new \DateTime("now"));
                        $param=['rxsezione_id'=>$record['rxsezione_id']];  
                        $this->ORMmanager->setOffAllEntitiesByParameters($listRxvotinonvalidi,$param);
                        $this->ORMmanager->insertEntity($rxvotinonvalidi);
                   
                       
                    }
                break;
            }
        }
        if($voti_non_validi){ $this->ORMmanager->insertEntity($rxvotinonvalidi); }
        }catch (\Throwable $t) {
                                    
            $this->ORMmanager->rollback();
            $errore=true;
         
            $response = array(
                'status' => '500',
                'msg' =>'Si è verificato un errore ' .$t,
            );
          
        }
        
        if(!$errore) $this->ORMmanager->commit();
   
        return new JsonResponse($response);

    }

}