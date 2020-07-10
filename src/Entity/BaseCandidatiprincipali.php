<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entity\Candidatiprincipali
 *
 * @ORM\Entity()
 * @ORM\Table(name="Candidatiprincipali")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"base":"BaseCandidatiprincipali", "extended":"Candidatiprincipali"})
 */
class BaseCandidatiprincipali
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
     * @ORM\Column(name="`off`", type="boolean", nullable=true)
     */
    protected $off;

    /**
     * @ORM\OneToMany(targetEntity="Circoxcandidato", mappedBy="candidatiprincipali")
     * @ORM\JoinColumn(name="id", referencedColumnName="candidato_principale_id", nullable=false)
     */
    protected $circoxcandidatos;

    /**
     * @ORM\OneToMany(targetEntity="Listaxprincipale", mappedBy="candidatiprincipali")
     * @ORM\JoinColumn(name="id", referencedColumnName="candidato_principale_id", nullable=false)
     */
    protected $listaxprincipales;

    /**
     * @ORM\OneToMany(targetEntity="Rxscrutinicandidati", mappedBy="candidatiprincipali")
     * @ORM\JoinColumn(name="id", referencedColumnName="candidato_principale_id", nullable=false)
     */
    protected $rxscrutinicandidatis;

    public function __construct()
    {
        $this->circoxcandidatos = new ArrayCollection();
        $this->listaxprincipales = new ArrayCollection();
        $this->rxscrutinicandidatis = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Candidatiprincipali
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
     * @return \App\Entity\Candidatiprincipali
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
     * @return \App\Entity\Candidatiprincipali
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
     * @return \App\Entity\Candidatiprincipali
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
     * @return \App\Entity\Candidatiprincipali
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
     * @return \App\Entity\Candidatiprincipali
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
     * @return \App\Entity\Candidatiprincipali
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
     * Set the value of off.
     *
     * @param boolean $off
     * @return \App\Entity\Candidatiprincipali
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
     * Add Circoxcandidato entity to collection (one to many).
     *
     * @param \App\Entity\Circoxcandidato $circoxcandidato
     * @return \App\Entity\Candidatiprincipali
     */
    public function addCircoxcandidato(Circoxcandidato $circoxcandidato)
    {
        $this->circoxcandidatos[] = $circoxcandidato;

        return $this;
    }

    /**
     * Remove Circoxcandidato entity from collection (one to many).
     *
     * @param \App\Entity\Circoxcandidato $circoxcandidato
     * @return \App\Entity\Candidatiprincipali
     */
    public function removeCircoxcandidato(Circoxcandidato $circoxcandidato)
    {
        $this->circoxcandidatos->removeElement($circoxcandidato);

        return $this;
    }

    /**
     * Get Circoxcandidato entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCircoxcandidatos()
    {
        return $this->circoxcandidatos;
    }

    /**
     * Add Listaxprincipale entity to collection (one to many).
     *
     * @param \App\Entity\Listaxprincipale $listaxprincipale
     * @return \App\Entity\Candidatiprincipali
     */
    public function addListaxprincipale(Listaxprincipale $listaxprincipale)
    {
        $this->listaxprincipales[] = $listaxprincipale;

        return $this;
    }

    /**
     * Remove Listaxprincipale entity from collection (one to many).
     *
     * @param \App\Entity\Listaxprincipale $listaxprincipale
     * @return \App\Entity\Candidatiprincipali
     */
    public function removeListaxprincipale(Listaxprincipale $listaxprincipale)
    {
        $this->listaxprincipales->removeElement($listaxprincipale);

        return $this;
    }

    /**
     * Get Listaxprincipale entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getListaxprincipales()
    {
        return $this->listaxprincipales;
    }

    /**
     * Add Rxscrutinicandidati entity to collection (one to many).
     *
     * @param \App\Entity\Rxscrutinicandidati $rxscrutinicandidati
     * @return \App\Entity\Candidatiprincipali
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
     * @return \App\Entity\Candidatiprincipali
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

    public function __sleep()
    {
        return array('id', 'cognome', 'nome', 'luogo_nascita', 'sesso', 'id_source', 'id_target', 'off');
    }
}