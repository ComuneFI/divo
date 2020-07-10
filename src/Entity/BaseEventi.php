<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entity\Eventi
 *
 * @ORM\Entity()
 * @ORM\Table(name="Eventi")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"base":"BaseEventi", "extended":"Eventi"})
 */
class BaseEventi
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $codice_evento;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $data_evento;

    /**
     * @ORM\Column(type="string", length=350, nullable=true)
     */
    protected $evento;

    /**
     * @ORM\Column(type="string", length=350, nullable=true)
     */
    protected $descrizione_evento;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $configurazioni;

    /**
     * @ORM\Column(name="`off`", type="boolean", nullable=true)
     */
    protected $off;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $stato_wf;

    /**
     * @ORM\OneToMany(targetEntity="Actionlogs", mappedBy="eventi")
     * @ORM\JoinColumn(name="id", referencedColumnName="eventi_id_evento", nullable=false)
     */
    protected $actionlogs;

    /**
     * @ORM\OneToMany(targetEntity="Circoscrizioni", mappedBy="eventi")
     * @ORM\JoinColumn(name="id", referencedColumnName="evento_id", nullable=false)
     */
    protected $circoscrizionis;

    /**
     * @ORM\OneToMany(targetEntity="Confxvotanti", mappedBy="eventi")
     * @ORM\JoinColumn(name="id", referencedColumnName="evento_id", nullable=false)
     */
    protected $confxvotantis;

    /**
     * @ORM\OneToMany(targetEntity="Entexevento", mappedBy="eventi")
     * @ORM\JoinColumn(name="id", referencedColumnName="evento_id", nullable=false)
     */
    protected $entexeventos;

    public function __construct()
    {
        $this->actionlogs = new ArrayCollection();
        $this->circoscrizionis = new ArrayCollection();
        $this->confxvotantis = new ArrayCollection();
        $this->entexeventos = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Eventi
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
     * Set the value of codice_evento.
     *
     * @param string $codice_evento
     * @return \App\Entity\Eventi
     */
    public function setCodiceEvento($codice_evento)
    {
        $this->codice_evento = $codice_evento;

        return $this;
    }

    /**
     * Get the value of codice_evento.
     *
     * @return string
     */
    public function getCodiceEvento()
    {
        return $this->codice_evento;
    }

    /**
     * Set the value of data_evento.
     *
     * @param \DateTime $data_evento
     * @return \App\Entity\Eventi
     */
    public function setDataEvento($data_evento)
    {
        $this->data_evento = $data_evento;

        return $this;
    }

    /**
     * Get the value of data_evento.
     *
     * @return \DateTime
     */
    public function getDataEvento()
    {
        return $this->data_evento;
    }

    /**
     * Set the value of evento.
     *
     * @param string $evento
     * @return \App\Entity\Eventi
     */
    public function setEvento($evento)
    {
        $this->evento = $evento;

        return $this;
    }

    /**
     * Get the value of evento.
     *
     * @return string
     */
    public function getEvento()
    {
        return $this->evento;
    }

    /**
     * Set the value of descrizione_evento.
     *
     * @param string $descrizione_evento
     * @return \App\Entity\Eventi
     */
    public function setDescrizioneEvento($descrizione_evento)
    {
        $this->descrizione_evento = $descrizione_evento;

        return $this;
    }

    /**
     * Get the value of descrizione_evento.
     *
     * @return string
     */
    public function getDescrizioneEvento()
    {
        return $this->descrizione_evento;
    }

    /**
     * Set the value of configurazioni.
     *
     * @param string $configurazioni
     * @return \App\Entity\Eventi
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
     * @return \App\Entity\Eventi
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
     * Set the value of stato_wf.
     *
     * @param string $stato_wf
     * @return \App\Entity\Eventi
     */
    public function setStatoWf($stato_wf)
    {
        $this->stato_wf = $stato_wf;

        return $this;
    }

    /**
     * Get the value of stato_wf.
     *
     * @return string
     */
    public function getStatoWf()
    {
        return $this->stato_wf;
    }

    /**
     * Add Actionlogs entity to collection (one to many).
     *
     * @param \App\Entity\Actionlogs $actionlogs
     * @return \App\Entity\Eventi
     */
    public function addActionlogs(Actionlogs $actionlogs)
    {
        $this->actionlogs[] = $actionlogs;

        return $this;
    }

    /**
     * Remove Actionlogs entity from collection (one to many).
     *
     * @param \App\Entity\Actionlogs $actionlogs
     * @return \App\Entity\Eventi
     */
    public function removeActionlogs(Actionlogs $actionlogs)
    {
        $this->actionlogs->removeElement($actionlogs);

        return $this;
    }

    /**
     * Get Actionlogs entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActionlogs()
    {
        return $this->actionlogs;
    }

    /**
     * Add Circoscrizioni entity to collection (one to many).
     *
     * @param \App\Entity\Circoscrizioni $circoscrizioni
     * @return \App\Entity\Eventi
     */
    public function addCircoscrizioni(Circoscrizioni $circoscrizioni)
    {
        $this->circoscrizionis[] = $circoscrizioni;

        return $this;
    }

    /**
     * Remove Circoscrizioni entity from collection (one to many).
     *
     * @param \App\Entity\Circoscrizioni $circoscrizioni
     * @return \App\Entity\Eventi
     */
    public function removeCircoscrizioni(Circoscrizioni $circoscrizioni)
    {
        $this->circoscrizionis->removeElement($circoscrizioni);

        return $this;
    }

    /**
     * Get Circoscrizioni entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCircoscrizionis()
    {
        return $this->circoscrizionis;
    }

    /**
     * Add Confxvotanti entity to collection (one to many).
     *
     * @param \App\Entity\Confxvotanti $confxvotanti
     * @return \App\Entity\Eventi
     */
    public function addConfxvotanti(Confxvotanti $confxvotanti)
    {
        $this->confxvotantis[] = $confxvotanti;

        return $this;
    }

    /**
     * Remove Confxvotanti entity from collection (one to many).
     *
     * @param \App\Entity\Confxvotanti $confxvotanti
     * @return \App\Entity\Eventi
     */
    public function removeConfxvotanti(Confxvotanti $confxvotanti)
    {
        $this->confxvotantis->removeElement($confxvotanti);

        return $this;
    }

    /**
     * Get Confxvotanti entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConfxvotantis()
    {
        return $this->confxvotantis;
    }

    /**
     * Add Entexevento entity to collection (one to many).
     *
     * @param \App\Entity\Entexevento $entexevento
     * @return \App\Entity\Eventi
     */
    public function addEntexevento(Entexevento $entexevento)
    {
        $this->entexeventos[] = $entexevento;

        return $this;
    }

    /**
     * Remove Entexevento entity from collection (one to many).
     *
     * @param \App\Entity\Entexevento $entexevento
     * @return \App\Entity\Eventi
     */
    public function removeEntexevento(Entexevento $entexevento)
    {
        $this->entexeventos->removeElement($entexevento);

        return $this;
    }

    /**
     * Get Entexevento entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEntexeventos()
    {
        return $this->entexeventos;
    }

    public function __sleep()
    {
        return array('id', 'codice_evento', 'data_evento', 'evento', 'descrizione_evento', 'configurazioni', 'off', 'stato_wf');
    }
}