<?php

// src/Controller/DummyController.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Exception\ValidatorException;
use App\Entity\FileCSV;
use App\Entity\Rxcandidati;
use App\Entity\Rxliste;
use App\Entity\Rxcandidatisecondari;
use App\Entity\Rxsezioni;
use App\Entity\Confxvotanti;
use App\Entity\Rxvotanti;
use App\Entity\Candidatiprincipali;
use App\Entity\Candidatisecondari;
use App\Entity\Listapreferenze;
use App\Entity\Rxscrutinicandidati;
use App\Entity\Rxscrutiniliste;
use App\Entity\Rxvotinonvalidi;
use App\Entity\Rxpreferenze;
use App\Service\RTServicesProvider;
use App\Service\ReportService;
use App\Service\RTDivoDataMiner;
use App\Form\FileCSVType;

/**
 * This is the controller managing navigation of app
 */
class UploadDataByCsv extends DivoController {
    

  
    /**
     * HomePage UploadDataByCsv
     * @Route("UploadDataByCsv", name="UploadDataByCsv")
     */
    function homepage(Request $request, ReportService $ReportService){

        $serviceUser = $this->ORMmanager->getServiceUser();   
        $eventLinks = $this->divoMiner->getEventsLinksByEnte( $serviceUser );
        $template = "UploadDataByCsv/sceltaEvento.html.twig";
        $num_eventi=count($eventLinks);
        
        $template_par = [
            'eventi' =>  $eventLinks,
            'num_eventi' =>  $num_eventi,
            
        ];
  
        return $this->render($template, $template_par);
    
    }
  
  /**
     * 
     * @Route("UploadDataByCsv/Event/{event}", name="UploadDataByCsv/Event")
     */
    function index($event,ReportService $ReportService){

        $records= $this->divoMiner->getSectionsByEvent($event);
     
        $circo_id=$records['circo_id'];
        $records= $records['array'];
       
       
        ksort($records);
        $template = "UploadDataByCsv/event.html.twig";
        $num_sez=count($records);
       
        $param_filter=['id'=>$event];
        $serviceURLEventi = $this->RTServicesProvider->getSeedEventi();
        $detailEvent = $this->ORMmanager->getActiveEntityObjects($serviceURLEventi,$param_filter,[]);
        $detailEvent=$detailEvent[0];
  
        $confxvotantiList = $detailEvent->getConfxvotantis();
        $comm=[];
        foreach($confxvotantiList as $com){
            $comm[$com->getId()]=$com;
        }
        ksort($comm);
  
        $configuration_status=$this->divoMiner->getConfStatus();
 

        $template_par = [
            'rxsezioni' => $records,
            'circo_id' => $circo_id,
            'num_sez' => $num_sez,
            'evento' => $detailEvent,
            'confxvotantiList' => $comm,
            'config_status'=>$configuration_status
            
        ];
  
        return $this->render($template, $template_par);
    
    }

    private function formCSV(Request $request){
     $filecsv = new FileCSV();
     $form = $this->createForm(FileCSVType::class, $filecsv);
 
     $form->handleRequest($request);
     $arrayass=[];
    
     if ($form->isSubmitted() && $form->isValid() ) {
 
         $filecsvFile =  $form->get('filecsv')->getData();
    
         if ($filecsvFile) {
     
              try {
                $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
                // decoding CSV contents
                $data = $serializer->decode(file_get_contents($filecsvFile->getPathname()), 'csv', array(CsvEncoder::DELIMITER_KEY => ';'));
                $arrayass=$data;
   
                
             } catch (FileException $e) {
                 // ... handle exception if something happens during file upload
             }
         }
     }

     $returnObj['form']=$form->createView();
     $returnObj['arrayass']=$arrayass;
   
     return $returnObj;
    }


    /**
     * 
     * @Route("uploadCSVRxSezioni/{event}", name="uploadCSVRxSezioni")
     */
    function uploadRxSezioni($event, Request $request, ReportService $ReportService){

    
        $records= $ReportService->reportForCSVRxsezioniByEvent($event);
        $circoscrizioniValide= $ReportService->getCircoscrizioniByEvent($event);
        $template = "UploadDataByCsv/rxsezioni.html.twig";
        $arrayCirc=[];
        $formObj=$this->formCSV($request);
        foreach($circoscrizioniValide as $circ){
            $arrayCirc[$circ['circo_id']]=$circ['circ_desc'];
            $circoscrizione_valida=$circ['circo_id'];
        }

        //save uploaded csv
        $checkArrayValid=true;
        $commit=false;
        $deleted_record=[];
        $errorDelete=false;
        $listID=array();
        try{
            $this->ORMmanager->beginTransaction();
    
            if(count($formObj['arrayass'])>1){
                $formObj['arrayass'] = isset($formObj['arrayass'][0]) ? $formObj['arrayass'] : array($formObj['arrayass']);
                $listRxCircoscrizioniServ = $this->RTServicesProvider->getSeedCircoscrizioni();
                $listRxSezioniServ = $this->RTServicesProvider->getSeedRxsezioni();

                foreach($formObj['arrayass'] as $record){
             
                    $rxsezione =new Rxsezioni();
                   
                    if( !isset($record['Numero']) or 
                        !isset($record['Descrizione'])or
                        array_search($record['Numero'],$listID)!==false
                        ){
                        $checkArrayValid=false;
                       
                        break;
                    }else{
                        array_push($listID, $record['Numero']);
                        $param_filter=['id' => $circoscrizione_valida];
                       
                        $param_order= [];
                        $listRxCircoscrizioni = $this->ORMmanager->getActiveEntityObjects($listRxCircoscrizioniServ,$param_filter,$param_order);
                        $rxsezione->setCircoscrizioni($listRxCircoscrizioni[0]);
                        $rxsezione->setNumero($record['Numero']);
                        $rxsezione->setDescrizione($record['Descrizione']);
                    
                        if(!isset($deleted_record[$circoscrizione_valida])){
                            $param_delete_array=['circo_id'=>$circoscrizione_valida];

                            try{
                            
                                $this->ORMmanager->deleteAllEntitiesByParameters($listRxSezioniServ,$param_delete_array);
                             }catch (\Throwable $t) {
                         
                                $errorDelete=true;
                                break;
                                
                            }
                            $deleted_record[$circoscrizione_valida]=true;
                        }
                      
                        $this->ORMmanager->insertEntity($rxsezione);
                      
                    }
                   
                }
  
                if($checkArrayValid && !$errorDelete){
                     $this->ORMmanager->commit();
                     $commit=true;
                     $formObj['arrayass']='';
                     $records= $ReportService->reportForCSVRxsezioniByEvent($event);
 
                 }else{
                     $this->ORMmanager->rollback();
                 }

            }
          
     
    
        }catch (\Throwable $t) {
            $this->ORMmanager->rollback();
            throw $t;
        }

        //end save uploaded csv

        $template_par = [
            'evento_id' => $event,
            'records' => $records,
            'form' => $formObj['form'],
            'errorDelete' => $errorDelete,
            'checkArrayValid' => $checkArrayValid,
            'commit' => $commit
        ];
  
        return $this->render($template, $template_par);
    }
    
     /**
     * 
     * @Route("uploadCSVAffluenza/{event}/{idComm}/{sezione_sel}", name="uploadCSVAffluenza")
     */
  
