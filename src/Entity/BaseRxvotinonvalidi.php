<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\Rxvotinonvalidi
 *
 * @ORM\Entity()
 * @ORM\Table(name="Rxvotinonvalidi", indexes={@ORM\Index(name="fk_Votinonvalidi_Rxsezioni1_idx", columns={"rxsezione_id"}), @ORM\Index(name="fk_Rxvotinonvalidi_Actionlogs1_idx", columns={"actionlogs_id"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"base":"BaseRxvotinonvalidi", "extended":"Rxvotinonvalidi"})
 */
class BaseRxvotinonvalidi
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
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $numero_schede_bianche;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $numero_schede_nulle;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $numero_schede_contestate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $tot_voti_dicui_solo_candidato;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $voti_nulli_liste;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $voti_nulli_coalizioni;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $voti_contestati_liste;

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
     * @ORM\ManyToOne(targetEntity="Rxsezioni", inversedBy="rxvotinonvalidis")
     * @ORM\JoinColumn(name="rxsezione_id", referencedColumnName="id", nullable=false)
     */
    protected $rxsezioni;

    /**
     * @ORM\ManyToOne(targetEntity="Actionlogs", inversedBy="rxvotinonvalidis")
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
     * @return \App\Entity\Rxvotinonvalidi
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
     * @return \App\Entity\Rxvotinonvalidi
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
     * Set the value of numero_schede_bianche.
     *
     * @param integer $numero_schede_bianche
     * @return \App\Entity\Rxvotinonvalidi
     */
    public function setNumeroSchedeBianche($numero_schede_bianche)
    {
        $this->numero_schede_bianche = $numero_schede_bianche;

        return $this;
    }

    /**
     * Get the value of numero_schede_bianche.
     *
     * @return integer
     */
    public function getNumeroSchedeBianche()
    {
        return $this->numero_schede_bianche;
    }

    /**
     * Set the value of numero_schede_nulle.
     *
     * @param integer $numero_schede_nulle
     * @return \App\Entity\Rxvotinonvalidi
     */
    public function setNumeroSchedeNulle($numero_schede_nulle)
    {
        $this->numero_schede_nulle = $numero_schede_nulle;

        return $this;
    }

    /**
     * Get the value of numero_schede_nulle.
     *
     * @return integer
     */
    public function getNumeroSchedeNulle()
    {
        return $this->numero_schede_nulle;
    }

    /**
     * Set the value of numero_schede_contestate.
     *
     * @param integer $numero_schede_contestate
     * @return \App\Entity\Rxvotinonvalidi
     */
    public function setNumeroSchedeContestate($numero_schede_contestate)
    {
        $this->numero_schede_contestate = $numero_schede_contestate;

        return $this;
    }

    /**
     * Get the value of numero_schede_contestate.
     *
     * @return integer
     */
    public function getNumeroSchedeContestate()
    {
        return $this->numero_schede_contestate;
    }

    /**
     * Set the value of tot_voti_dicui_solo_candidato.
     *
     * @param integer $tot_voti_dicui_solo_candidato
     * @return \App\Entity\Rxvotinonvalidi
     */
    public function setTotVotiDicuiSoloCandidato($tot_voti_dicui_solo_candidato)
    {
        $this->tot_voti_dicui_solo_candidato = $tot_voti_dicui_solo_candidato;

        return $this;
    }

    /**
     * Get the value of tot_voti_dicui_solo_candidato.
     *
     * @return integer
     */
    public function getTotVotiDicuiSoloCandidato()
    {
        return $this->tot_voti_dicui_solo_candidato;
    }

    /**
     * Set the value of voti_nulli_liste.
     *
     * @param integer $voti_nulli_liste
     * @return \App\Entity\Rxvotinonvalidi
     */
    public function setVotiNulliListe($voti_nulli_liste)
    {
        $this->voti_nulli_liste = $voti_nulli_liste;

        return $this;
    }

    /**
     * Get the value of voti_nulli_liste.
     *
     * @return integer
     */
    public function getVotiNulliListe()
    {
        return $this->voti_nulli_liste;
    }

    /**
     * Set the value of voti_nulli_coalizioni.
     *
     * @param integer $voti_nulli_coalizioni
     * @return \App\Entity\Rxvotinonvalidi
     */
    public function setVotiNulliCoalizioni($voti_nulli_coalizioni)
    {
        $this->voti_nulli_coalizioni = $voti_nulli_coalizioni;

        return $this;
    }

    /**
     * Get the value of voti_nulli_coalizioni.
     *
     * @return integer
     */
    public function getVotiNulliCoalizioni()
    {
        return $this->voti_nulli_coalizioni;
    }

    /**
     * Set the value of voti_contestati_liste.
     *
     * @param integer $voti_contestati_liste
     * @return \App\Entity\Rxvotinonvalidi
     */
    public function setVotiContestatiListe($voti_contestati_liste)
    {
        $this->voti_contestati_liste = $voti_contestati_liste;

        return $this;
    }

    /**
     * Get the value of voti_contestati_liste.
     *
     * @return integer
     */
    public function getVotiContestatiListe()
    {
        return $this->voti_contestati_liste;
    }

    /**
     * Set the value of off.
     *
     * @param boolean $off
     * @return \App\Entity\Rxvotinonvalidi
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
     * @return \App\Entity\Rxvotinonvalidi
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
     * @return \App\Entity\Rxvotinonvalidi
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
     * @return \App\Entity\Rxvotinonvalidi
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
     * @return \App\Entity\Rxvotinonvalidi
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
     * @return \App\Entity\Rxvotinonvalidi
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
     * Set Actionlogs entity (many to one).
     *
     * @param \App\Entity\Actionlogs $actionlogs
     * @return \App\Entity\Rxvotinonvalidi
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
        return array('id', 'rxsezione_id', 'numero_schede_bianche', 'numero_schede_nulle', 'numero_schede_contestate', 'tot_voti_dicui_solo_candidato', 'voti_nulli_liste', 'voti_nulli_coalizioni', 'voti_contestati_liste', 'off', 'timestamp', 'sent', 'ins_date', 'actionlogs_id');
    }
}