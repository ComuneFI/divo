<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\BaseListapreferenze;

use App\Service\RTServicesProvider;

/**
 * App\Entity\Listapreferenze
 *
 * @ORM\Entity()
 */
class Listapreferenze extends BaseListapreferenze
{

    //the field key from circoxcandidato table to candidatiprincipali
    private const ENT_CANDIDATE_KEY = 'candidato_principale_id';
    private const ENT_LIST_KEY = 'lista_id';

    /**
     * It return a number that is the position of list inside them that are supporting a main candidate.
     * It requires an ORMmanager reference in order to return the value.
     * It requires a main Candidate Id that it's supporting for.
     */
    public function getPosizione( $ORMmanager, $candidateId = null ) 
    {
        $filters = array();
        $filters[Listapreferenze::ENT_LIST_KEY] = $this->getId();
        if (isset($candidateId)) {
            $filters[Listapreferenze::ENT_CANDIDATE_KEY] = $candidateId;
        }
        $entity = $ORMmanager->getActiveEntityPop( RTServicesProvider::ENT_LISTA_PRINCIPALE, $filters );
        return $entity->getPosizione();
    }

}