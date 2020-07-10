<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entity\Enti
 *
 * @ORM\Entity()
 * @ORM\Table(name="Enti")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"base":"BaseEnti", "extended":"Enti"})
 */
class BaseEnti
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    protected $cod_provincia;

    /**
     * @ORM\Column(type="string", length=10)
     */
    protected $cod_comune;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $descrizione;

    /**
     * @ORM\OneToMany(targetEntity="Entexevento", mappedBy="enti")
     * @ORM\JoinColumn(name="id", referencedColumnName="ente_id", nullable=false)
     */
    protected $entexeventos;

    /**
     * @ORM\OneToMany(targetEntity="Rxcandidati", mappedBy="enti")
     * @ORM\JoinColumn(name="id", referencedColumnName="ente_id", nullable=false)
     */
    protected $rxcandidatis;

    /**
     * @ORM\OneToMany(targetEntity="Rxcandidatisecondari", mappedBy="enti")
     * @ORM\JoinColumn(name="id", referencedColumnName="ente_id", nullable=false)
     */
    protected $rxcandidatisecondaris;

    /**
     * @ORM\OneToMany(targetEntity="Rxliste", mappedBy="enti")
     * @ORM\JoinColumn(name="id", referencedColumnName="ente_id", nullable=false)
     */
    protected $rxlistes;

    /**
     * @ORM\OneToMany(targetEntity="Utenti", mappedBy="enti")
     * @ORM\JoinColumn(name="id", referencedColumnName="ente_id", nullable=false)
     */
    protected $utentis;

    public function __construct()
    {
        $this->entexeventos = new ArrayCollection();
        $this->rxcandidatis = new ArrayCollection();
        $this->rxcandidatisecondaris = new ArrayCollection();
        $this->rxlistes = new ArrayCollection();
        $this->utentis = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Enti
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
     * Set the value of cod_provincia.
     *
     * @param string $cod_provincia
     * @return \App\Entity\Enti
     */
    public function setCodProvincia($cod_provincia)
    {
        $this->cod_provincia = $cod_provincia;

        return $this;
    }

    /**
     * Get the value of cod_provincia.
     *
     * @return string
     */
    public function getCodProvincia()
    {
        return $this->cod_provincia;
    }

    /**
     * Set the value of cod_comune.
     *
     * @param string $cod_comune
     * @return \App\Entity\Enti
     */
    public function setCodComune($cod_comune)
    {
        $this->cod_comune = $cod_comune;

        return $this;
    }

    /**
     * Get the value of cod_comune.
     *
     * @return string
     */
    public function getCodComune()
    {
        return $this->cod_comune;
    }

    /**
     * Set the value of descrizione.
     *
     * @param string $descrizione
     * @return \App\Entity\Enti
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
     * Add Entexevento entity to collection (one to many).
     *
     * @param \App\Entity\Entexevento $entexevento
     * @return \App\Entity\Enti
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
     * @return \App\Entity\Enti
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

    /**
     * Add Rxcandidati entity to collection (one to many).
     *
     * @param \App\Entity\Rxcandidati $rxcandidati
     * @return \App\Entity\Enti
     */
    public function addRxcandidati(Rxcandidati $rxcandidati)
    {
        $this->rxcandidatis[] = $rxcandidati;

        return $this;
    }

    /**
     * Remove Rxcandidati entity from collection (one to many).
     *
     * @param \App\Entity\Rxcandidati $rxcandidati
     * @return \App\Entity\Enti
     */
    public function removeRxcandidati(Rxcandidati $rxcandidati)
    {
        $this->rxcandidatis->removeElement($rxcandidati);

        return $this;
    }

    /**
     * Get Rxcandidati entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRxcandidatis()
    {
        return $this->rxcandidatis;
    }

    /**
     * Add Rxcandidatisecondari entity to collection (one to many).
     *
     * @param \App\Entity\Rxcandidatisecondari $rxcandidatisecondari
     * @return \App\Entity\Enti
     */
    public function addRxcandidatisecondari(Rxcandidatisecondari $rxcandidatisecondari)
    {
        $this->rxcandidatisecondaris[] = $rxcandidatisecondari;

        return $this;
    }

    /**
     * Remove Rxcandidatisecondari entity from collection (one to many).
     *
     * @param \App\Entity\Rxcandidatisecondari $rxcandidatisecondari
     * @return \App\Entity\Enti
     */
    public function removeRxcandidatisecondari(Rxcandidatisecondari $rxcandidatisecondari)
    {
        $this->rxcandidatisecondaris->removeElement($rxcandidatisecondari);

        return $this;
    }

    /**
     * Get Rxcandidatisecondari entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRxcandidatisecondaris()
    {
        return $this->rxcandidatisecondaris;
    }

    /**
     * Add Rxliste entity to collection (one to many).
     *
     * @param \App\Entity\Rxliste $rxliste
     * @return \App\Entity\Enti
     */
    public function addRxliste(Rxliste $rxliste)
    {
        $this->rxlistes[] = $rxliste;

        return $this;
    }

    /**
     * Remove Rxliste entity from collection (one to many).
     *
     * @param \App\Entity\Rxliste $rxliste
     * @return \App\Entity\Enti
     */
    public function removeRxliste(Rxliste $rxliste)
    {
        $this->rxlistes->removeElement($rxliste);

        return $this;
    }

    /**
     * Get Rxliste entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRxlistes()
    {
        return $this->rxlistes;
    }

    /**
     * Add Utenti entity to collection (one to many).
     *
     * @param \App\Entity\Utenti $utenti
     * @return \App\Entity\Enti
     */
    public function addUtenti(Utenti $utenti)
    {
        $this->utentis[] = $utenti;

        return $this;
    }

    /**
     * Remove Utenti entity from collection (one to many).
     *
     * @param \App\Entity\Utenti $utenti
     * @return \App\Entity\Enti
     */
    public function removeUtenti(Utenti $utenti)
    {
        $this->utentis->removeElement($utenti);

        return $this;
    }

    /**
     * Get Utenti entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUtentis()
    {
        return $this->utentis;
    }

    public function __sleep()
    {
        return array('id', 'cod_provincia', 'cod_comune', 'descrizione');
    }
}