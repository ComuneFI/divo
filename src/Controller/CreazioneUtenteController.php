<?php

// src/Controller/DummyController.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
//in order to let possible render templates
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This is a Dummy controller to be used only to check parameter values regarding REGIONE TOSCANA SERVICES.
 */
class CreazioneUtenteController extends AbstractController {

    /**
     *  @Route("/CreazioneUtente", name="CreazioneUtenteIndex")
     */
    public function index() {

        $template = "CreazioneUtente/index.html.twig";
        return $this->render($template);
    }

    /**
     *  @Route("/config/createuser", name="CreazioneUtente")
     */
    public function createuser(Request $request, KernelInterface $kernel) {

        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'App:CreateUser',
            'divousername' => $request->get('username'),
            'divouserpassword' => $request->get('password'),
            'divouseremail' => $request->get('email'),
            'rtusername' => $request->get('rtusername'),
            'rtuserpassword' => $request->get('rtuserpassword'),
        ]);

        // You can use NullOutput() if you don't need the output
        $output = new BufferedOutput();
        $application->run($input, $output);

        // return the output, don't use if you used NullOutput()
        $content = $output->fetch();

        $response = array(
            'status' => '200',
            'msg' => $content,
        );
        return new \Symfony\Component\HttpFoundation\JsonResponse($response);
    }

}
