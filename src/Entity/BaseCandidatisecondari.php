<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entity\Candidatisecondari
 *
 * @ORM\Entity()
 * @ORM\Table(name="Candidatisecondari")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"base":"BaseCandidatisecondari", "extended":"Candidatisecondari"})
 */
class BaseCandidatisecondari
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $cognome;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $nome;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $luogo_nascita;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $sesso;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $id_source;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $id_target;
  /**
     * @ORM\Column(name="`indipendente`", type="integer", nullable=true)
     */
    protected $indipendente;
    /**
     * @ORM\Column(name="`off`", type="boolean", nullable=true)
     */
    protected $off;

    /**
     * @ORM\OneToMany(targetEntity="Rxpreferenze", mappedBy="candidatisecondari")
     * @ORM\JoinColumn(name="id", referencedColumnName="candidato_secondario_id", nullable=false)
     */
    protected $rxpreferenzes;

    /**
     * @ORM\OneToMany(targetEntity="Secondarioxlista", mappedBy="candidatisecondari")
     * @ORM\JoinColumn(name="id", referencedColumnName="candidato_secondario_id", nullable=false)
     */
    protected $secondarioxlistas;

    public function __construct()
    {
        $this->rxpreferenzes = new ArrayCollection();
        $this->secondarioxlistas = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Candidatisecondari
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
     * Set the value of cognome.
     *
     * @param string $cognome
     * @return \App\Entity\Candidatisecondari
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
     * Set the value of nome.
     *
     * @param string $nome
     * @return \App\Entity\Candidatisecondari
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
     * Set the value of luogo_nascita.
     *
     * @param string $luogo_nascita
     * @return \App\Entity\Candidatisecondari
     */
    public function setLuogoNascita($luogo_nascita)
    {
        $this->luogo_nascita = $luogo_nascita;

        return $this;
    }

    /**
     * Get the value of luogo_nascita.
     *
     * @return string
     */
    public function getLuogoNascita()
    {
        return $this->luogo_nascita;
    }

    /**
     * Set the value of sesso.
     *
     * @param string $sesso
     * @return \App\Entity\Candidatisecondari
     */
    public function setSesso($sesso)
    {
        $this->sesso = $sesso;

        return $this;
    }

    /**
     * Get the value of sesso.
     *
     * @return string
     */
    public function getSesso()
    {
        return $this->sesso;
    }

    /**
     * Set the value of id_source.
     *
     * @param string $id_source
     * @return \App\Entity\Candidatisecondari
     */
    public function setIdSource($id_source)
    {
        $this->id_source = $id_source;

        return $this;
    }

    /**
     * Get the value of id_source.
     *
     * @return string
     */
    public function getIdSource()
    {
        return $this->id_source;
    }

    /**
     * Set the value of id_target.
     *
     * @param string $id_target
     * @return \App\Entity\Candidatisecondari
     */
    public function setIdTarget($id_target)
    {
        $this->id_target = $id_target;

        return $this;
    }

    /**
     * Get the value of id_target.
     *
     * @return string
     */
    public function getIdTarget()
    {
        return $this->id_target;
    }

     /**
     * Set the value of indipendente.
     *
     * @param integer $indipendente
     * @return \App\Entity\Candidatisecondari
     */
    public function setIndipendente($indipendente)
    {
        $this->indipendente = $indipendente;

        return $this;
    }

    /**
     * Get the value of indipendente.
     *
     * @return integer
     */
    public function getIndipendente()
    {
        return $this->indipendente;
    }

    
    /**
     * Set the value of off.
     *
     * @param boolean $off
     * @return \App\Entity\Candidatisecondari
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
     * Add Rxpreferenze entity to collection (one to many).
     *
     * @param \App\Entity\Rxpreferenze $rxpreferenze
     * @return \App\Entity\Candidatisecondari
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
     * @return \App\Entity\Candidatisecondari
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
     * Add Secondarioxlista entity to collection (one to many).
     *
     * @param \App\Entity\Secondarioxlista $secondarioxlista
     * @return \App\Entity\Candidatisecondari
     */
    public function addSecondarioxlista(Secondarioxlista $secondarioxlista)
    {
        $this->secondarioxlistas[] = $secondarioxlista;

        return $this;
    }

    /**
     * Remove Secondarioxlista entity from collection (one to many).
     *
     * @param \App\Entity\Secondarioxlista $secondarioxlista
     * @return \App\Entity\Candidatisecondari
     */
    public function removeSecondarioxlista(Secondarioxlista $secondarioxlista)
    {
        $this->secondarioxlistas->removeElement($secondarioxlista);

        return $this;
    }

    /**
     * Get Secondarioxlista entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSecondarioxlistas()
    {
        return $this->secondarioxlistas;
    }

    public function __sleep()
    {
        return array('id', 'cognome', 'nome', 'luogo_nascita', 'sesso', 'id_source', 'id_target', 'off');
    }
}