    function uploadAffluenze( $event, $idComm,$sezione_sel, Request $request, ReportService $ReportService){
        $serviceUser = $this->ORMmanager->getServiceUser();
        $records= $ReportService->reportForCSVAffluenze($serviceUser->getEnti()->getId(),$idComm,$sezione_sel);
        $listRxVotanti = $this->RTServicesProvider->getSeedRxAffluenze();
        $template = "UploadDataByCsv/rxvotanti.html.twig";
        $formObj=$this->formCSV($request);
        //save uploaded csv
        $checkArrayValid=true;
        $commit=false;
        $deleted_record=[];
        $errorDelete=false;
         try{
             $this->ORMmanager->beginTransaction();
     
             if(count($formObj['arrayass'])>1){
             
                $formObj['arrayass'] = isset($formObj['arrayass'][0]) ? $formObj['arrayass'] : array($formObj['arrayass']);
                 $listRxCircoscrizioniServ = $this->RTServicesProvider->getSeedCircoscrizioni();
                 foreach($formObj['arrayass'] as $record){
              
                    
                     
                     if(
                         !isset($record['evento_id']) or !isset($record['evento']) or 
                         !isset($record['circo_id']) or !isset($record['circ_desc']) or 
                         !isset($record['rxsezione_id']) or !isset($record['descrizione']) or !isset($record['numero']) or $record['rxsezione_id']!==$sezione_sel or
                         !isset($record['confxvotanti_id']) or !isset($record['comunicazione_desc']) or $record['confxvotanti_id']!=$idComm or
                         !isset($record['Voti Maschi']) or !isset($record['Voti Femmine']) or !isset($record['Voti Totali']) 
                         ){
                         $checkArrayValid=false;
                         break;
                     }else{
                        $rxvotanti =new Rxvotanti();
                        if(!isset($deleted_record[$record['confxvotanti_id'].'_'.$record['rxsezione_id']])){
                 
                            $confxvotanti_id=$record['confxvotanti_id'];
                            $rxsezione_id=$record['rxsezione_id'];
                            $param_delete_array=['confxvotanti_id'=>$confxvotanti_id,'rxsezione_id'=>$rxsezione_id];    
                            try{
                                $this->ORMmanager->setOffAllEntitiesByParameters($listRxVotanti,$param_delete_array);
                             }catch (\Throwable $t) {
                            
                                $errorDelete=true;
                                break;
                                
                            }
                            $deleted_record[$confxvotanti_id]=true;
                        }
                         $sezione= $this->ORMmanager->getEntityById(Rxsezioni::class,$record['rxsezione_id']);
                         $cofxvotanti= $this->ORMmanager->getEntityById(Confxvotanti::class,$record['confxvotanti_id']);        
                         $rxvotanti->setRxsezioni($sezione);
                         $rxvotanti->setConfxvotanti($cofxvotanti);
                         if($record['Voti Maschi']!='')
                         $rxvotanti->setNumVotantiMaschi($record['Voti Maschi']);
                         if($record['Voti Femmine']!='')
                         $rxvotanti->setNumVotantiFemmine($record['Voti Femmine']);
                         if($record['Voti Totali']!='')
                         $rxvotanti->setNumVotantiTotali($record['Voti Totali']);
                         $rxvotanti->setOff('false');
                         $rxvotanti->setSent('0');
                         $rxvotanti->setTimestamp(new \DateTime("now"));
                         $rxvotanti->setInsDate(new \DateTime("now"));
          
                         $this->ORMmanager->insertEntity($rxvotanti);
                       
                       
                     }
                    
                 }
                 if($checkArrayValid && !$errorDelete){
                      $this->ORMmanager->commit();
                      $commit=true;
                      $formObj['arrayass']='';
                      $records= $ReportService->reportForCSVAffluenze($serviceUser->getEnti()->getId(),$idComm,$sezione_sel);
                  }else{
                      $this->ORMmanager->rollback();
                  }
 
             }
           
      
     
         }catch (\Throwable $t) {
             $this->ORMmanager->rollback();
             throw $t;
         }
         //end save uploaded csv
   
        $template_par = [
            'records' => $records,
            'evento_id' => $event,
            'idsezione' => $sezione_sel,
            'form' => $formObj['form'],
            'errorDelete' => $errorDelete,
            'checkArrayValid' => $checkArrayValid,
            'commit' => $commit
        ];
        return $this->render($template, $template_par);
    
    }

    /**
     * Load all preferences
     * 
     * @Route("CSV/{event}/preferenze/upload", name="CSVUploadPreferenze")
     */
    function uploadAllPreferencesByCSV(Request $request, ReportService $ReportService, $event){
        //setup for elaboration a time limit of 300 seconds
        set_time_limit(300);
        $schema = getenv('BICORE_SCHEMA');
        //in order to boost the insertion of many records
        $em = $this->ORMmanager->getManager(); 
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        //where elaboration has to landing after done
        $template = "UploadDataByCsv/preferenze-all.html.twig";
              
        $formObj=$this->formCSV($request);
        //save uploaded csv

        $checkArrayValid=true;
        $commit=false;
        $genericError=false;
        $batchSize = 100;

        try{
            $numItems = count($formObj['arrayass']);
            if($numItems>1){
                $formObj['arrayass'] = isset($formObj['arrayass'][0]) ? $formObj['arrayass'] : array($formObj['arrayass']);

                //Check validity of arrays, when first invalid found it aroses an exception
                if (!$this->checkValidity($formObj['arrayass'], ['Id Candidato','Id Lista Preferenze','Sezione'])) {
                    throw new ValidatorException("Invalid loaded CSV file");
                }
                
                //Upload the map of sections (number, RxSection)
                $resultSections=$this->divoMiner->getSectionsByEvent($event);
                $sectionMap=$resultSections['array'];
                $listOfIds=$resultSections['listOfId'];
                //Upload the map of lists
                $listMap = $this->divoMiner->getMappingListsByEvent($event);
                //Upload the map of candidates
                $listCandidates = $this->divoMiner->readCandidatiSecondariByEvent($event);
                $candidateMap = array();
                foreach($listCandidates as $candidate) {
                    $candidateMap[$candidate->getIdSource()] = $candidate;
                }

                $listRxPreferences = $this->RTServicesProvider->getSeedRxPreferenze();
                //Invalidate already existent preferences
                $this->ORMmanager->setOffAllEntitiesByParametersIN($listRxPreferences,['rxsezione_id'=>$listOfIds]);

               //preparing queries
                $sqlInsert = "INSERT INTO ".$schema.".rxpreferenze 
                (id, rxsezione_id, listapreferenze_id , candidato_secondario_id , off, timestamp, sent, numero_voti, discr, ins_date )
                VALUES ";
                $sqlValues = '';

                //preparing controllers
                $i = 0;
                $insert = 0;
                $skipLine = false;
                foreach($formObj['arrayass'] as $row) {  
                    $skipLine = false;
                    if( $this->isColumnUnset('Voti', $row) ) {
                        //we skip this line and proceed with next one
                        $i++;
                        $skipLine = true;
                    }
                    else {
                        if( $this->isColumnUnset('Timestamp', $row) ) {
                            $row['Timestamp']= 'NOW()';
                        }
                        $sqlValues = $sqlValues."
                        (nextval('".$schema.".rxpreferenze_id_seq'), ".$sectionMap[$row['Sezione']]->getId().",
                         ".$listMap[$row['Id Lista Preferenze']]->getId().", ".$candidateMap[$row['Id Candidato']]->getId().", 
                         false, '".$row['Timestamp']."', 0, ".$row['Voti'].",'extended', NOW()),";            
                        $i++;
                        $insert++;
                    }
                    if (($insert % $batchSize) === 0 && $insert > 0) {
                        $this->writeData($em, $sqlInsert, $sqlValues);
                        $sqlValues = '';
                    }
                }
                //are there some other records lesser than batchsize?
                if( ($insert% $batchSize) > 0 )  {
                    $this->writeData($em, $sqlInsert, $sqlValues);
                }
                $em->flush(); //Persist objects that did not make up an entire batch
                $em->clear();
                $commit = true;
            } 

        }
        catch (ValidatorException $e) {
            $checkArrayValid = false;
        }
        catch (\Throwable $t) {
             $genericError = true;
             throw $t;
        }
         //end uploaded csv
        $template_par = [
            'evento_id' => $event,
            'form' => $formObj['form'],
            'genericError' => $genericError,
            'checkArrayValid' => $checkArrayValid,
            'commit' => $commit
        ];
        return $this->render($template, $template_par);
    }

