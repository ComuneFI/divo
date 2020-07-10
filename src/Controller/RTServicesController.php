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

use App\Service\RTSentableInterface;

/**
 * This is the controller managing navigation of app
 */
class RTServicesController extends DivoController
{

    /**
     *  @Route("/service/down", name="retrieveRTdata")
     */
    public function goRTDown()
    {
        //$registry = RTServicesProvider::getInstance();
        $template = "regione/regione.down.html.twig";
        return $this->render($template, [
        //'myURL' => $serviceURL,
        ]);

    }

    /**
     *  @Route("/service/up", name="sendRTdata")
     */
    public function goRTUp()
    {
       //$registry = RTServicesProvider::getInstance();
        $results=$this->divoMiner->getConfStatus();
        $template = "regione/regione.up.html.twig";
        return $this->render($template, ['config_status'=>$results]);
    }


    /**
     *  @Route("/service/config", name="config")
     */
    public function goConfig()
    {
       //$registry = RTServicesProvider::getInstance();
        $template = "config/config.html.twig";
        return $this->render($template, []);
    }




}



