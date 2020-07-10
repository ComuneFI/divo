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



/**
 * This is the controller managing navigation of app
 */
class SourceServicesController extends DivoController
{
    /**
     *  @Route("/service/downsource", name="retrieveSOURCEdata")
     */
    public function goRTDownSource()
    {
       // $registry = RTServicesProvider::getInstance();
       $template = "source/source.down.html.twig";
        return $this->render($template, [
        //'myURL' => $serviceURL,
        ]);
    }


    /**
     *  @Route("/service/mappingData", name="mappingSOURCEdata")
     */
    public function goMappingData()
    {
       //$registry = RTServicesProvider::getInstance();
        $template = "regione/regione.up.html.twig";
        return $this->render($template, []);
    }


}