    /**
     * Load all preferences
     * 
     * @Route("CSV/{event}/liste/upload", name="CSVUploadLists")
     */
    function UploadListeAll(Request $request, ReportService $ReportService, $event){
        set_time_limit(300); 
        $schema = getenv('BICORE_SCHEMA');

        $invalidKeys = ['__BIANCHE__','__CONTESTATE__','__NULLE__', '__VALIDI_PRESIDENTE__'];
        //in order to boost the insertion of many records
        
        $em = $this->ORMmanager->getManager(); 
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        //where elaboration has to landing after done
        $template = "UploadDataByCsv/liste-all.html.twig";
              
        $formObj=$this->formCSV($request);
         //save uploaded csv

         $checkArrayValid=true;
         $commit=false;
         $deleted_record=[];
         $errorDelete=false;
         $batchSize = 100;

        try{
            if(count($formObj['arrayass'])>1){
                $formObj['arrayass'] = isset($formObj['arrayass'][0]) ? $formObj['arrayass'] : array($formObj['arrayass']);

                //Check validity of arrays, when first invalid found it aroses an exception
                if (!$this->checkValidity($formObj['arrayass'], ['Id Lista','Sezione'])) {
                    throw new ValidatorException("Invalid loaded CSV file");
                }

                //Upload the map of sections (number, RxSection)
                $resultsSection=$this->divoMiner->getSectionsByEvent($event);
                $sectionMap=$resultsSection['array'];
                $listOfIds=$resultsSection['listOfId'];
                //Upload the map of candidates
                //$listCandidates = $this->divoMiner->getAllMainCandidatesByEvent($event);
                $listByEvent = $this->divoMiner->getMappingListsByEvent($event);
                $listMap = array();
                foreach($listByEvent as $list) {
                    $listMap[$list->getIdSource()] = $list;
                }

                //read seeds
                $RxPoolsListe = $this->RTServicesProvider->getSeedRxScrutiniListe();
                $RxNotValidPools = $this->RTServicesProvider->getSeedRxVotiNonValidi();
                //Invalidate already existent preferences
                try{
                    $this->ORMmanager->setOffAllEntitiesByParametersIN($RxPoolsListe,['rxsezione_id'=>$listOfIds]);
                    $this->ORMmanager->setOffAllEntitiesByParametersIN($RxNotValidPools,['rxsezione_id'=>$listOfIds]);
                }catch (\Throwable $t) {
                    $errorDelete=true;
                }  

               //Divide into 2 different groups
               $array_lists = array();
               $array_votes = array();
               $this->divideByType($formObj['arrayass'], $array_lists, $array_votes, $invalidKeys, 'Lista Preferenze', 'Voti');

               //$numItems_cand = count($array_lists);

                //preparing INSERT query parts
                $sqlInsert = array();
                $sqlInsert['pools'] = "INSERT INTO ".$schema.".rxscrutiniliste 
                (id, rxsezione_id, lista_preferenze_id  , off, timestamp, sent, voti_tot_lista, discr, ins_date )
                VALUES ";
                $sqlInsert['invalid'] = "INSERT INTO ".$schema.".rxvotinonvalidi 
                (id, rxsezione_id, numero_schede_bianche, numero_schede_nulle, numero_schede_contestate, tot_voti_dicui_solo_candidato, off, timestamp, sent, discr, ins_date )
                VALUES ";
                //preparing VALUES query parts
                $sqlValues = array();
                $sqlValues['pools'] = '';
                $sqlValues['invalid'] = '';

                //preparing controllers
                //DEAL LISTS POOLS
                $i = 0;
                $insert = 0;

                foreach($array_lists as $row) {  
                    $sqlValues['pools'] = $sqlValues['pools']."
                        (nextval('".$schema.".rxscrutiniliste_id_seq'), ".$sectionMap[$row['Sezione']]->getId().", 
                        ".$listMap[$row['Id Lista']]->getId().", 
                         false, '".$row['Timestamp']."', 0, ".$row['Voti'].",'extended', NOW()),";            
                    $i++;
                    $insert++;
                    if (($insert % $batchSize) === 0 && $insert > 0) {
                        $this->writeData($em, $sqlInsert['pools'], $sqlValues['pools'] );
                        $sqlValues['pools'] = '';
                    }
                }
                //are there some other records lesser than batchsize?
                if( ($insert% $batchSize) > 0 )  {
                    $this->writeData($em, $sqlInsert['pools'], $sqlValues['pools'] );
                }
                $em->flush(); //Persist objects that did not make up an entire batch
                $em->clear();
                $commit = true;
         
                //prepare data for insertion
                //reducing records by 4
                $reduced_array = array();
                $this->projectIntoSections($array_votes, $reduced_array, $invalidKeys);

                //DEAL NOT VALID POOLS
                //$numItems_votes = count($reduced_array);
                //numero_schede_bianche, numero_schede_nulle, numero_schede_contestate, voti candidato singolo
                $i = 0;
                $insert = 0;
                foreach($reduced_array as $row) {  
                    foreach($invalidKeys as $key) {
                        if (!isset($row[$key])) {
                            $row[$key] = 'null';
                        }
                    }
                    $sqlValues['invalid'] = $sqlValues['invalid']."
                        (nextval('".$schema.".rxvotinonvalidi_id_seq'),".$sectionMap[$row['Sezione']]->getId()."
                        ,".$row['__BIANCHE__']."
                        ,".$row['__NULLE__']."
                        ,".$row['__CONTESTATE__']."
                        ,".$row['__VALIDI_PRESIDENTE__']."
                        ,false, '".$row['Timestamp']."', 0, 'extended', NOW()),";            
                    $i++;
                    $insert++;
                    if (($insert % $batchSize) === 0 && $insert > 0) {
                        $this->writeData($em, $sqlInsert['invalid'], $sqlValues['invalid'] );
                        $sqlValues['invalid'] = '';
                    }
                }
                //are there some other records lesser than batchsize?
                if( ($insert% $batchSize) > 0 )  {
                    $this->writeData($em, $sqlInsert['invalid'], $sqlValues['invalid'] );
                }
                $em->flush(); //Persist objects that did not make up an entire batch
                $em->clear();
                $commit = true;
            } 

        }
        /** */
        catch (ValidatorException $e) {
            $checkArrayValid = false;
        }
        catch (\Throwable $t) {
            $commit = false;
            throw $t;
         }
         //end uploaded csv
        $template_par = [
            'form' => $formObj['form'],
            'evento_id' => $event,
            'errorDelete' => $errorDelete,
            'checkArrayValid' => $checkArrayValid,
            'commit' => $commit
        ];
        return $this->render($template, $template_par);
    }

     /**
     * 
     * @Route("uploadCSVAffluenzaAll/{event}/{idComm}", name="uploadCSVAffluenzaAll")
     */
  
    function uploadAffluenzeAll(  $event,$idComm, Request $request, ReportService $ReportService){

        $serviceUser = $this->ORMmanager->getServiceUser();
        $records= $ReportService->reportForCSVAffluenzeAll($event,$idComm);
        $listRxVotanti = $this->RTServicesProvider->getSeedRxAffluenze();
        $template = "UploadDataByCsv/rxvotantiAll.html.twig";
        $formObj=$this->formCSV($request);
        //save uploaded csv
        $checkArrayValid=true;
        $commit=false;
        $deleted_record=[];
        $errorDelete=false;
       
         try{
             $this->ORMmanager->beginTransaction();
     
             if(count($formObj['arrayass'])>1){
            
                $formObj['arrayass'] = isset($formObj['arrayass'][0]) ? $formObj['arrayass'] : array($formObj['arrayass']);
              
                $obj=$this->divoMiner->getSectionsByEvent($event);
            
                $listSections=$obj['array'];
                $listConfVot=array();
                 foreach($formObj['arrayass'] as $record){
                  
                    if(
                            !isset($record['Sezione']) or
                            !isset($record['Confxvotanti_id']) or
                            !isset($record['Voti Maschi']) or 
                            !isset($record['Voti Femmine']) or
                            !isset($record['Voti Totali']) or
                            !isset($record['Timestamp']) 
                            ){
                         $checkArrayValid=false;
                         break;
                     }else{
                    
                        $confxvotanti_id=$record['Confxvotanti_id'];   
                        if(!isset($listConfVot[$confxvotanti_id])){
                            //get confxvontati by id and set off 
                            try{
                                $cofxvotanti_item= $this->ORMmanager->getEntityById(Confxvotanti::class,$confxvotanti_id);  
                                $listConfVot[$confxvotanti_id]=$cofxvotanti_item; 
                                $param_delete_array=['confxvotanti_id'=>$confxvotanti_id];    
                                $this->ORMmanager->setOffAllEntitiesByParameters($listRxVotanti,$param_delete_array);
                            }catch (\Throwable $t) {
                                $errorDelete=true;
                                break;
                                    
                            }
                                
                        }
                        

                        $cofxvotanti=$listConfVot[$confxvotanti_id];
                        $rxvotanti =new Rxvotanti();
                        $numero=$record['Sezione'];
                        //si assume possa esserci una sola sezione con quel numero
                        $sezione=$listSections[$numero];
                        $rxsezione_id=$sezione->getId();
                    
                        $rxvotanti->setRxsezioni($sezione);
                        $rxvotanti->setConfxvotanti($cofxvotanti);
                        if($record['Voti Maschi']!='')
                            $rxvotanti->setNumVotantiMaschi($record['Voti Maschi']);
                        if($record['Voti Femmine']!='')
                            $rxvotanti->setNumVotantiFemmine($record['Voti Femmine']);
                        if($record['Voti Totali']!='')
                            $rxvotanti->setNumVotantiTotali($record['Voti Totali']);
                        $rxvotanti->setOff('false');
                        $rxvotanti->setSent('0');
                        if($record['Timestamp']=='')
                            $rxvotanti->setTimestamp(new \DateTime("now"));
                        else
                            $rxvotanti->setTimestamp(new \DateTime($record['Timestamp']));
                        $rxvotanti->setInsDate(new \DateTime("now"));
                      
                        $this->ORMmanager->insertEntity($rxvotanti);
                       
                       
                     }
              
                 }
                 if($checkArrayValid && !$errorDelete){
                      $this->ORMmanager->commit();
                      $commit=true;
                      $formObj['arrayass']='';
                      $records= $ReportService->reportForCSVAffluenzeAll($event,$idComm);
                  }else{
                      $this->ORMmanager->rollback();
                  }
 
             }
           
      
     
         }catch (\Throwable $t) {
             $this->ORMmanager->rollback();
             throw $t;
         }
         //end save uploaded csv
  
        $template_par = [
            'records' => $records,
            'evento_id' => $event,
           
            'form' => $formObj['form'],
            'errorDelete' => $errorDelete,
            'checkArrayValid' => $checkArrayValid,
            'commit' => $commit
        ];
        return $this->render($template, $template_par);
    
    }

