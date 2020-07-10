<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\Rxscrutinicandidati
 *
 * @ORM\Entity()
 * @ORM\Table(name="Rxscrutinicandidati", indexes={@ORM\Index(name="fk_Scrutinicandidati_Rxsezioni1_idx", columns={"rxsezione_id"}), @ORM\Index(name="fk_Scrutinicandidati_Candidatiprincipali1_idx", columns={"candidato_principale_id"}), @ORM\Index(name="fk_Rxscrutinicandidati_Actionlogs1_idx", columns={"actionlogs_id"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"base":"BaseRxscrutinicandidati", "extended":"Rxscrutinicandidati"})
 */
class BaseRxscrutinicandidati
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
    protected $candidato_principale_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $voti_totale_candidato;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $voti_dicui_solo_candidato;

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
     * @ORM\ManyToOne(targetEntity="Rxsezioni", inversedBy="rxscrutinicandidatis")
     * @ORM\JoinColumn(name="rxsezione_id", referencedColumnName="id", nullable=false)
     */
    protected $rxsezioni;

    /**
     * @ORM\ManyToOne(targetEntity="Candidatiprincipali", inversedBy="rxscrutinicandidatis")
     * @ORM\JoinColumn(name="candidato_principale_id", referencedColumnName="id", nullable=false)
     */
    protected $candidatiprincipali;

    /**
     * @ORM\ManyToOne(targetEntity="Actionlogs", inversedBy="rxscrutinicandidatis")
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
     * @return \App\Entity\Rxscrutinicandidati
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
     * @return \App\Entity\Rxscrutinicandidati
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
     * Set the value of candidato_principale_id.
     *
     * @param integer $candidato_principale_id
     * @return \App\Entity\Rxscrutinicandidati
     */
    public function setCandidatoPrincipaleId($candidato_principale_id)
    {
        $this->candidato_principale_id = $candidato_principale_id;

        return $this;
    }

    /**
     * Get the value of candidato_principale_id.
     *
     * @return integer
     */
    public function getCandidatoPrincipaleId()
    {
        return $this->candidato_principale_id;
    }

    /**
     * Set the value of voti_totale_candidato.
     *
     * @param integer $voti_totale_candidato
     * @return \App\Entity\Rxscrutinicandidati
     */
    public function setVotiTotaleCandidato($voti_totale_candidato)
    {
        $this->voti_totale_candidato = $voti_totale_candidato;

        return $this;
    }

    /**
     * Get the value of voti_totale_candidato.
     *
     * @return integer
     */
    public function getVotiTotaleCandidato()
    {
        return $this->voti_totale_candidato;
    }

    /**
     * Set the value of voti_dicui_solo_candidato.
     *
     * @param integer $voti_dicui_solo_candidato
     * @return \App\Entity\Rxscrutinicandidati
     */
    public function setVotiDicuiSoloCandidato($voti_dicui_solo_candidato)
    {
        $this->voti_dicui_solo_candidato = $voti_dicui_solo_candidato;

        return $this;
    }

    /**
     * Get the value of voti_dicui_solo_candidato.
     *
     * @return integer
     */
    public function getVotiDicuiSoloCandidato()
    {
        return $this->voti_dicui_solo_candidato;
    }

    /**
     * Set the value of off.
     *
     * @param boolean $off
     * @return \App\Entity\Rxscrutinicandidati
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
     * @return \App\Entity\Rxscrutinicandidati
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
     * @return \App\Entity\Rxscrutinicandidati
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
     * @return \App\Entity\Rxscrutinicandidati
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
     * @return \App\Entity\Rxscrutinicandidati
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
     * @return \App\Entity\Rxscrutinicandidati
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
     * Set Candidatiprincipali entity (many to one).
     *
     * @param \App\Entity\Candidatiprincipali $candidatiprincipali
     * @return \App\Entity\Rxscrutinicandidati
     */
    public function setCandidatiprincipali(Candidatiprincipali $candidatiprincipali = null)
    {
        $this->candidatiprincipali = $candidatiprincipali;

        return $this;
    }

    /**
     * Get Candidatiprincipali entity (many to one).
     *
     * @return \App\Entity\Candidatiprincipali
     */
    public function getCandidatiprincipali()
    {
        return $this->candidatiprincipali;
    }

    /**
     * Set Actionlogs entity (many to one).
     *
     * @param \App\Entity\Actionlogs $actionlogs
     * @return \App\Entity\Rxscrutinicandidati
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
        return array('id', 'rxsezione_id', 'candidato_principale_id', 'voti_totale_candidato', 'voti_dicui_solo_candidato', 'off', 'timestamp', 'sent', 'ins_date', 'actionlogs_id');
    }
}