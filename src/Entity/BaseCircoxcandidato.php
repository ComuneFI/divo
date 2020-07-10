<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\Circoxcandidato
 *
 * @ORM\Entity()
 * @ORM\Table(name="Circoxcandidato", indexes={@ORM\Index(name="fk_Personaxpremier_Circoscrizioni1_idx", columns={"circ_id"}), @ORM\Index(name="fk_Circoxcandidato_Candidatiprincipali1_idx", columns={"candidato_principale_id"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"base":"BaseCircoxcandidato", "extended":"Circoxcandidato"})
 */
class BaseCircoxcandidato
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
    protected $circ_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $candidato_principale_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $posizione;

    /**
     * @ORM\Column(name="`off`", type="boolean", nullable=true)
     */
    protected $off;

    /**
     * @ORM\ManyToOne(targetEntity="Circoscrizioni", inversedBy="circoxcandidatos")
     * @ORM\JoinColumn(name="circ_id", referencedColumnName="id", nullable=false)
     */
    protected $circoscrizioni;

    /**
     * @ORM\ManyToOne(targetEntity="Candidatiprincipali", inversedBy="circoxcandidatos")
     * @ORM\JoinColumn(name="candidato_principale_id", referencedColumnName="id", nullable=false)
     */
    protected $candidatiprincipali;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Circoxcandidato
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
     * Set the value of circ_id.
     *
     * @param integer $circ_id
     * @return \App\Entity\Circoxcandidato
     */
    public function setCircId($circ_id)
    {
        $this->circ_id = $circ_id;

        return $this;
    }

    /**
     * Get the value of circ_id.
     *
     * @return integer
     */
    public function getCircId()
    {
        return $this->circ_id;
    }

    /**
     * Set the value of candidato_principale_id.
     *
     * @param integer $candidato_principale_id
     * @return \App\Entity\Circoxcandidato
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
     * Set the value of posizione.
     *
     * @param integer $posizione
     * @return \App\Entity\Circoxcandidato
     */
    public function setPosizione($posizione)
    {
        $this->posizione = $posizione;

        return $this;
    }

    /**
     * Get the value of posizione.
     *
     * @return integer
     */
    public function getPosizione()
    {
        return $this->posizione;
    }

    /**
     * Set the value of off.
     *
     * @param boolean $off
     * @return \App\Entity\Circoxcandidato
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
     * Set Circoscrizioni entity (many to one).
     *
     * @param \App\Entity\Circoscrizioni $circoscrizioni
     * @return \App\Entity\Circoxcandidato
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

    /**
     * Set Candidatiprincipali entity (many to one).
     *
     * @param \App\Entity\Candidatiprincipali $candidatiprincipali
     * @return \App\Entity\Circoxcandidato
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

    public function __sleep()
    {
        return array('id', 'circ_id', 'candidato_principale_id', 'posizione', 'off');
    }
}