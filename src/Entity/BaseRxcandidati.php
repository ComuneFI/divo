<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\Rxcandidati
 *
 * @ORM\Entity()
 * @ORM\Table(name="Rxcandidati", indexes={@ORM\Index(name="fk_Rxcandidati_Enti1_idx", columns={"ente_id"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"base":"BaseRxcandidati", "extended":"Rxcandidati"})
 */
class BaseRxcandidati
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
    protected $ente_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $nome;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $cognome;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $id_source;

    /**
     * @ORM\Column(name="`off`", type="boolean", nullable=true)
     */
    protected $off;

    /**
     * @ORM\Column(name="`timestamp`", type="datetime", nullable=true)
     */
    protected $timestamp;

    /**
     * @ORM\ManyToOne(targetEntity="Enti", inversedBy="rxcandidatis")
     * @ORM\JoinColumn(name="ente_id", referencedColumnName="id", nullable=false)
     */
    protected $enti;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Rxcandidati
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
     * Set the value of ente_id.
     *
     * @param integer $ente_id
     * @return \App\Entity\Rxcandidati
     */
    public function setEnteId($ente_id)
    {
        $this->ente_id = $ente_id;

        return $this;
    }

    /**
     * Get the value of ente_id.
     *
     * @return integer
     */
    public function getEnteId()
    {
        return $this->ente_id;
    }

    /**
     * Set the value of nome.
     *
     * @param string $nome
     * @return \App\Entity\Rxcandidati
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get the value of nome.
     *
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set the value of cognome.
     *
     * @param string $cognome
     * @return \App\Entity\Rxcandidati
     */
    public function setCognome($cognome)
    {
        $this->cognome = $cognome;

        return $this;
    }

    /**
     * Get the value of cognome.
     *
     * @return string
     */
    public function getCognome()
    {
        return $this->cognome;
    }

    /**
     * Set the value of id_source.
     *
     * @param integer $id_source
     * @return \App\Entity\Rxcandidati
     */
    public function setIdSource($id_source)
    {
        $this->id_source = $id_source;

        return $this;
    }

    /**
     * Get the value of id_source.
     *
     * @return integer
     */
    public function getIdSource()
    {
        return $this->id_source;
    }

    /**
     * Set the value of off.
     *
     * @param boolean $off
     * @return \App\Entity\Rxcandidati
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
     * @return \App\Entity\Rxcandidati
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
     * Set Enti entity (many to one).
     *
     * @param \App\Entity\Enti $enti
     * @return \App\Entity\Rxcandidati
     */
    public function setEnti(Enti $enti = null)
    {
        $this->enti = $enti;

        return $this;
    }

    /**
     * Get Enti entity (many to one).
     *
     * @return \App\Entity\Enti
     */
    public function getEnti()
    {
        return $this->enti;
    }

    public function __sleep()
    {
        return array('id', 'ente_id', 'nome', 'cognome', 'id_source', 'off', 'timestamp');
    }
}