<?php

namespace App\Tool;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Actionlogs;
use App\Service\ORMManager;

class LogTracker {

    protected $action;

    public function __construct(ORMManager $ormmanager) {
        $this->ORMManager = $ormmanager;
        
    }

    public function fill($serviceURL, $json_response, $array_payload): Actionlogs 
    {
        $action = new Actionlogs();
        $phpobj = json_decode($json_response);
        $action->setCodiceEsito( $phpobj->esito->codice );
        $action->setDescrizioneEsito( $phpobj->esito->descrizione );
        $action->setRequestedws( $serviceURL );
        $action->setResponseMessage( $json_response );
        $action->setPayload( json_encode($array_payload) );
        $action->setTimestamp( new \DateTime("now") );
        return $action;
    }

    /**
     * @Route("/track", name="trackLog")
     */
    public function trackAction(Actionlogs $log): array 
    { 
        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $em = $this->ORMManager->getManager();

        $em->persist($log);
        $key = $log->getId();
        // actually executes the queries (i.e. the INSERT query)
        $em->flush();

        $return = [
            'key' => $log->getId(),
            'json' => json_decode($log->getResponseMessage()),
        ];

        return $return;
    }

}