    /**
     * Split the complete array into 2 new arrays divided by type (pools on candidates or not valid pools)
     */
    private function divideByType(array &$completeArray, array &$type1, array &$type2, array &$invalidKeys, $keyColumn, $keyValue) 
    {
        //TODO: insert additional keys
        foreach($completeArray as $row) {
            if( $this->isColumnUnset($keyValue, $row) ) {
                //process ignores these lines
                continue;
            }
            elseif ($this->isColumnUnset('Timestamp', $row) ) {
                $row['Timestamp']= 'NOW()';
            }
            //where I have to put it
            if (in_array( $row[$keyColumn], $invalidKeys )) {
                $row[ $row[$keyColumn] ] = $row[$keyValue];
                array_push($type2, $row);
            }
            else {
                array_push($type1, $row);
            }
       }
    }

    /**
     * Project 3 records into 1 having a single section into final array
     */
    private function projectIntoSections(array &$initial_array, array &$final_array, array &$invalidKeys) 
    {
        foreach($initial_array as $row) {
            if (!isset($final_array[$row['Sezione']])) {
                $final_array[$row['Sezione']] = array();
                $final_array[$row['Sezione']]['Sezione'] = $row['Sezione'];
                $final_array[$row['Sezione']]['Timestamp'] = $row['Timestamp'];
            }
            foreach($invalidKeys as $key) {
                if (isset($row[$key])) {
                    $final_array[$row['Sezione']][$key] = $row[$key];
                }
            }
            if( $row['Timestamp'] > $final_array[$row['Sezione']]['Timestamp'] or $row['Timestamp']=='NOW()'){
                $final_array[$row['Sezione']]['Timestamp']= $row['Timestamp'];
            }
        }
    }

    /**
     * Write data on database.
     * Before it removes last char from sqlValues string (it must be a comma)
     */
    private function writeData($em, $sqlInsert, $sqlValues ) {
        $sqlValues = substr_replace($sqlValues ,"",-1);
        $sql = $sqlInsert.$sqlValues;
        $stmt = $em->getConnection()->prepare($sql);
        $r = $stmt->execute();
        return $r; 
    }

    /**
     * Load all main candidates
     * 
     * @Route("CSV/{event}/candidati/upload", name="CSVUploadCandidati")
     */
    function uploadAllCandidatesPoolsByCSV(Request $request, ReportService $ReportService, $event){
        //setup for elaboration a time limit of 300 seconds
        set_time_limit(300);
        $schema = getenv('BICORE_SCHEMA');
        //TODO: move to .env file ?
        $invalidKeys = ['__BIANCHE__','__CONTESTATE__','__NULLE__'];
        //in order to boost the insertion of many records
        $em = $this->ORMmanager->getManager(); 
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        //where elaboration has to landing after done
        $template = "UploadDataByCsv/scrutini-candidates-all.html.twig";
              
        $formObj=$this->formCSV($request);
        //save uploaded csv

        $checkArrayValid=true;
        $commit=false;
        //deleted_record=[]; //not used
        $genericError=false;
        $batchSize = 100;

        try{
            if(count($formObj['arrayass'])>1){
                $formObj['arrayass'] = isset($formObj['arrayass'][0]) ? $formObj['arrayass'] : array($formObj['arrayass']);

                //Check validity of arrays, when first invalid found it aroses an exception
                if (!$this->checkValidity($formObj['arrayass'], ['Id Candidato','Sezione'])) {
                    throw new ValidatorException("Invalid loaded CSV file");
                }

                //Upload the map of sections (number, RxSection)
                $resultsSection=$this->divoMiner->getSectionsByEvent($event);
                $sectionMap=$resultsSection['array'];
                $listOfIds=$resultsSection['listOfId'];
                //Upload the map of candidates
                $listCandidates = $this->divoMiner->getAllMainCandidatesByEvent($event);
                $candidateMap = array();
                foreach($listCandidates as $candidate) {
                    $candidateMap[$candidate->getIdSource()] = $candidate;
                }

                //read seeds
                $RxPools = $this->RTServicesProvider->getSeedRxScrutini();
                $RxNotValidPools = $this->RTServicesProvider->getSeedRxVotiNonValidi();
                //Invalidate already existent preferences
                $this->ORMmanager->setOffAllEntitiesByParametersIN($RxPools,['rxsezione_id'=>$listOfIds]);
                $this->ORMmanager->setOffAllEntitiesByParametersIN($RxNotValidPools,['rxsezione_id'=>$listOfIds]);
              
               //Divide into 2 different groups
               $array_candidates = array();
               $array_votes = array();
               $this->divideByType($formObj['arrayass'], $array_candidates, $array_votes, $invalidKeys, 'Cognome', 'Voti');

               $numItems_cand = count($array_candidates);
          
                //preparing INSERT query parts
                $sqlInsert = array();
                $sqlInsert['pools'] = "INSERT INTO ".$schema.".rxscrutinicandidati 
                (id, rxsezione_id, candidato_principale_id , off, timestamp, sent, voti_totale_candidato, discr, ins_date )
                VALUES ";
                $sqlInsert['invalid'] = "INSERT INTO ".$schema.".rxvotinonvalidi 
                (id, rxsezione_id, numero_schede_bianche, numero_schede_nulle, numero_schede_contestate, off, timestamp, sent, discr, ins_date )
                VALUES ";
                //preparing VALUES query parts
                $sqlValues = array();
                $sqlValues['pools'] = '';
                $sqlValues['invalid'] = '';

                //preparing controllers
                //DEAL CANDIDATES POOLS
                $i = 0;
                $insert = 0;
                foreach($array_candidates as $row) {  
                    $sqlValues['pools'] = $sqlValues['pools']."
                        (nextval('".$schema.".rxscrutinicandidati_id_seq'), ".$sectionMap[$row['Sezione']]->getId().", 
                        ".$candidateMap[$row['Id Candidato']]->getId().", 
                         false, '".$row['Timestamp']."', 0, ".$row['Voti'].",'extended', NOW()),";            
                    $i++;
                    $insert++;
                    if (($insert % $batchSize) === 0 && $insert > 0) {
                        $this->writeData($em, $sqlInsert['pools'], $sqlValues['pools'] );
                        $sqlValues['pools'] = '';
                    }
                }
                //are there some other records lesser than batchsize?
                if( ($insert% $batchSize) > 0 )  {
                    $this->writeData($em, $sqlInsert['pools'], $sqlValues['pools'] );
                }
                $em->flush(); //Persist objects that did not make up an entire batch
                $em->clear();
                $commit = true;
         
                //prepare data for insertion
                //reducing records by 3
                $reduced_array = array();
                $this->projectIntoSections($array_votes, $reduced_array, $invalidKeys);

                //DEAL NOT VALID POOLS
                $numItems_votes = count($reduced_array);
                //numero_schede_bianche, numero_schede_nulle, numero_schede_contestate
                $i = 0;
                $insert = 0;
                foreach($reduced_array as $row) {  
                    foreach($invalidKeys as $key) {
                        if (!isset($row[$key])) {
                            $row[$key] = 'null';
                        }
                    }
                    $sqlValues['invalid'] = $sqlValues['invalid']."
                        (nextval('".$schema.".rxvotinonvalidi_id_seq'),".$sectionMap[$row['Sezione']]->getId()."
                        ,".$row['__BIANCHE__']."
                        ,".$row['__NULLE__']."
                        ,".$row['__CONTESTATE__']."
                        ,false, '".$row['Timestamp']."', 0, 'extended', NOW()),";            
                    $i++;
                    $insert++;
                    if (($insert % $batchSize) === 0 && $insert > 0) {
                        $this->writeData($em, $sqlInsert['invalid'], $sqlValues['invalid'] );
                        $sqlValues['invalid'] = '';
                    }
                }
                //are there some other records lesser than batchsize?
                if( ($insert% $batchSize) > 0 )  {
                    $this->writeData($em, $sqlInsert['invalid'], $sqlValues['invalid'] );
                }
                $em->flush(); //Persist objects that did not make up an entire batch
                $em->clear();
                $commit = true;
            } 

        }
        catch (ValidatorException $e) {
            $checkArrayValid = false;
            $commit = false;
        }
        catch (\Throwable $t) {
             $genericError = true;
             throw $t;
        }
         //end uploaded csv
        $template_par = [
            'evento_id' => $event,
            'form' => $formObj['form'],
            'genericError' => $genericError,
            'checkArrayValid' => $checkArrayValid,
            'commit' => $commit
        ];
        return $this->render($template, $template_par);
    }


