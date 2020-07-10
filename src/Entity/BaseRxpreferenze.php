<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\Rxpreferenze
 *
 * @ORM\Entity()
 * @ORM\Table(name="Rxpreferenze", indexes={@ORM\Index(name="fk_Rxpreferenze_Rxsezioni1_idx", columns={"rxsezione_id"}), @ORM\Index(name="fk_Rxpreferenze_Listapreferenze1_idx", columns={"listapreferenze_id"}), @ORM\Index(name="fk_Rxpreferenze_Candidatisecondari1_idx", columns={"candidato_secondario_id"}), @ORM\Index(name="fk_Rxpreferenze_Actionlogs1_idx", columns={"actionlogs_id"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"base":"BaseRxpreferenze", "extended":"Rxpreferenze"})
 */
class BaseRxpreferenze
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="`off`", type="boolean", nullable=true)
     */
    protected $off;

    /**
     * @ORM\Column(name="`timestamp`", type="datetime", nullable=true)
     */
    protected $timestamp;

    /**
     * @ORM\Column(type="integer")
     */
    protected $rxsezione_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $listapreferenze_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $candidato_secondario_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $numero_voti;

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
     * @ORM\ManyToOne(targetEntity="Rxsezioni", inversedBy="rxpreferenzes")
     * @ORM\JoinColumn(name="rxsezione_id", referencedColumnName="id", nullable=false)
     */
    protected $rxsezioni;

    /**
     * @ORM\ManyToOne(targetEntity="Listapreferenze", inversedBy="rxpreferenzes")
     * @ORM\JoinColumn(name="listapreferenze_id", referencedColumnName="id", nullable=false)
     */
    protected $listapreferenze;

    /**
     * @ORM\ManyToOne(targetEntity="Candidatisecondari", inversedBy="rxpreferenzes")
     * @ORM\JoinColumn(name="candidato_secondario_id", referencedColumnName="id", nullable=false)
     */
    protected $candidatisecondari;

    /**
     * @ORM\ManyToOne(targetEntity="Actionlogs", inversedBy="rxpreferenzes")
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
     * @return \App\Entity\Rxpreferenze
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
     * Set the value of off.
     *
     * @param boolean $off
     * @return \App\Entity\Rxpreferenze
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
     * @return \App\Entity\Rxpreferenze
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
     * Set the value of rxsezione_id.
     *
     * @param integer $rxsezione_id
     * @return \App\Entity\Rxpreferenze
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
     * Set the value of listapreferenze_id.
     *
     * @param integer $listapreferenze_id
     * @return \App\Entity\Rxpreferenze
     */
    public function setListapreferenzeId($listapreferenze_id)
    {
        $this->listapreferenze_id = $listapreferenze_id;

        return $this;
    }

    /**
     * Get the value of listapreferenze_id.
     *
     * @return integer
     */
    public function getListapreferenzeId()
    {
        return $this->listapreferenze_id;
    }

    /**
     * Set the value of candidato_secondario_id.
     *
     * @param integer $candidato_secondario_id
     * @return \App\Entity\Rxpreferenze
     */
    public function setCandidatoSecondarioId($candidato_secondario_id)
    {
        $this->candidato_secondario_id = $candidato_secondario_id;

        return $this;
    }

    /**
     * Get the value of candidato_secondario_id.
     *
     * @return integer
     */
    public function getCandidatoSecondarioId()
    {
        return $this->candidato_secondario_id;
    }

    /**
     * Set the value of numero_voti.
     *
     * @param integer $numero_voti
     * @return \App\Entity\Rxpreferenze
     */
    public function setNumeroVoti($numero_voti)
    {
        $this->numero_voti = $numero_voti;

        return $this;
    }

    /**
     * Get the value of numero_voti.
     *
     * @return integer
     */
    public function getNumeroVoti()
    {
        return $this->numero_voti;
    }

    /**
     * Set the value of sent.
     *
     * @param integer $sent
     * @return \App\Entity\Rxpreferenze
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
     * @return \App\Entity\Rxpreferenze
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
     * @return \App\Entity\Rxpreferenze
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
     * @return \App\Entity\Rxpreferenze
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
     * Set Listapreferenze entity (many to one).
     *
     * @param \App\Entity\Listapreferenze $listapreferenze
     * @return \App\Entity\Rxpreferenze
     */
    public function setListapreferenze(Listapreferenze $listapreferenze = null)
    {
        $this->listapreferenze = $listapreferenze;

        return $this;
    }

    /**
     * Get Listapreferenze entity (many to one).
     *
     * @return \App\Entity\Listapreferenze
     */
    public function getListapreferenze()
    {
        return $this->listapreferenze;
    }

    /**
     * Set Candidatisecondari entity (many to one).
     *
     * @param \App\Entity\Candidatisecondari $candidatisecondari
     * @return \App\Entity\Rxpreferenze
     */
    public function setCandidatisecondari(Candidatisecondari $candidatisecondari = null)
    {
        $this->candidatisecondari = $candidatisecondari;

        return $this;
    }

    /**
     * Get Candidatisecondari entity (many to one).
     *
     * @return \App\Entity\Candidatisecondari
     */
    public function getCandidatisecondari()
    {
        return $this->candidatisecondari;
    }

    /**
     * Set Actionlogs entity (many to one).
     *
     * @param \App\Entity\Actionlogs $actionlogs
     * @return \App\Entity\Rxpreferenze
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
        return array('id', 'off', 'timestamp', 'rxsezione_id', 'listapreferenze_id', 'candidato_secondario_id', 'numero_voti', 'sent', 'ins_date', 'actionlogs_id');
    }
}