<?php
// src/Controller/DummyController.php
namespace App\Controller;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
//in order to let possible render templates
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\AppProxyRest;
use App\Entity\Rxcandidati;
use App\Entity\Rxcandidatisecondari;
use App\Entity\Rxliste;
use App\Service\RTServicesProvider;
use App\Service\RTDivoDataMiner;
use App\Service\ORMManager;


/**
 * This is the controller managing navigation of app
 */
class RTCSVxSourceServicesController extends DivoController
{


     /**
     *  @Route("/service/downCSVxSource", name="downCSVxSource")
     */
    public function goRTCSVhome()
    {
      
        $template = "Csvdownload/sceltaEvento.html.twig";
        $serviceUser = $this->ORMmanager->getServiceUser();   
        $eventLinks = $this->divoMiner->getEventsLinksByEnte( $serviceUser );

        $num_eventi=count($eventLinks);
        
        $template_par = [
            'eventi' =>  $eventLinks,
            'num_eventi' =>  $num_eventi,
            
        ];
  
        return $this->render($template, $template_par);
    }


     /**
     *  @Route("/service/downCSVxSource/{event}", name="downCSVxSourceByEvent")
     */
    public function goRTCSVhomeBy($event)
    {
      
        $template = "Csvdownload/csvdownload.home.html.twig";
      
        $template_par = [
            'evento' =>  $event,
            
        ];
        


        return $this->render($template, $template_par);
    }

    


    /**
     *  @Route("/service/downCSVCandPrincxSource/{event}", name="downCSVCandPrincxSource")
     */
    public function goRTCSVCandPrincSourceDown($event)
    {
      
        $template = "Csvdownload/candidatoprincipale.down.html.twig";
      
        $template_par = [];

        $visible_events = $this->divoMiner->readCandidatiListeByEvent($event);
     
      
       $template_par = [ 'visible_objects' => $visible_events];


        return $this->render($template, $template_par);
    }




    /**
     *  @Route("/service/downCSVCandSecxSource/{event}", name="downCSVCandSecxSource")
     */
    public function goRTCSVCandSecSourceDown($event)
    {
      
        $template = "Csvdownload/candidatisecondari.down.html.twig";
      
        $template_par = [];

        $visible_events = $this->divoMiner->readCandidatiListeByEvent($event);

        $template_par = [
            'visible_objects' => $visible_events,
           // 'listCandidates' => $listCandidates,
        ];

        return $this->render($template, $template_par);
    }

    



}