    private function votinonvalidiCandPrinc(&$records,$ente_id,$sezione_sel, ReportService $ReportService){
        $records_nulli= $ReportService->reportForCSVScrutiniVotiNulli($ente_id,$sezione_sel);
        foreach($records_nulli as $record_nulli){
            
       
            $record_bianche=$record_nulli;
            $record_contestate=$record_nulli;
            $record_schedenulle=$record_nulli;
            $record_bianche['voti_totale_candidato']=$record_nulli['numero_schede_bianche'];
            $record_bianche['candidato_principale_id']=0;
            $record_bianche['posizione']=0;
            $record_bianche['nome']='__BIANCHE__';
            $record_bianche['cognome']='__BIANCHE__';

            $record_contestate['voti_totale_candidato']=$record_nulli['numero_schede_contestate'];
            $record_contestate['candidato_principale_id']=0;
            $record_contestate['posizione']=0;
            $record_contestate['nome']='__CONTESTATE__';
            $record_contestate['cognome']='__CONTESTATE__';


            $record_schedenulle['voti_totale_candidato']=$record_nulli['numero_schede_nulle'];
            $record_schedenulle['candidato_principale_id']=0;
            $record_schedenulle['posizione']=0;
            $record_schedenulle['nome']='__NULLE__';
            $record_schedenulle['cognome']='__NULLE__';

        
            

        }
        array_push($records,$record_bianche,$record_contestate,$record_schedenulle );
    }


    private function votinonvalidiListe(&$records,$ente_id,$sezione_sel, ReportService $ReportService){
        $records_nulli= $ReportService->reportForCSVScrutiniVotiNulli($ente_id,$sezione_sel);
        foreach($records_nulli as $record_nulli){
           
       
            $record_bianche=$record_nulli;
            $record_contestate=$record_nulli;
            $record_schedenulle=$record_nulli;
            $record_totvotisolocandidato=$record_nulli;
            $record_bianche['voti_tot_lista']=$record_nulli['numero_schede_bianche'];
            $record_bianche['lista_preferenze_id']=0;
            $record_bianche['posizione']=0;
            $record_bianche['lista_desc']='__BIANCHE__';
         

            $record_contestate['voti_tot_lista']=$record_nulli['numero_schede_contestate'];
            $record_contestate['lista_preferenze_id']=0;
            $record_contestate['posizione']=0;
            $record_contestate['lista_desc']='__CONTESTATE__';
           

            $record_schedenulle['voti_tot_lista']=$record_nulli['numero_schede_nulle'];
            $record_schedenulle['lista_preferenze_id']=0;
            $record_schedenulle['posizione']=0;
            $record_schedenulle['lista_desc']='__NULLE__';



            $record_totvotisolocandidato['voti_tot_lista']=$record_nulli['tot_voti_dicui_solo_candidato'];
            $record_totvotisolocandidato['lista_preferenze_id']=0;
            $record_totvotisolocandidato['posizione']=0;
            $record_totvotisolocandidato['lista_desc']='__VALIDI_PRESIDENTE__';

        
            

        }
        array_push($records,$record_bianche,$record_contestate,$record_schedenulle,$record_totvotisolocandidato );
    }

