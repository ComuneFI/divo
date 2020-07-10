<?php

/**
 * This is the class utility responsible to wrap REST API request management and
 * interact with needed services.
 */

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Validator\Constraints\DateTime;
use Doctrine\ORM\EntityManagerInterface;
use ArrayObject;
use App\Entity\Actionlogs;
use App\Tool\LogTracker;

use Symfony\Component\HttpClient\Exception\ClientException;

class AppProxyRest {

    private $logtracker;

    public function __construct(LogTracker $logtracker) {
        $this->logtracker = $logtracker;
    }

    /**
     * Performs a POST request, tracking response/request.
     * Return an array composed by 2 elements:
     * - key = the identifier of action log
     * - json = the response of requested service
     */
    public function doPOST($serviceURL, array $array): array 
    {
        $client = HttpClient::create();
        //perform the request API REST POST method
        $response = $client->request('POST', $serviceURL, [
            'json' => $array,
        ]);

        $content = $response->getContent();

        //Create a new logTracker in order to track results of this operation
        return $this->track($serviceURL, $content, $array);
    }


    /**
     * Performs a GET request, tracking response/request.
     */
    public function doGET($serviceURL) {
        $client = HttpClient::create();
        //perform the request API REST
        $response = $client->request('GET', $serviceURL);
        //$contentType = $response->getHeaders()['content-type'][0];
        //in case of JSON it will return: $contentType = 'application/json'
        //for the moment we know that services are JSON and we collect the content
        //it should be to manage errors, failures, faults etc..
        $content = $response->getContent();
        //prepare the response to allocate instances of PHP classes
        //Create a new logTracker in order to track results of this operation
        //Create a new logTracker in order to track results of this operation
        return self::track($serviceURL, $content, $array);
    }

    /**
     * It tracks into action-logs what happens with this request
     */
    private function track($serviceURL, $content, $array) 
    {
        $log = $this->logtracker->fill($serviceURL, $content, $array);
        $response = $this->logtracker->trackAction($log);
        return $response;
    }

}
