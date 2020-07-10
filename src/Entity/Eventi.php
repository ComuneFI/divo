<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\BaseEventi;

use App\Service\StatesService;

/**
 * App\Entity\Eventi
 *
 * @ORM\Entity()
 */
class Eventi extends BaseEventi
{

    protected $statoWfDesc;


    /**
     * Set initial status for this item
     */
    public function __construct()
    {
        parent::__construct();
        $this->setStatoWf(StatesService::STATUS_START);
    }

    /**
     * Compute and archive a description related to the event status
     */
    public function storeStatoWfDesc(StatesService $wfService)
    {
        $desc = $wfService->getActualStateDesc($this, StatesService::ENT_EVENT);
        $this->statoWfDesc = $desc;
    }

    /**
     * Return the archived description for the event status
     */
    public function getStatoWfDesc() 
    {
        return $this->statoWfDesc;
    } 

    /**
     * Set the description for the event status
     */
    public function setStatoWfDesc(string $statoWfDesc) 
    {
        $this->statoWfDesc = $statoWfDesc;
    }
        
    
    /**
     * Get Circoscrizioni entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActiveCircoscrizionis()
    {
        $circoscrizioni = $this->getCircoscrizionis();
        $array = array();
        foreach ($circoscrizioni as $circoscrizione) {
            if (!$circoscrizione->getOff()) {
                array_push($array, $circoscrizione);
            }
        }
        return $array;
    } 



}