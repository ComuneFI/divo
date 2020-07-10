<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\Rxvotanti
 *
 * @ORM\Entity()
 * @ORM\Table(name="Rxvotanti", indexes={@ORM\Index(name="fk_Rxvotanti_Rxsezioni1_idx", columns={"rxsezione_id"}), @ORM\Index(name="fk_Rxvotanti_Confxvotanti1_idx", columns={"confxvotanti_id"}), @ORM\Index(name="fk_Rxvotanti_Actionlogs1_idx", columns={"actionlogs_id"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"base":"BaseRxvotanti", "extended":"Rxvotanti"})
 */
class BaseRxvotanti
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $rxsezione_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $confxvotanti_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $num_votanti_maschi;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $num_votanti_femmine;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $num_votanti_totali;

    /**
     * @ORM\Column(name="`off`", type="boolean", nullable=true)
     */
    protected $off;

    /**
     * @ORM\Column(name="`timestamp`", type="datetime", nullable=true)
     */
    protected $timestamp;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    protected $sent;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $ins_date;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $actionlogs_id;

    /**
     * @ORM\ManyToOne(targetEntity="Rxsezioni", inversedBy="rxvotantis")
     * @ORM\JoinColumn(name="rxsezione_id", referencedColumnName="id", nullable=false)
     */
    protected $rxsezioni;

    /**
     * @ORM\ManyToOne(targetEntity="Confxvotanti", inversedBy="rxvotantis")
     * @ORM\JoinColumn(name="confxvotanti_id", referencedColumnName="id", nullable=false)
     */
    protected $confxvotanti;

    /**
     * @ORM\ManyToOne(targetEntity="Actionlogs", inversedBy="rxvotantis")
     * @ORM\JoinColumn(name="actionlogs_id", referencedColumnName="id")
     */
    protected $actionlogs;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Rxvotanti
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of rxsezione_id.
     *
     * @param integer $rxsezione_id
     * @return \App\Entity\Rxvotanti
     */
    public function setRxsezioneId($rxsezione_id)
    {
        $this->rxsezione_id = $rxsezione_id;

        return $this;
    }

    /**
     * Get the value of rxsezione_id.
     *
     * @return integer
     */
    public function getRxsezioneId()
    {
        return $this->rxsezione_id;
    }

    /**
     * Set the value of confxvotanti_id.
     *
     * @param integer $confxvotanti_id
     * @return \App\Entity\Rxvotanti
     */
    public function setConfxvotantiId($confxvotanti_id)
    {
        $this->confxvotanti_id = $confxvotanti_id;

        return $this;
    }

    /**
     * Get the value of confxvotanti_id.
     *
     * @return integer
     */
    public function getConfxvotantiId()
    {
        return $this->confxvotanti_id;
    }

    /**
     * Set the value of num_votanti_maschi.
     *
     * @param integer $num_votanti_maschi
     * @return \App\Entity\Rxvotanti
     */
    public function setNumVotantiMaschi($num_votanti_maschi)
    {
        $this->num_votanti_maschi = $num_votanti_maschi;

        return $this;
    }

    /**
     * Get the value of num_votanti_maschi.
     *
     * @return integer
     */
    public function getNumVotantiMaschi()
    {
        return $this->num_votanti_maschi;
    }

    /**
     * Set the value of num_votanti_femmine.
     *
     * @param integer $num_votanti_femmine
     * @return \App\Entity\Rxvotanti
     */
    public function setNumVotantiFemmine($num_votanti_femmine)
    {
        $this->num_votanti_femmine = $num_votanti_femmine;

        return $this;
    }

    /**
     * Get the value of num_votanti_femmine.
     *
     * @return integer
     */
    public function getNumVotantiFemmine()
    {
        return $this->num_votanti_femmine;
    }

    /**
     * Set the value of num_votanti_totali.
     *
     * @param integer $num_votanti_totali
     * @return \App\Entity\Rxvotanti
     */
    public function setNumVotantiTotali($num_votanti_totali)
    {
        $this->num_votanti_totali = $num_votanti_totali;

        return $this;
    }

    /**
     * Get the value of num_votanti_totali.
     *
     * @return integer
     */
    public function getNumVotantiTotali()
    {
        return $this->num_votanti_totali;
    }

    /**
     * Set the value of off.
     *
     * @param boolean $off
     * @return \App\Entity\Rxvotanti
     */
    public function setOff($off)
    {
        $this->off = $off;

        return $this;
    }

    /**
     * Get the value of off.
     *
     * @return boolean
     */
    public function getOff()
    {
        return $this->off;
    }

    /**
     * Set the value of timestamp.
     *
     * @param \DateTime $timestamp
     * @return \App\Entity\Rxvotanti
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get the value of timestamp.
     *
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Set the value of sent.
     *
     * @param integer $sent
     * @return \App\Entity\Rxvotanti
     */
    public function setSent($sent)
    {
        $this->sent = $sent;

        return $this;
    }

    /**
     * Get the value of sent.
     *
     * @return integer
     */
    public function getSent()
    {
        return $this->sent;
    }

    /**
     * Set the value of ins_date.
     *
     * @param \DateTime $ins_date
     * @return \App\Entity\Rxvotanti
     */
    public function setInsDate($ins_date)
    {
        $this->ins_date = $ins_date;

        return $this;
    }

    /**
     * Get the value of ins_date.
     *
     * @return \DateTime
     */
    public function getInsDate()
    {
        return $this->ins_date;
    }

    /**
     * Set the value of actionlogs_id.
     *
     * @param integer $actionlogs_id
     * @return \App\Entity\Rxvotanti
     */
    public function setActionlogsId($actionlogs_id)
    {
        $this->actionlogs_id = $actionlogs_id;

        return $this;
    }

    /**
     * Get the value of actionlogs_id.
     *
     * @return integer
     */
    public function getActionlogsId()
    {
        return $this->actionlogs_id;
    }

    /**
     * Set Rxsezioni entity (many to one).
     *
     * @param \App\Entity\Rxsezioni $rxsezioni
     * @return \App\Entity\Rxvotanti
     */
    public function setRxsezioni(Rxsezioni $rxsezioni = null)
    {
        $this->rxsezioni = $rxsezioni;

        return $this;
    }

    /**
     * Get Rxsezioni entity (many to one).
     *
     * @return \App\Entity\Rxsezioni
     */
    public function getRxsezioni()
    {
        return $this->rxsezioni;
    }

    /**
     * Set Confxvotanti entity (many to one).
     *
     * @param \App\Entity\Confxvotanti $confxvotanti
     * @return \App\Entity\Rxvotanti
     */
    public function setConfxvotanti(Confxvotanti $confxvotanti = null)
    {
        $this->confxvotanti = $confxvotanti;

        return $this;
    }

    /**
     * Get Confxvotanti entity (many to one).
     *
     * @return \App\Entity\Confxvotanti
     */
    public function getConfxvotanti()
    {
        return $this->confxvotanti;
    }

    /**
     * Set Actionlogs entity (many to one).
     *
     * @param \App\Entity\Actionlogs $actionlogs
     * @return \App\Entity\Rxvotanti
     */
    public function setActionlogs(Actionlogs $actionlogs = null)
    {
        $this->actionlogs = $actionlogs;

        return $this;
    }

    /**
     * Get Actionlogs entity (many to one).
     *
     * @return \App\Entity\Actionlogs
     */
    public function getActionlogs()
    {
        return $this->actionlogs;
    }

    public function __sleep()
    {
        return array('id', 'rxsezione_id', 'confxvotanti_id', 'num_votanti_maschi', 'num_votanti_femmine', 'num_votanti_totali', 'off', 'timestamp', 'sent', 'ins_date', 'actionlogs_id');
    }
}