<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\BaseRxsezioni;

use App\Service\StatesService;

/**
 * App\Entity\Rxsezioni
 *
 * @ORM\Entity()
 */
class Rxsezioni extends BaseRxsezioni
{
    protected $statoWfDesc;

    /**
     * Compute and archive a description related to the event status
     */
    public function storeStatoWfDesc(StatesService $wfService)
    {
        $desc = $wfService->getActualStateDesc($this, StatesService::ENT_SECTION);
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
     * Set initial status for this item
     */
    public function __construct()
    {
        parent::__construct();
        $this->setStatoWf(StatesService::STATUS_READY);
    }

}