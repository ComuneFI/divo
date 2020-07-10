<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entity\Actionlogs
 *
 * @ORM\Entity()
 * @ORM\Table(name="Actionlogs", indexes={@ORM\Index(name="fk_Messaggi_Eventi1_idx", columns={"eventi_id_evento"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"base":"BaseActionlogs", "extended":"Actionlogs"})
 */
class BaseActionlogs
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $eventi_id_evento;

    /**
     * @ORM\Column(name="`timestamp`", type="datetime", nullable=true)
     */
    protected $timestamp;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $requestedws;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $payload;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $codice_esito;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $descrizione_esito;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $response_message;

    /**
     * @ORM\OneToMany(targetEntity="Rxpreferenze", mappedBy="actionlogs")
     * @ORM\JoinColumn(name="id", referencedColumnName="actionlogs_id", nullable=false)
     */
    protected $rxpreferenzes;

    /**
     * @ORM\OneToMany(targetEntity="Rxscrutinicandidati", mappedBy="actionlogs")
     * @ORM\JoinColumn(name="id", referencedColumnName="actionlogs_id", nullable=false)
     */
    protected $rxscrutinicandidatis;

    /**
     * @ORM\OneToMany(targetEntity="Rxscrutiniliste", mappedBy="actionlogs")
     * @ORM\JoinColumn(name="id", referencedColumnName="actionlogs_id", nullable=false)
     */
    protected $rxscrutinilistes;

    /**
     * @ORM\OneToMany(targetEntity="Rxvotanti", mappedBy="actionlogs")
     * @ORM\JoinColumn(name="id", referencedColumnName="actionlogs_id", nullable=false)
     */
    protected $rxvotantis;

    /**
     * @ORM\OneToMany(targetEntity="Rxvotinonvalidi", mappedBy="actionlogs")
     * @ORM\JoinColumn(name="id", referencedColumnName="actionlogs_id", nullable=false)
     */
    protected $rxvotinonvalidis;

    /**
     * @ORM\ManyToOne(targetEntity="Eventi", inversedBy="actionlogs")
     * @ORM\JoinColumn(name="eventi_id_evento", referencedColumnName="id")
     */
    protected $eventi;

    public function __construct()
    {
        $this->rxpreferenzes = new ArrayCollection();
        $this->rxscrutinicandidatis = new ArrayCollection();
        $this->rxscrutinilistes = new ArrayCollection();
        $this->rxvotantis = new ArrayCollection();
        $this->rxvotinonvalidis = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Actionlogs
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
     * Set the value of eventi_id_evento.
     *
     * @param integer $eventi_id_evento
     * @return \App\Entity\Actionlogs
     */
    public function setEventiIdEvento($eventi_id_evento)
    {
        $this->eventi_id_evento = $eventi_id_evento;

        return $this;
    }

    /**
     * Get the value of eventi_id_evento.
     *
     * @return integer
     */
    public function getEventiIdEvento()
    {
        return $this->eventi_id_evento;
    }

    /**
     * Set the value of timestamp.
     *
     * @param \DateTime $timestamp
     * @return \App\Entity\Actionlogs
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
     * Set the value of requestedws.
     *
     * @param string $requestedws
     * @return \App\Entity\Actionlogs
     */
    public function setRequestedws($requestedws)
    {
        $this->requestedws = $requestedws;

        return $this;
    }

    /**
     * Get the value of requestedws.
     *
     * @return string
     */
    public function getRequestedws()
    {
        return $this->requestedws;
    }

    /**
     * Set the value of payload.
     *
     * @param string $payload
     * @return \App\Entity\Actionlogs
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * Get the value of payload.
     *
     * @return string
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Set the value of codice_esito.
     *
     * @param string $codice_esito
     * @return \App\Entity\Actionlogs
     */
    public function setCodiceEsito($codice_esito)
    {
        $this->codice_esito = $codice_esito;

        return $this;
    }

    /**
     * Get the value of codice_esito.
     *
     * @return string
     */
    public function getCodiceEsito()
    {
        return $this->codice_esito;
    }

    /**
     * Set the value of descrizione_esito.
     *
     * @param string $descrizione_esito
     * @return \App\Entity\Actionlogs
     */
    public function setDescrizioneEsito($descrizione_esito)
    {
        $this->descrizione_esito = $descrizione_esito;

        return $this;
    }

    /**
     * Get the value of descrizione_esito.
     *
     * @return string
     */
    public function getDescrizioneEsito()
    {
        return $this->descrizione_esito;
    }

    /**
     * Set the value of response_message.
     *
     * @param string $response_message
     * @return \App\Entity\Actionlogs
     */
    public function setResponseMessage($response_message)
    {
        $this->response_message = $response_message;

        return $this;
    }

    /**
     * Get the value of response_message.
     *
     * @return string
     */
    public function getResponseMessage()
    {
        return $this->response_message;
    }

    /**
     * Add Rxpreferenze entity to collection (one to many).
     *
     * @param \App\Entity\Rxpreferenze $rxpreferenze
     * @return \App\Entity\Actionlogs
     */
    public function addRxpreferenze(Rxpreferenze $rxpreferenze)
    {
        $this->rxpreferenzes[] = $rxpreferenze;

        return $this;
    }

    /**
     * Remove Rxpreferenze entity from collection (one to many).
     *
     * @param \App\Entity\Rxpreferenze $rxpreferenze
     * @return \App\Entity\Actionlogs
     */
    public function removeRxpreferenze(Rxpreferenze $rxpreferenze)
    {
        $this->rxpreferenzes->removeElement($rxpreferenze);

        return $this;
    }

    /**
     * Get Rxpreferenze entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRxpreferenzes()
    {
        return $this->rxpreferenzes;
    }

    /**
     * Add Rxscrutinicandidati entity to collection (one to many).
     *
     * @param \App\Entity\Rxscrutinicandidati $rxscrutinicandidati
     * @return \App\Entity\Actionlogs
     */
    public function addRxscrutinicandidati(Rxscrutinicandidati $rxscrutinicandidati)
    {
        $this->rxscrutinicandidatis[] = $rxscrutinicandidati;

        return $this;
    }

    /**
     * Remove Rxscrutinicandidati entity from collection (one to many).
     *
     * @param \App\Entity\Rxscrutinicandidati $rxscrutinicandidati
     * @return \App\Entity\Actionlogs
     */
    public function removeRxscrutinicandidati(Rxscrutinicandidati $rxscrutinicandidati)
    {
        $this->rxscrutinicandidatis->removeElement($rxscrutinicandidati);

        return $this;
    }

    /**
     * Get Rxscrutinicandidati entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRxscrutinicandidatis()
    {
        return $this->rxscrutinicandidatis;
    }

    /**
     * Add Rxscrutiniliste entity to collection (one to many).
     *
     * @param \App\Entity\Rxscrutiniliste $rxscrutiniliste
     * @return \App\Entity\Actionlogs
     */
    public function addRxscrutiniliste(Rxscrutiniliste $rxscrutiniliste)
    {
        $this->rxscrutinilistes[] = $rxscrutiniliste;

        return $this;
    }

    /**
     * Remove Rxscrutiniliste entity from collection (one to many).
     *
     * @param \App\Entity\Rxscrutiniliste $rxscrutiniliste
     * @return \App\Entity\Actionlogs
     */
    public function removeRxscrutiniliste(Rxscrutiniliste $rxscrutiniliste)
    {
        $this->rxscrutinilistes->removeElement($rxscrutiniliste);

        return $this;
    }

    /**
     * Get Rxscrutiniliste entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRxscrutinilistes()
    {
        return $this->rxscrutinilistes;
    }

    /**
     * Add Rxvotanti entity to collection (one to many).
     *
     * @param \App\Entity\Rxvotanti $rxvotanti
     * @return \App\Entity\Actionlogs
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
     * @return \App\Entity\Actionlogs
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
     * Add Rxvotinonvalidi entity to collection (one to many).
     *
     * @param \App\Entity\Rxvotinonvalidi $rxvotinonvalidi
     * @return \App\Entity\Actionlogs
     */
    public function addRxvotinonvalidi(Rxvotinonvalidi $rxvotinonvalidi)
    {
        $this->rxvotinonvalidis[] = $rxvotinonvalidi;

        return $this;
    }

    /**
     * Remove Rxvotinonvalidi entity from collection (one to many).
     *
     * @param \App\Entity\Rxvotinonvalidi $rxvotinonvalidi
     * @return \App\Entity\Actionlogs
     */
    public function removeRxvotinonvalidi(Rxvotinonvalidi $rxvotinonvalidi)
    {
        $this->rxvotinonvalidis->removeElement($rxvotinonvalidi);

        return $this;
    }

    /**
     * Get Rxvotinonvalidi entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRxvotinonvalidis()
    {
        return $this->rxvotinonvalidis;
    }

    /**
     * Set Eventi entity (many to one).
     *
     * @param \App\Entity\Eventi $eventi
     * @return \App\Entity\Actionlogs
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
        return array('id', 'eventi_id_evento', 'timestamp', 'requestedws', 'payload', 'codice_esito', 'descrizione_esito', 'response_message');
    }
}