     /**
     * 
     * @Route("uploadCandidatoPrincipale/{event}/{sezione_sel}", name="uploadCandidatoPrincipale")
     */
    function UploadCandidatoPrincipale($event, $sezione_sel, Request $request, ReportService $ReportService){


        $serviceUser = $this->ORMmanager->getServiceUser();
       
        $records= $ReportService->reportForCSVScrutiniCandidati($serviceUser->getEnti()->getId(),$sezione_sel);
        
        $this->votinonvalidiCandPrinc($records,$serviceUser->getEnti()->getId(),$sezione_sel,$ReportService);
    

        $template = "UploadDataByCsv/scrutinicandidatiprincipali.html.twig";
        $listRxScrutiniCandidati = $this->RTServicesProvider->getSeedRxScrutini();
     
        $formObj=$this->formCSV($request);
       //save uploaded csv
        $checkArrayValid=true;
        $commit=false;
        $deleted_record=[];
        $errorDelete=false;
        try{
            $this->ORMmanager->beginTransaction();
    
            if(count($formObj['arrayass'])>1){
                $formObj['arrayass'] = isset($formObj['arrayass'][0]) ? $formObj['arrayass'] : array($formObj['arrayass']);
                $rxvotinonvalidi =new Rxvotinonvalidi();
                $voti_non_validi=false;
             

                foreach($formObj['arrayass'] as $record){
                  
                   
                
                  
                    if(
                        !isset($record['evento_id']) or !isset($record['evento']) or 
                        !isset($record['circo_id']) or !isset($record['circ_desc']) or 
                        !isset($record['rxsezione_id']) or ($record['rxsezione_id']!==$sezione_sel)
                        or !isset($record['descrizione']) or !isset($record['numero']) or 
                        !isset($record['posizione']) or !isset($record['candidato_principale_id']) or
                        !isset($record['cognome']) or !isset($record['voti_totale_candidato']) or !isset($record['nome']) 
                        ){
                        
                        $checkArrayValid=false;
              
                        break; 
                    }else{ 
                    
                      
                       if(!isset($deleted_record[$record['rxsezione_id']])){
                
                           $sez_id=$record['rxsezione_id'];
                           $param_delete_array=['rxsezione_id'=>$sez_id];

                           try{
                               $this->ORMmanager->setOffAllEntitiesByParameters($listRxScrutiniCandidati,$param_delete_array);
                            }catch (\Throwable $t) {
                         
                               $errorDelete=true;
                               break;
                               
                           }
                           $deleted_record[$sez_id]=true;
                       }
                       $sezione= $this->ORMmanager->getEntityById(Rxsezioni::class,$record['rxsezione_id']);
                       if($record['candidato_principale_id']!=0){
                        $rxscrutinicandidati =new Rxscrutinicandidati();
                        $candidatoprincipale= $this->ORMmanager->getEntityById(Candidatiprincipali::class,$record['candidato_principale_id']);        
                        $rxscrutinicandidati->setRxsezioni($sezione);
                        $rxscrutinicandidati->setCandidatiprincipali($candidatoprincipale);
                        $rxscrutinicandidati->setVotiTotaleCandidato($record['voti_totale_candidato']);
                        $rxscrutinicandidati->setOff('false');
                        $rxscrutinicandidati->setSent('0');
                        $rxscrutinicandidati->setTimestamp(new \DateTime("now"));
                        $rxscrutinicandidati->setInsDate(new \DateTime("now"));
                        $this->ORMmanager->insertEntity($rxscrutinicandidati);
                       }
                       if($record['candidato_principale_id']==0){
                          
                        if(!$voti_non_validi){
                            $rxvotinonvalidi->setRxsezioni($sezione);
                            $rxvotinonvalidi->setOff('false');
                            $rxvotinonvalidi->setSent('0');
                            $rxvotinonvalidi->setTimestamp(new \DateTime("now"));
                            $rxvotinonvalidi->setInsDate(new \DateTime("now"));
                            $voti_non_validi=true;
                          
                        }              
                       
                        if($record['nome']=='__BIANCHE__')
                            $rxvotinonvalidi->setNumeroSchedeBianche($record['voti_totale_candidato']);
                        if($record['nome']=='__NULLE__')
                            $rxvotinonvalidi->setNumeroSchedeNulle($record['voti_totale_candidato']);
                        if($record['nome']=='__CONTESTATE__')
                            $rxvotinonvalidi->setNumeroSchedeContestate($record['voti_totale_candidato']);
                       
                        
                        } 

                      
                      
                    }
                   
                }
   
                if($voti_non_validi) $this->ORMmanager->insertEntity($rxvotinonvalidi);
              
                if($checkArrayValid && !$errorDelete){
                     $this->ORMmanager->commit();
                     $commit=true;
                     $formObj['arrayass']='';
                     $records= $ReportService->reportForCSVScrutiniCandidati($serviceUser->getEnti()->getId(),$sezione_sel);
                     $records_nulli= $ReportService->reportForCSVScrutiniVotiNulli($serviceUser->getEnti()->getId(),$sezione_sel);
                     foreach($records_nulli as $record_nulli){
                         
                    
                         $record_bianche=$record_nulli;
                         $record_contestate=$record_nulli;
                         $record_schedenulle=$record_nulli;
                         $record_bianche['voti_totale_candidato']=$record_nulli['numero_schede_bianche'];
                         $record_bianche['candidato_principale_id']=0;
                         $record_bianche['posizione']=0;
                         $record_bianche['nome']='__BIANCHE__';
                         $record_bianche['cognome']='__BIANCHE__';
             
                         $record_contestate['voti_totale_candidato']=$record_nulli['numero_schede_contestate'];
                         $record_contestate['candidato_principale_id']=0;
                         $record_contestate['posizione']=0;
                         $record_contestate['nome']='__CONTESTATE__';
                         $record_contestate['cognome']='__CONTESTATE__';
             
             
                         $record_schedenulle['voti_totale_candidato']=$record_nulli['numero_schede_nulle'];
                         $record_schedenulle['candidato_principale_id']=0;
                         $record_schedenulle['posizione']=0;
                         $record_schedenulle['nome']='__NULLE__';
                         $record_schedenulle['cognome']='__NULLE__';
             
                     
                         
             
                     }

                     array_push($records,$record_bianche,$record_contestate,$record_schedenulle );
                 }else{
                     $this->ORMmanager->rollback();
                 }

            }
          
     
    
        }catch (\Throwable $t) {
            $this->ORMmanager->rollback();
            throw $t;
        }
        //end uploaded csv
  
      
        $template_par = [
            'evento_id' => $event,
            'records' => $records,
            'idsezione' => $sezione_sel,
            'form' => $formObj['form'],
            'errorDelete' => $errorDelete,
            'checkArrayValid' => $checkArrayValid,
            'commit' => $commit
        ];
        return $this->render($template, $template_par);

    }


     /**
     * 
     * @Route("uploadListe/{event}/{sezione_sel}", name="uploadListe")
     */
    function UploadListe($event,$sezione_sel,Request $request, ReportService $ReportService){


        $serviceUser = $this->ORMmanager->getServiceUser();
        $records= $ReportService->reportForCSVScrutiniListe($serviceUser->getEnti()->getId(),$sezione_sel);
        
        $this->votinonvalidiListe($records,$serviceUser->getEnti()->getId(),$sezione_sel,$ReportService);
     
        $template = "UploadDataByCsv/scrutiniliste.html.twig";
        $listRxScrutiniListe = $this->RTServicesProvider->getSeedRxScrutiniListe();
        $formObj=$this->formCSV($request);

        $checkArrayValid=true;
        $commit=false;
        $deleted_record=[];
        $errorDelete=false;
        try{
            $this->ORMmanager->beginTransaction();

            if(count($formObj['arrayass'])>1){
                $formObj['arrayass'] = isset($formObj['arrayass'][0]) ? $formObj['arrayass'] : array($formObj['arrayass']);
                foreach($formObj['arrayass'] as $record){
                    if(
                        !isset($record['evento_id']) or !isset($record['evento']) or 
                        !isset($record['circo_id']) or !isset($record['circ_desc']) or 
                        !isset($record['rxsezione_id']) or ($record['rxsezione_id']!==$sezione_sel)
                        or !isset($record['descrizione']) or !isset($record['numero']) or 
                        !isset($record['lista_preferenze_id']) or !isset($record['lista_desc']) or
                        !isset($record['voti_tot_lista'] )
                        ){
                        $checkArrayValid=false;
            
                        break; 
                    }else{ 
                    
                    $rxscrutiniliste =new Rxscrutiniliste();
                    if(!isset($deleted_record[$record['rxsezione_id']])){
                
                        $sez_id=$record['rxsezione_id'];
                        $param_delete_array=['rxsezione_id'=>$sez_id];

                        try{
                            $this->ORMmanager->setOffAllEntitiesByParameters($listRxScrutiniListe,$param_delete_array);
                            }catch (\Throwable $t) {
                        
                            $errorDelete=true;
                            break;
                            
                        }
                        $deleted_record[$sez_id]=true;
                    }
                    
                    
                        $sezione= $this->ORMmanager->getEntityById(Rxsezioni::class,$record['rxsezione_id']);
                        $listapreferenza= $this->ORMmanager->getEntityById(Listapreferenze::class,$record['lista_preferenze_id']);        
                        $rxscrutiniliste->setRxsezioni($sezione);
                        $rxscrutiniliste->setListapreferenze($listapreferenza);
                        $rxscrutiniliste->setVotiTotLista($record['voti_tot_lista']);
                        $rxscrutiniliste->setOff('false');
                        $rxscrutiniliste->setSent('0');
                        $rxscrutiniliste->setTimestamp(new \DateTime("now"));
                        $rxscrutiniliste->setInsDate(new \DateTime("now"));
                    
                        $this->ORMmanager->insertEntity($rxscrutiniliste);

                    
                    
                    }
                
                }
            
                if($checkArrayValid && !$errorDelete){
                    $this->ORMmanager->commit();
                    $commit=true;
                    $formObj['arrayass']='';
                    $records= $ReportService->reportForCSVScrutiniListe($serviceUser->getEnti()->getId(),$sezione_sel);
                }else{
                    $this->ORMmanager->rollback();
                }

            }
        


        }catch (\Throwable $t) {
            $this->ORMmanager->rollback();
            throw $t;
        }

        //end uploaded csv

      
        $template_par = [
            'records' => $records,
            'evento_id' => $event,
            'idsezione' => $sezione_sel,
            'form' => $formObj['form'],
            'errorDelete' => $errorDelete,
            'checkArrayValid' => $checkArrayValid,
            'commit' => $commit
        ];
        return $this->render($template, $template_par);
    }



    
     


