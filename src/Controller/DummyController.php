<?php
// src/Controller/DummyController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
//in order to let possible render templates
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * This is a Dummy controller to be used only to check parameter values regarding REGIONE TOSCANA SERVICES.
 */
class DummyController extends AbstractController
{


    /**
     *  @Route("/service/21", name="go21")
     */
    public function getGET21()
    {
        $registry = RTServicesProvider::getInstance();
        $serviceURL = $registry->getRT_ComunicazioniEventi();
        //template to render the list of employees
        $template = "foo/foo.html.twig";
        return $this->render($template, [
        //we are providing the data to the template
        'myURL' => $serviceURL,
        ]);
    }



    /**
     *  @Route("/service/22", name="go22")
     */
    public function getGET22()
    {
        $serviceURL = RTServicesProvider::getInstance()->getRT_Liste();
        //template to render the list of employees
        $template = "foo/foo.html.twig";
        return $this->render($template, [
        //we are providing the data to the template
        'myURL' => $serviceURL,
        ]);
    }

        /**
     *  @Route("/service/31", name="go31")
     */
    public function getPUT31()
    {
        $serviceURL = RTServicesProvider::getInstance()->getRT_Votanti();
        //template to render the list of employees
        $template = "foo/foo.html.twig";
        return $this->render($template, [
        //we are providing the data to the template
        'myURL' => $serviceURL,
        ]);
    }

        /**
     *  @Route("/service/32", name="go32")
     */
    public function getPUT32()
    {
        $serviceURL = RTServicesProvider::getInstance()->getRT_Scrutini();
        //template to render the list of employees
        $template = "foo/foo.html.twig";
        return $this->render($template, [
        //we are providing the data to the template
        'myURL' => $serviceURL,
        ]);
    }

        /**
     *  @Route("/service/33", name="gO33")
     */
    public function getPUT33()
    {
        $serviceURL = RTServicesProvider::getInstance()->getRT_Preferenze();
        //template to render the list of employees
        $template = "foo/foo.html.twig";
        return $this->render($template, [
        //we are providing the data to the template
        'myURL' => $serviceURL,
        ]);
    }







}



