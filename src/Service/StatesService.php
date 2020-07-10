<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Exception\ClientException;

use App\Service\ORMManager;
use App\Service\RTServicesProvider;

use App\Entity\States;

class StatesService {

    //PUBLIC CONSTS
    //entity types
    public const ENT_SECTION = 'RxSezioni'; //*
    public const ENT_EVENT = 'Eventi'; //*
    //initial stages
    public const STATUS_START = 'START'; //*
    public const STATUS_READY = 'READY'; //*

    //PRIVATE
    //states
    private $st_get_communications;
    //cap affluences
    private $st_affluences;
    private $st_affluences_final;
    //cap scrutini
    private $st_pool;
    //cap preferenze
    private $st_preferences;

    private $affMap;


    public function __construct(ORMmanager $ORMmanager, RTServicesProvider $serviceProvider) {
        $this->ORMmanager = $ORMmanager;
        $this->serviceProvider = $serviceProvider;
        //setup fixed values
        $this->st_get_communications = 'GET_COMMUNICATIONS';
        $this->st_affluences = 'POST_AFFLUENCE';
        $this->st_pool = 'POST_POLL';
        $this->st_preferences = 'POST_PREFERENCES';
        //load other configurations
        $this->affMap = $this->buildAffluencesMap();
    }

    /**
     * It reads data of .conf file where it contains the list of states forecast for affluences stages.
     * For each state takes the updatable next state when you manage affluences deliveries.
     */
    private function buildAffluencesMap(): array
    {
        $descrEntity = StatesService::ENT_SECTION;
        $comm_array = array();
        $comm_matrix = array();
        $comm_array = explode(',', getenv('RT_AFF_STATES'));
        $size = count($comm_array);
        $i = 0;
        foreach($comm_array as $comm_code) {
            $i++;
            if ($i != $size) {
                $nextStateCode = $this->getNextStateByCode($comm_code, $descrEntity);
                $recordState = $this->getUpdatableState($descrEntity, $comm_code, $nextStateCode);
                $comm_matrix[$comm_code] = $recordState;
            }
            else {
                $recordState = $this->getStateRecord($comm_code, $descrEntity);
                $comm_matrix[$comm_code] = $recordState;
                $this->st_affluences_final = $comm_code;
            }
        }
        return $comm_matrix;
    }

    public function getMapAffluences() 
    {
        return $this->affMap;
    }

    /**
     * Return the status to be not surpassed when you manage pools
     */
    public function getCapPools() 
    {
        return $this->st_pool;
    }

    /**
     * Return the status to be not surpassed when you manage preferences
     */
    public function getCapPreferences() 
    {
        return $this->st_preferences;
    }

    /**
     * Return the status to be not surpassed when you manage affluences
     */
    public function getCapAffluences($descrentity) 
    {
        $outcome = '';
        if ($descrentity == StatesService::ENT_SECTION) {
            $outcome = $this->st_affluences_final;
        }
        else {
            $outcome = $this->st_affluences;
        }
        return $outcome;
    }

     /**
     * Check if the code $codeState is valid
     * 
     */
    private function checkStateIsValid($codeState, $descrentity)
    {
        $states=$this->getAllState($descrentity);
        foreach($states as $state){
            if($state->getCode()==$codeState) return true;
        }
        return false;            
    }

    /**
     * Return table statesxgrant record where current is $actualState and next is $newState
     * 
     */
    private function getGrantStateAuthorized($acutalState, $nextState,$descrentity)
    {
        $parameters=['current'=>$acutalState, 'next'=>$nextState, 'entity_ref'=>$descrentity];
        $grant = $this->ORMmanager->popActiveEntity( $this->serviceProvider->getSeedStatesxgrant(),$parameters,[]);
        return $grant; 
    }

    /**
     *
     *  Get StatoWf for $Entity 
     * 
     */
    public function getActualState($Entity)
    {
        return $Entity->getStatoWf();
    }

    /**
     * Return the label of status that given entity has
     */
    public function getActualStateDesc($entity,$descrentity ): string
    {
        $descr = '---';
        $status = $this->ORMmanager->popActiveEntity( $this->serviceProvider->getSeedStates() , [
            'code' => $entity->getStatoWf(), 'entity_ref'=>$descrentity
        ] );
        if  (isset($status)) {
            $descr = $status->getDescr();
        }
        return $descr;
    }


     /**
     *
     *  Set StatoWf for $Entity 
     * 
     */
    public function setActualState($Entity,$newState)
    {
        return $Entity->setStatoWf($newState);
    }


    
     /**
     * 
     * Get NextState for $codeState 
     * 
     */
    public function getNextStateByCode($codeState,$descrentity)
    {
        $parameters=['code'=>$codeState, 'entity_ref'=>$descrentity];
        $recordsState=$this->serviceProvider->getSeedStates();
        $recodbycode = $this->ORMmanager->popActiveEntity($recordsState,$parameters,[]);

        if(!$recodbycode){
             return false;
        }
        return $recodbycode->getNextstate();
      
    }