      /**
     * 
     * @Route("uploadCandidatoSecondario/{event}/{sezione_sel}", name="uploadCandidatoSecondario")
     */
    function UploadCandidatoSecondario($event, $sezione_sel,Request $request, ReportService $ReportService){


        $serviceUser = $this->ORMmanager->getServiceUser();
        $records= $ReportService->reportForCSVScrutiniCandidatiSec($serviceUser->getEnti()->getId(),$sezione_sel);
        $template = "UploadDataByCsv/scrutinicandidatisecondari.html.twig";
        $listRxpreferenze= $this->RTServicesProvider->getSeedRxPreferenze();
      
        $formObj=$this->formCSV($request);
         //save uploaded csv

         $checkArrayValid=true;
         $commit=false;
         $deleted_record=[];
         $errorDelete=false;
         try{
             $this->ORMmanager->beginTransaction();
 
             if(count($formObj['arrayass'])>1){
                $formObj['arrayass'] = isset($formObj['arrayass'][0]) ? $formObj['arrayass'] : array($formObj['arrayass']);
                 foreach($formObj['arrayass'] as $record){
                  
                   if(
                         !isset($record['evento_id']) or !isset($record['evento']) or 
                         !isset($record['circo_id']) or !isset($record['circ_desc']) or 
                         !isset($record['rxsezione_id']) or ($record['rxsezione_id']!==$sezione_sel)
                         or !isset($record['descrizione']) or !isset($record['numero']) or 
                         !isset($record['lista_preferenze_id']) or !isset($record['lista_desc'])  or !isset($record['posizione']) or
                         !isset($record['candidato_secondario_id']) or !isset($record['nome'])  or !isset($record['cognome']) or
                         !isset($record['numero_voti'] )
                         ){
                         $checkArrayValid=false;
             
                         break; 
                     }else{ 
                     
                     $rxpreferenze =new Rxpreferenze();
                     if(!isset($deleted_record[$record['rxsezione_id']])){
                 
                         $sez_id=$record['rxsezione_id'];
                         $param_delete_array=['rxsezione_id'=>$sez_id];
 
                         try{
                             $this->ORMmanager->setOffAllEntitiesByParameters($listRxpreferenze,$param_delete_array);
                             }catch (\Throwable $t) {
                         
                             $errorDelete=true;
                             break;
                             
                         }
                         $deleted_record[$sez_id]=true;
                     }
                     
                     
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
                     
                         $this->ORMmanager->insertEntity($rxpreferenze);
 
                     
                     
                     }
                 
                 }
             
                 if($checkArrayValid && !$errorDelete){
                     $this->ORMmanager->commit();
                     $commit=true;
                     $formObj['arrayass']='';
                     $records= $ReportService->reportForCSVScrutiniCandidatiSec($serviceUser->getEnti()->getId(),$sezione_sel);
                 }else{
                     $this->ORMmanager->rollback();
                 }
 
             }
         
 
 
         }catch (\Throwable $t) {
             $this->ORMmanager->rollback();
             throw $t;
         }
 
         //end uploaded csv
        $template_par = [
            'evento_id' => $event,
            'records' => $records,
            'idsezione' => $sezione_sel,
            'form' => $formObj['form'],
            'errorDelete' => $errorDelete,
            'checkArrayValid' => $checkArrayValid,
            'commit' => $commit
        ];
        return $this->render($template, $template_par);
    }

      /**
     * 
     * @Route("uploadVotiNulli/{sezione_sel}", name="uploadVotiNulli")
     */
    function UploadVotiNulli($sezione_sel,Request $request, ReportService $ReportService){


        $serviceUser = $this->ORMmanager->getServiceUser();
        $records= $ReportService->reportForCSVScrutiniVotiNulli($serviceUser->getEnti()->getId(),$sezione_sel);
        $template = "UploadDataByCsv/scrutinivotinulli.html.twig";
        $listRxvotinonvalidi= $this->RTServicesProvider->getSeedRxVotiNonValidi();
        $formObj=$this->formCSV($request);
         //save uploaded csv

         $checkArrayValid=true;
         $commit=false;
         $deleted_record=[];
         $errorDelete=false;

         try{
             $this->ORMmanager->beginTransaction();
 
             if(count($formObj['arrayass'])>1){
                $formObj['arrayass'] = isset($formObj['arrayass'][0]) ? $formObj['arrayass'] : array($formObj['arrayass']);

                 foreach($formObj['arrayass'] as $record){
                    
                     if(
                         !isset($record['evento_id']) or !isset($record['evento']) or 
                         !isset($record['circo_id']) or !isset($record['circ_desc']) or 
                         !isset($record['rxsezione_id']) or ($record['rxsezione_id']!==$sezione_sel)
                         or !isset($record['descrizione']) or !isset($record['numero']) or 
                         !isset($record['numero_schede_bianche']) or !isset($record['numero_schede_nulle']) or
                         !isset($record['numero_schede_contestate'] )
                         ){
                         $checkArrayValid=false;
             
                         break; 
                     }else{ 
                     
                    
                     if(!isset($deleted_record[$record['rxsezione_id']])){
                 
                         $sez_id=$record['rxsezione_id'];
                         $param_delete_array=['rxsezione_id'=>$sez_id];
 
                         try{
                             $this->ORMmanager->setOffAllEntitiesByParameters($listRxvotinonvalidi,$param_delete_array);
                             }catch (\Throwable $t) {
                         
                             $errorDelete=true;
                             break;
                             
                         }
                         $deleted_record[$sez_id]=true;
                     }
                     
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
                     
                         $this->ORMmanager->insertEntity($rxvotinonvalidi);
 
                     
                     
                     }
                 
                 }
             
                 if($checkArrayValid && !$errorDelete){
                     $this->ORMmanager->commit();
                     $commit=true;
                     $formObj['arrayass']='';
                     $records= $ReportService->reportForCSVScrutiniVotiNulli($serviceUser->getEnti()->getId(),$sezione_sel);
                 }else{
                     $this->ORMmanager->rollback();
                 }
 
             }
         
 
 
         }catch (\Throwable $t) {
             $this->ORMmanager->rollback();
             throw $t;
         }
 
         //end uploaded csv
      
        $template_par = [
            'records' => $records,
            'idsezione' => $sezione_sel,
            'form' => $formObj['form'],
            'errorDelete' => $errorDelete,
            'checkArrayValid' => $checkArrayValid,
            'commit' => $commit
        ];
        return $this->render($template, $template_par);
    }




    //ANAGRAFICHE

    /**
     * 
     * @Route("uploadCSVRxCandidati", name="uploadCSVRxCandidati")
     */
    function uploadRxCandidati( Request $request, ReportService $ReportService){

        $serviceUser = $this->ORMmanager->getServiceUser();   
        $ente=$serviceUser->getEnti()->getId();
        $param_filter=['ente_id'=>$ente];
        $serviceURLCandidati = $this->RTServicesProvider->getSeedRxCandidati();
        $records = $this->ORMmanager->getActiveEntityObjects($serviceURLCandidati,$param_filter,[]);
        $template = "UploadDataByCsv/rxcandidati.html.twig";
  
       
        $formObj=$this->formCSV($request);
       

        //save uploaded csv
        $checkArrayValid=true;
        $commit=false;
        $deleted_record=[];
        $errorDelete=false;
        $listID=array();
        try{
            $this->ORMmanager->beginTransaction();
    
            if(count($formObj['arrayass'])>1){
                $formObj['arrayass'] = isset($formObj['arrayass'][0]) ? $formObj['arrayass'] : array($formObj['arrayass']);

                foreach($formObj['arrayass'] as $record){
           
                    $rxcandidati =new RxCandidati();
                   
                   
                    if( !isset($record['Nome']) or 
                        !isset($record['Cognome']) or
                        !isset($record['IdSource']) or
                        array_search($record['IdSource'],$listID)!==false)
                        {
                        $checkArrayValid=false;
                        break;
                    }else{
                       
                        array_push($listID, $record['IdSource']);
                        if(!isset($deleted_record[$ente])){
                            $param_delete_array=['ente_id'=>$ente];
                            try{
                            
                                $this->ORMmanager->deleteAllEntitiesByParameters($serviceURLCandidati,$param_delete_array);
                             }catch (\Throwable $t) {
                         
                                $errorDelete=true;
                                break;
                                
                            }
                            $deleted_record[$ente]=true;
                        }
                       
                        $rxcandidati->setEnti($serviceUser->getEnti());
                        $rxcandidati->setNome($record['Nome']);
                        $rxcandidati->setCognome($record['Cognome']);
                        $rxcandidati->setIdSource($record['IdSource']);
                        $rxcandidati->setOff('false');
                        $rxcandidati->setTimestamp(new \DateTime("now"));
                        
                        $this->ORMmanager->insertEntity($rxcandidati);
                     
                       
                      
                    }
         
              
                   
                }
          
                if($checkArrayValid && !$errorDelete){
                     $this->ORMmanager->commit();
                     $commit=true;
                     $formObj['arrayass']='';
                     $records = $this->ORMmanager->getActiveEntityObjects($serviceURLCandidati,$param_filter,[]);
 
                 }else{
                     $this->ORMmanager->rollback();
                 }

            }
          
     
    
        }catch (\Throwable $t) {
            $this->ORMmanager->rollback();
            throw $t;
        }

        //end save uploaded csv

        $template_par = [
            'records' => $records,
            'form' => $formObj['form'],
            'errorDelete' => $errorDelete,
            'checkArrayValid' => $checkArrayValid,
            'commit' => $commit
        ];
  
        return $this->render($template, $template_par);
    }



