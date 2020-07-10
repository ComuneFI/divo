<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entity\Rxsezioni
 *
 * @ORM\Entity()
 * @ORM\Table(name="Rxsezioni", indexes={@ORM\Index(name="fk_Rxsezioni_Circoscrizioni1_idx", columns={"circo_id"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"base":"BaseRxsezioni", "extended":"Rxsezioni"})
 */
class BaseRxsezioni
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
    protected $circo_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $numero;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $descrizione;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $stato_wf;

    /**
     * @ORM\OneToMany(targetEntity="Rxpreferenze", mappedBy="rxsezioni")
     * @ORM\JoinColumn(name="id", referencedColumnName="rxsezione_id", nullable=false)
     */
    protected $rxpreferenzes;

    /**
     * @ORM\OneToMany(targetEntity="Rxscrutinicandidati", mappedBy="rxsezioni")
     * @ORM\JoinColumn(name="id", referencedColumnName="rxsezione_id", nullable=false)
     */
    protected $rxscrutinicandidatis;

    /**
     * @ORM\OneToMany(targetEntity="Rxscrutiniliste", mappedBy="rxsezioni")
     * @ORM\JoinColumn(name="id", referencedColumnName="rxsezione_id", nullable=false)
     */
    protected $rxscrutinilistes;

    /**
     * @ORM\OneToMany(targetEntity="Rxvotanti", mappedBy="rxsezioni")
     * @ORM\JoinColumn(name="id", referencedColumnName="rxsezione_id", nullable=false)
     */
    protected $rxvotantis;

    /**
     * @ORM\OneToMany(targetEntity="Rxvotinonvalidi", mappedBy="rxsezioni")
     * @ORM\JoinColumn(name="id", referencedColumnName="rxsezione_id", nullable=false)
     */
    protected $rxvotinonvalidis;

    /**
     * @ORM\ManyToOne(targetEntity="Circoscrizioni", inversedBy="rxsezionis")
     * @ORM\JoinColumn(name="circo_id", referencedColumnName="id", nullable=false)
     */
    protected $circoscrizioni;

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
     * @return \App\Entity\Rxsezioni
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
     * Set the value of circo_id.
     *
     * @param integer $circo_id
     * @return \App\Entity\Rxsezioni
     */
    public function setCircoId($circo_id)
    {
        $this->circo_id = $circo_id;

        return $this;
    }

    /**
     * Get the value of circo_id.
     *
     * @return integer
     */
    public function getCircoId()
    {
        return $this->circo_id;
    }

    /**
     * Set the value of numero.
     *
     * @param integer $numero
     * @return \App\Entity\Rxsezioni
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get the value of numero.
     *
     * @return integer
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set the value of descrizione.
     *
     * @param string $descrizione
     * @return \App\Entity\Rxsezioni
     */
    public function setDescrizione($descrizione)
    {
        $this->descrizione = $descrizione;

        return $this;
    }

    /**
     * Get the value of descrizione.
     *
     * @return string
     */
    public function getDescrizione()
    {
        return $this->descrizione;
    }

    /**
     * Set the value of stato_wf.
     *
     * @param string $stato_wf
     * @return \App\Entity\Rxsezioni
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
     * Add Rxpreferenze entity to collection (one to many).
     *
     * @param \App\Entity\Rxpreferenze $rxpreferenze
     * @return \App\Entity\Rxsezioni
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
     * @return \App\Entity\Rxsezioni
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
     * @return \App\Entity\Rxsezioni
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
     * @return \App\Entity\Rxsezioni
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
     * @return \App\Entity\Rxsezioni
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
     * @return \App\Entity\Rxsezioni
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
     * @return \App\Entity\Rxsezioni
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
     * @return \App\Entity\Rxsezioni
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
     * @return \App\Entity\Rxsezioni
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
     * @return \App\Entity\Rxsezioni
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
     * Set Circoscrizioni entity (many to one).
     *
     * @param \App\Entity\Circoscrizioni $circoscrizioni
     * @return \App\Entity\Rxsezioni
     */
    public function setCircoscrizioni(Circoscrizioni $circoscrizioni = null)
    {
        $this->circoscrizioni = $circoscrizioni;

        return $this;
    }

    /**
     * Get Circoscrizioni entity (many to one).
     *
     * @return \App\Entity\Circoscrizioni
     */
    public function getCircoscrizioni()
    {
        return $this->circoscrizioni;
    }

    public function __sleep()
    {
        return array('id', 'circo_id', 'numero', 'descrizione', 'stato_wf');
    }
}