     /**
     * 
     * Get PreviousState for $codeState  
     * 
     */
    public function getPreviousState($codeState,$descrentity)
    {
        $parameters=['nextstate'=>$codeState, 'entity_ref'=>$descrentity];
        $recordsState=$this->serviceProvider->getSeedStates();
        $recodbycode = $this->ORMmanager->popActiveEntity($recordsState,$parameters,[]);

        if(!$recodbycode){
             return false;
        }
        return $recodbycode->getCode();
      
    }

    /**
     * It returns the instance of States associated to the given code
     */
    private function getStateRecord($codeState, $descrentity): ?States 
    {
        $parameters = ['code'=>$codeState, 'entity_ref'=>$descrentity];
        $record = $this->ORMmanager->popActiveEntity($this->serviceProvider->getSeedStates(), $parameters,[]);
        return $record;
    }



    /**
     * 
     * Get NextState for $Entity 
     * 
     */
    public function getNextState($Entity,$descrentity)
    {
        return $this->getNextStateByCode($this->getActualState($Entity),$descrentity);
    }


    /**
     * 
     * Get All states for by Entity 
     * 
     */
    public function getAllState($descrentity)
    {
        $parameters=['entity_ref'=>$descrentity];
        $recordsState=$this->serviceProvider->getSeedStates();
        $recordsState = $this->ORMmanager->getActiveEntityObjects($recordsState,$parameters,[]);

        if(!$recordsState){
             return false;
        }
        return $recordsState;
    }

    /**
     * 
     * Check if exists one entity $descrentity with nextstate  valid 
     * 
     */
    public function getIfExists($nextstate,$descrentity)
    {

        $serviceUser = $this->ORMmanager->getServiceUser();
        $ente_id=$serviceUser->getEnti()->getId();
        $param_filter=['ente_id' => $ente_id];
        $param_order= [];
        $serviceURLEneteEventi = $this->serviceProvider->getSeedEnteEvento();
        $listEnteEventi = $this->ORMmanager->getActiveEntityObjects($serviceURLEneteEventi,$param_filter,$param_order);
        $previous= $this->getPreviousState($nextstate, $descrentity); 
       
        switch($descrentity){
            case 'Eventi': 
                      
                         foreach($listEnteEventi as $entxevent){
                            $event= $entxevent->getEventi();
            
                           
                            if($this->getNextState($event, $descrentity)==$nextstate){
                                return true;
                            }
                         }

                         break;
            case 'RxSezioni':
     
                        foreach($listEnteEventi as $entxevent){
                            $class='App\Entity\\'.$descrentity; 
                            $param_filter=['evento_id' => $entxevent->getEventi()->getId()];
                            $param_order= [];
                            $listRxCircoscrizioni = $this->serviceProvider->getSeedCircoscrizioni();
                            $listRxCircoscrizioni = $this->ORMmanager->getActiveEntityObjects($listRxCircoscrizioni,$param_filter,$param_order);
                            foreach($listRxCircoscrizioni as $circ){
                                $circoId=$circ->getID();
                           
                                $qb = $this->ORMmanager->getManager()->createQueryBuilder();
                      
                                $qb->select('count(e.id)')
                                        ->from($class,'e')
                                        ->where('e.circo_id = ?1 and e.stato_wf=?2')
                                        ->setParameter('1',$circoId)
                                        ->setParameter('2',$previous);
                                $count = $qb->getQuery()->getSingleScalarResult();;
                                if($count>0) return true;
                            }
                        }
                           
                        break;
            default: return false; break;
        }
            
        return false;
       
    }

    /**
     * Update StatoEvento for $Event with $newState 
     * Return -1 if the status cannot be updated
     *         1 if the status has been updated
     *         2 if the state can be forced
     * 
     */
    public function updateState($Entity,$descrEntity, $newState,$force=false)
    {
        $actualState=$this->getActualState($Entity);
        $valid=$this->checkStateIsValid($newState,$descrEntity);
      
        if(isset($actualState) && $valid){
        
            $grant=$this->getGrantStateAuthorized($actualState,$newState,$descrEntity);  
           
            if(!isset($grant)) return -1;
            if($grant->getEnabled() or ($grant->getCrackable() & $force)){
                    $this->setActualState($Entity,$newState) ;   
                    return 1;
            }
            if($grant->getCrackable()){
                return 2;
            }
        }
        return -1;
    }

    /**
     * Return the state that could be given current state
     */
    private function getUpdatableState($descrEntity, $actualState, $newState): ?States
    {
        $valid=$this->checkStateIsValid($newState, $descrEntity);
        $nextStateRecord = null;
        if (isset($actualState) && $valid){
            $grant=$this->getGrantStateAuthorized($actualState, $newState, $descrEntity);             
            if (isset($grant)) {
                if ($grant->getEnabled() or $grant->getCrackable() ){
                    $nextStateRecord = $this->getStateRecord($newState, $descrEntity) ;   
                }
            }
        }
        return $nextStateRecord;
    }

    /**
     * Move the given entity to the next state toward the workflow
     */
    public function moveNextState($entity, $descrEntity) 
    {
        $nextState = $this->getNextState($entity,$descrEntity);
        $this->updateState($entity, $descrEntity, $nextState);
    }


}