      /**
     * 
     * @Route("uploadCSVRxListe", name="uploadCSVRxListe")
     */
    function uploadCSVRxListe( Request $request, ReportService $ReportService){

        $serviceUser = $this->ORMmanager->getServiceUser();   
        $ente=$serviceUser->getEnti()->getId();
        $param_filter=['ente_id'=>$ente];
        $serviceURLListe = $this->RTServicesProvider->getSeedRxListe();
        $records = $this->ORMmanager->getActiveEntityObjects($serviceURLListe,$param_filter,[]);
       
        $template = "UploadDataByCsv/rxliste.html.twig";
  

        $formObj=$this->formCSV($request);
       

        //save uploaded csv
        $checkArrayValid=true;
        $commit=false;
        $deleted_record=[];
        $errorDelete=false;
        $listID=array();
        try{
            $this->ORMmanager->beginTransaction();
    
            if(count($formObj['arrayass'])>1){
                $formObj['arrayass'] = isset($formObj['arrayass'][0]) ? $formObj['arrayass'] : array($formObj['arrayass']);

                foreach($formObj['arrayass'] as $record){
           
                    $rxliste =new RxListe();
                   
                   
                    if( !isset($record['ListaDesc']) or 
                        !isset($record['NomeCand']) or
                        !isset($record['IdSource']) or
                        array_search($record['IdSource'],$listID)!==false)
                        {
                        $checkArrayValid=false;
                        break;
                    }else{
                       
                        array_push($listID, $record['IdSource']);
                        if(!isset($deleted_record[$ente])){
                            $param_delete_array=['ente_id'=>$ente];
                            try{
                            
                                $this->ORMmanager->deleteAllEntitiesByParameters($serviceURLListe,$param_delete_array);
                             }catch (\Throwable $t) {
                         
                                $errorDelete=true;
                                break;
                                
                            }
                            $deleted_record[$ente]=true;
                        }
                       
                        $rxliste->setEnti($serviceUser->getEnti());
                        $rxliste->setListaDesc($record['ListaDesc']);
                        $rxliste->setNomeCand($record['NomeCand']);
                        $rxliste->setIdSource($record['IdSource']);
                        $rxliste->setOff('false');
                        $rxliste->setTimestamp(new \DateTime("now"));
                        
                        
                        $this->ORMmanager->insertEntity($rxliste);
                     
                    
                      
                    }
         
              
                   
                }

                if($checkArrayValid && !$errorDelete){
                     $this->ORMmanager->commit();
                     $commit=true;
                     $formObj['arrayass']='';
                     $records = $this->ORMmanager->getActiveEntityObjects($serviceURLListe,$param_filter,[]);
 
                 }else{
                     $this->ORMmanager->rollback();
                 }

            }
          
     
    
        }catch (\Throwable $t) {
            $this->ORMmanager->rollback();
            throw $t;
        }

        //end save uploaded csv

        $template_par = [
            'records' => $records,
            'form' => $formObj['form'],
            'errorDelete' => $errorDelete,
            'checkArrayValid' => $checkArrayValid,
            'commit' => $commit
        ];
  
        return $this->render($template, $template_par);
    }


     /**
     * 
     * @Route("uploadCSVRxCandidatiSecondari", name="uploadCSVRxCandidatiSecondari")
     */
    function uploadCSVRxCandidatiSecondari( Request $request, ReportService $ReportService){

        $serviceUser = $this->ORMmanager->getServiceUser();   
        $ente=$serviceUser->getEnti()->getId();
        $param_filter=['ente_id'=>$ente];
        $serviceURLCandidatiSecondari = $this->RTServicesProvider->getSeedRxCandidatiSecondari();
        $records = $this->ORMmanager->getActiveEntityObjects($serviceURLCandidatiSecondari,$param_filter,[]);
        $template = "UploadDataByCsv/rxcandidatisecondari.html.twig";
  
       
        $formObj=$this->formCSV($request);
       

        //save uploaded csv
        $checkArrayValid=true;
        $commit=false;
        $deleted_record=[];
        $errorDelete=false;
        $listID=array();
        try{
            $this->ORMmanager->beginTransaction();
    
            if(count($formObj['arrayass'])>1){
                $formObj['arrayass'] = isset($formObj['arrayass'][0]) ? $formObj['arrayass'] : array($formObj['arrayass']);

                foreach($formObj['arrayass'] as $record){
           
                    $rxcandidatisecondari =new RxCandidatisecondari();
                    $key= array_search($record['IdSource'],$listID);
                   
                    if( !isset($record['Nome']) or 
                        !isset($record['Cognome']) or
                        !isset($record['IdSource']) or
                        !isset($record['IdSourceLista']) or
                        array_search($record['IdSource'],$listID)!==false)
                        {
                        $checkArrayValid=false;
                        break;
                    }else{
                       
                        array_push($listID, $record['IdSource']);
                        if(!isset($deleted_record[$ente])){
                            $param_delete_array=['ente_id'=>$ente];
                            try{
                            
                                $this->ORMmanager->deleteAllEntitiesByParameters($serviceURLCandidatiSecondari,$param_delete_array);
                             }catch (\Throwable $t) {
                         
                                $errorDelete=true;
                                break;
                                
                            }
                            $deleted_record[$ente]=true;
                        }
            
                       
                        $rxcandidatisecondari->setEnti($serviceUser->getEnti());
                        $rxcandidatisecondari->setNome($record['Nome']);
                        $rxcandidatisecondari->setCognome($record['Cognome']);
                        $rxcandidatisecondari->setIdSource($record['IdSource']);
                        $rxcandidatisecondari->setRxlistaId($record['IdSourceLista']);
                        $rxcandidatisecondari->setOff('false');
                        $rxcandidatisecondari->setTimestamp(new \DateTime("now"));
                        
                        
                        $this->ORMmanager->insertEntity($rxcandidatisecondari);
                   
                 
                       
                      
                    }
         
                 
                   
                }
                
                
          
                if($checkArrayValid && !$errorDelete){
                     $this->ORMmanager->commit();
                     $commit=true;
                     $formObj['arrayass']='';
                     $records = $this->ORMmanager->getActiveEntityObjects($serviceURLCandidatiSecondari,$param_filter,[]);
 
                 }else{
                     $this->ORMmanager->rollback();
                 }

            }
          
     
    
        }catch (\Throwable $t) {
            $this->ORMmanager->rollback();
            throw $t;
        }

        //end save uploaded csv

        $template_par = [
            'records' => $records,
            'form' => $formObj['form'],
            'errorDelete' => $errorDelete,
            'checkArrayValid' => $checkArrayValid,
            'commit' => $commit
        ];
  
        return $this->render($template, $template_par);
    }

     /**
     * It checks if a column has set
     */
    private function isColumnUnset(String $columnName, &$row): bool 
    {
        //for default we expect that it's set
        $outcome = false;
        if(!isset($row[$columnName]) or $row[$columnName]=='') { 
            //it's unset!
            $outcome = true;
        }
        return $outcome;
    }

    /**
     * It checks a row if has a valid format
     */
    private function hasValidFormat(array $keys, &$row): bool 
    {
        $outcome = true;
        foreach($keys as $key) {
            if( $this->isColumnUnset($key, $row)){
              
                $outcome = false;
                break;
            }
        }
        return $outcome;
    }

    /**
     * It checks validity of a given set of rows
     */
    private function checkValidity(array &$rows, array $keys): bool 
    {
        $outcome = true;
        
        foreach($rows as $row) {
          
            if (!$this->hasValidFormat($keys, $row)) {

                $outcome = false;
                break;
            }
        }
        return $outcome;
    }



}
