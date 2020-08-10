<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\BaseCandidatisecondari;

use App\Service\RTServicesProvider; 

/**
 * App\Entity\Candidatisecondari
 *
 * @ORM\Entity()
 */
class Candidatisecondari extends BaseCandidatisecondari
{

    //the field key from circoxcandidato table to candidatiprincipali
    private const ENT_LISTA_CANDIDATO_KEY = 'candidato_secondario_id';
    private const ENT_LISTA_KEY = 'lista_id';

    /**
     * Accepts $candidatoPreferenze that is an array of data.
     * It initializes a candidate (secondary) for cognome, nome, sesso, luogoNascita, Id Target.
     */
    function createFromTarget( $candidatoPreferenze ) {
        $this->setIdTarget( $candidatoPreferenze->id );
        $this->setCognome( $candidatoPreferenze->cognome );
        $this->setNome( $candidatoPreferenze->nome );
        $this->setSesso( $candidatoPreferenze->sesso);
        $this->setLuogoNascita( $candidatoPreferenze->luogoNascita);
        $this->setIndipendente( $candidatoPreferenze->indipendente);
    }

    /**
     * It return a number that is the position of secondary candidates inside the List.
     * It requires an ORMmanager reference in order to return the value.
     * It requires the Lista Id where this main candidate is subscribed.
     */
    public function getPosizione( $ORMmanager, RTServicesProvider $provider, $listaId ) 
    {
        $entity = $ORMmanager->popActiveEntity($provider->getSeedSecondarioLista() , [
            Candidatisecondari::ENT_LISTA_CANDIDATO_KEY => $this->getId(),
            Candidatisecondari::ENT_LISTA_KEY => $listaId,
        ]);
        return $entity->getPosizione();
    }

}