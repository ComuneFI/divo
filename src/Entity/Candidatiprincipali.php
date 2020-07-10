<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\BaseCandidatiprincipali;

use App\Service\RTServicesProvider;

/**
 * App\Entity\Candidatiprincipali
 *
 * @ORM\Entity()
 */
class Candidatiprincipali extends BaseCandidatiprincipali
{

    //the field key from circoxcandidato table to candidatiprincipali
    private const ENT_CIRCO_CANDIDATO_KEY = 'candidato_principale_id';
    private const ENT_CIRCO_KEY = 'circ_id';

    /**
     * Accepts $candidato that is an array of data.
     * It initializes a candidate for cognome, nome, sesso, luogoNascita, Id Target.
     */
    public function createFromTarget( $candidato ) {
        $this->setCognome( $candidato->cognome );
        $this->setNome( $candidato->nome );
        $this->setSesso( $candidato->sesso);
        $this->setLuogoNascita( $candidato->luogoNascita);
        $this->setIdTarget( $candidato->id );
    }

    /**
     * It return a number that is the position of main candidates inside the Circoscrizione.
     * It requires an ORMmanager reference in order to return the value.
     * It requires the circoscrizione Id where this main candidate is competing.
     */
    public function getPosizione( $ORMmanager, $circoId ) {
        $entity = $ORMmanager->getActiveEntityPop( RTServicesProvider::ENT_CIRCO_CANDIDATO, [
            Candidatiprincipali::ENT_CIRCO_CANDIDATO_KEY => $this->getId(),
            Candidatiprincipali::ENT_CIRCO_KEY => $circoId,
        ]);
        return $entity->getPosizione();
    }

}