<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entity\Confxvotanti
 *
 * @ORM\Entity()
 * @ORM\Table(name="Confxvotanti", indexes={@ORM\Index(name="fk_Confxvotanti_Eventi1_idx", columns={"evento_id"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"base":"BaseConfxvotanti", "extended":"Confxvotanti"})
 */
class BaseConfxvotanti
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
    protected $evento_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $comunicazione_desc;

    /**
     * @ORM\Column(type="integer")
     */
    protected $comunicazione_codice;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $comunicazione_final;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $configurazioni;

    /**
     * @ORM\Column(name="`off`", type="boolean", nullable=true)
     */
    protected $off;

    /**
     * @ORM\OneToMany(targetEntity="Rxvotanti", mappedBy="confxvotanti")
     * @ORM\JoinColumn(name="id", referencedColumnName="confxvotanti_id", nullable=false)
     */
    protected $rxvotantis;

    /**
     * @ORM\ManyToOne(targetEntity="Eventi", inversedBy="confxvotantis")
     * @ORM\JoinColumn(name="evento_id", referencedColumnName="id", nullable=false)
     */
    protected $eventi;

    public function __construct()
    {
        $this->rxvotantis = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Confxvotanti
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
     * Set the value of evento_id.
     *
     * @param integer $evento_id
     * @return \App\Entity\Confxvotanti
     */
    public function setEventoId($evento_id)
    {
        $this->evento_id = $evento_id;

        return $this;
    }

    /**
     * Get the value of evento_id.
     *
     * @return integer
     */
    public function getEventoId()
    {
        return $this->evento_id;
    }

    /**
     * Set the value of comunicazione_desc.
     *
     * @param string $comunicazione_desc
     * @return \App\Entity\Confxvotanti
     */
    public function setComunicazioneDesc($comunicazione_desc)
    {
        $this->comunicazione_desc = $comunicazione_desc;

        return $this;
    }

    /**
     * Get the value of comunicazione_desc.
     *
     * @return string
     */
    public function getComunicazioneDesc()
    {
        return $this->comunicazione_desc;
    }

    /**
     * Set the value of comunicazione_codice.
     *
     * @param integer $comunicazione_codice
     * @return \App\Entity\Confxvotanti
     */
    public function setComunicazioneCodice($comunicazione_codice)
    {
        $this->comunicazione_codice = $comunicazione_codice;

        return $this;
    }

    /**
     * Get the value of comunicazione_codice.
     *
     * @return integer
     */
    public function getComunicazioneCodice()
    {
        return $this->comunicazione_codice;
    }

    /**
     * Set the value of comunicazione_final.
     *
     * @param boolean $comunicazione_final
     * @return \App\Entity\Confxvotanti
     */
    public function setComunicazioneFinal($comunicazione_final)
    {
        $this->comunicazione_final = $comunicazione_final;

        return $this;
    }

    /**
     * Get the value of comunicazione_final.
     *
     * @return boolean
     */
    public function getComunicazioneFinal()
    {
        return $this->comunicazione_final;
    }

    /**
     * Set the value of configurazioni.
     *
     * @param string $configurazioni
     * @return \App\Entity\Confxvotanti
     */
    public function setConfigurazioni($configurazioni)
    {
        $this->configurazioni = $configurazioni;

        return $this;
    }

    /**
     * Get the value of configurazioni.
     *
     * @return string
     */
    public function getConfigurazioni()
    {
        return $this->configurazioni;
    }

    /**
     * Set the value of off.
     *
     * @param boolean $off
     * @return \App\Entity\Confxvotanti
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
     * Add Rxvotanti entity to collection (one to many).
     *
     * @param \App\Entity\Rxvotanti $rxvotanti
     * @return \App\Entity\Confxvotanti
     */
    public function addRxvotanti(Rxvotanti $rxvotanti)
    {
        $this->rxvotantis[] = $rxvotanti;

        return $this;
    }

    /**
     * Remove Rxvotanti entity from collection (one to many).
     *
     * @param \App\Entity\Rxvotanti $rxvotanti
     * @return \App\Entity\Confxvotanti
     */
    public function removeRxvotanti(Rxvotanti $rxvotanti)
    {
        $this->rxvotantis->removeElement($rxvotanti);

        return $this;
    }

    /**
     * Get Rxvotanti entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRxvotantis()
    {
        return $this->rxvotantis;
    }

    /**
     * Set Eventi entity (many to one).
     *
     * @param \App\Entity\Eventi $eventi
     * @return \App\Entity\Confxvotanti
     */
    public function setEventi(Eventi $eventi = null)
    {
        $this->eventi = $eventi;

        return $this;
    }

    /**
     * Get Eventi entity (many to one).
     *
     * @return \App\Entity\Eventi
     */
    public function getEventi()
    {
        return $this->eventi;
    }

    public function __sleep()
    {
        return array('id', 'evento_id', 'comunicazione_desc', 'comunicazione_codice', 'comunicazione_final', 'configurazioni', 'off');
    }
}