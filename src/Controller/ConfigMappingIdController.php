<?php

// src/Controller/DummyController.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
//in order to let possible render templates
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\AppProxyRest;
use App\Entity\Rxcandidati;
use App\Entity\Rxcandidatisecondari;
use App\Entity\Rxliste;
use App\Entity\FileCSV;
use App\Service\RTServicesProvider;
use App\Service\RTDivoDataMiner;
use App\Service\ORMManager;
use App\Form\FileCSVType;

/**
 * This is the controller managing navigation of app
 */
class ConfigMappingIdController extends DivoController {


   private function formCSV(Request $request){
    $filecsv = new FileCSV();
    $form = $this->createForm(FileCSVType::class, $filecsv);

    $form->handleRequest($request);
    $arrayass=[];
   
    if ($form->isSubmitted() && $form->isValid() ) {

        $filecsvFile =  $form->get('filecsv')->getData();
 
       
        if ($filecsvFile) {
    
            $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
            // decoding CSV contents
            $data = $serializer->decode(file_get_contents($filecsvFile->getPathname()), 'csv');
           
            foreach($data as $dataitem){
                if(isset($dataitem['Id'])&& isset($dataitem['Id Source']))
                $arrayass[$dataitem['Id']]=$dataitem['Id Source'];
            }
      
            try {
               
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
     * Step 1: get CandidatiPrincipali saved into DIVO and send to twig for configuration with idSource
     * @Route("/config/candidati", name="configCandidati")
     */
    function configCandidati(Request $request){
        $serviceUser = $this->ORMmanager->getServiceUser();
        $template = "config/candidati.html.twig";
        $template_par = [];
        
        $visible_events = $this->divoMiner->readCandidatiListe();
        $param_filter=['ente_id' => $serviceUser->getEnti()->getId()];
        $param_order= ['cognome' => 'ASC', 'nome' => 'ASC'];

        $serviceURLCandidati = $this->RTServicesProvider->getSeedRxCandidati();
        $listCandidates = $this->ORMmanager->getActiveEntityObjects($serviceURLCandidati,$param_filter,$param_order);
        $objUPCSV=$this->formCSV($request);
        $form=$objUPCSV['form'];
        $dataupload=$objUPCSV['arrayass'];

        $template_par = [
            'visible_objects' => $visible_events,
            'listCandidates' => $listCandidates,
            'form'=>$form,
            'dataupload'=>$dataupload,
            'state'=>'COF_CANDIDATES',
        ];
  
       
        return $this->render($template, $template_par);
    }


     /**
     * Step 2: get Liste saved into DIVO and send to twig for configuration with idSource
     * @Route("/config/liste", name="configListe")
     */
    function configListe(Request $request) {
        $serviceUser = $this->ORMmanager->getServiceUser();
        $template = "config/liste.html.twig";
        $template_par = [];
        
        $visible_events = $this->divoMiner->readCandidatiListe();
        $param_filter=['ente_id' => $serviceUser->getEnti()->getId()];
        $param_order= ['lista_desc' => 'ASC'];

        $serviceURLListe = $this->RTServicesProvider->getSeedRxListe();
        $listLists = $this->ORMmanager->getActiveEntityObjects($serviceURLListe,$param_filter,$param_order);
        $objUPCSV=$this->formCSV($request);
        $form=$objUPCSV['form'];
        $dataupload=$objUPCSV['arrayass'];
     
        $template_par = [
            'visible_objects' => $visible_events,
            'listLists' => $listLists,
            'form'=>$form,
            'dataupload'=>$dataupload,
            'state'=>'COF_LISTS',
        ];
        return $this->render($template, $template_par);
    }




    /**
     * Step 3: get Candidatisecondari saved into DIVO and send to twig for configuration with idSource
     * @Route("/config/candidatisecondari", name="configCandidatiSecondari")
     */
    function configCandidatiSecondari(Request $request) {
        $serviceUser = $this->ORMmanager->getServiceUser();
        $template = "config/candidatisecondari.html.twig";
        $template_par = [];

        $visible_events = $this->divoMiner->readCandidatiListe();
        $param_filter=['ente_id' => $serviceUser->getEnti()->getId()];
        $param_order= ['cognome' => 'ASC', 'nome' => 'ASC'];

        $serviceURLCandidatiSecondari = $this->RTServicesProvider->getSeedRxCandidatiSecondari();
        $listCandidatesSecondari = $this->ORMmanager->getActiveEntityObjects($serviceURLCandidatiSecondari,$param_filter,$param_order);
       
       
        $objUPCSV=$this->formCSV($request);
        $form=$objUPCSV['form'];
        $dataupload=$objUPCSV['arrayass'];
        $template_par = [
            'visible_objects' => $visible_events,
            'listCandidates' => $listCandidatesSecondari,
            'form'=>$form,
            'dataupload'=>$dataupload,
            'state'=>'COF_PREFERENCESCANDIDATES',
        ];

        return $this->render($template, $template_par);
    }

}
