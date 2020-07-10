<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\Secondarioxlista
 *
 * @ORM\Entity()
 * @ORM\Table(name="Secondarioxlista", indexes={@ORM\Index(name="fk_Secondarioxlista_Listapreferenze1_idx", columns={"lista_id"}), @ORM\Index(name="fk_Secondarioxlista_Candidatisecondari1_idx", columns={"candidato_secondario_id"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"base":"BaseSecondarioxlista", "extended":"Secondarioxlista"})
 */
class BaseSecondarioxlista
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
    protected $lista_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $candidato_secondario_id;

    /**
     * @ORM\Column(name="`off`", type="boolean", nullable=true)
     */
    protected $off;

    /**
     * @ORM\Column(type="integer")
     */
    protected $posizione;

    /**
     * @ORM\ManyToOne(targetEntity="Listapreferenze", inversedBy="secondarioxlistas")
     * @ORM\JoinColumn(name="lista_id", referencedColumnName="id", nullable=false)
     */
    protected $listapreferenze;

    /**
     * @ORM\ManyToOne(targetEntity="Candidatisecondari", inversedBy="secondarioxlistas")
     * @ORM\JoinColumn(name="candidato_secondario_id", referencedColumnName="id", nullable=false)
     */
    protected $candidatisecondari;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Secondarioxlista
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
     * Set the value of lista_id.
     *
     * @param integer $lista_id
     * @return \App\Entity\Secondarioxlista
     */
    public function setListaId($lista_id)
    {
        $this->lista_id = $lista_id;

        return $this;
    }

    /**
     * Get the value of lista_id.
     *
     * @return integer
     */
    public function getListaId()
    {
        return $this->lista_id;
    }

    /**
     * Set the value of candidato_secondario_id.
     *
     * @param integer $candidato_secondario_id
     * @return \App\Entity\Secondarioxlista
     */
    public function setCandidatoSecondarioId($candidato_secondario_id)
    {
        $this->candidato_secondario_id = $candidato_secondario_id;

        return $this;
    }

    /**
     * Get the value of candidato_secondario_id.
     *
     * @return integer
     */
    public function getCandidatoSecondarioId()
    {
        return $this->candidato_secondario_id;
    }

    /**
     * Set the value of off.
     *
     * @param boolean $off
     * @return \App\Entity\Secondarioxlista
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
     * Set the value of posizione.
     *
     * @param integer $posizione
     * @return \App\Entity\Secondarioxlista
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
     * Set Listapreferenze entity (many to one).
     *
     * @param \App\Entity\Listapreferenze $listapreferenze
     * @return \App\Entity\Secondarioxlista
     */
    public function setListapreferenze(Listapreferenze $listapreferenze = null)
    {
        $this->listapreferenze = $listapreferenze;

        return $this;
    }

    /**
     * Get Listapreferenze entity (many to one).
     *
     * @return \App\Entity\Listapreferenze
     */
    public function getListapreferenze()
    {
        return $this->listapreferenze;
    }

    /**
     * Set Candidatisecondari entity (many to one).
     *
     * @param \App\Entity\Candidatisecondari $candidatisecondari
     * @return \App\Entity\Secondarioxlista
     */
    public function setCandidatisecondari(Candidatisecondari $candidatisecondari = null)
    {
        $this->candidatisecondari = $candidatisecondari;

        return $this;
    }

    /**
     * Get Candidatisecondari entity (many to one).
     *
     * @return \App\Entity\Candidatisecondari
     */
    public function getCandidatisecondari()
    {
        return $this->candidatisecondari;
    }

    public function __sleep()
    {
        return array('id', 'lista_id', 'candidato_secondario_id', 'off', 'posizione');
    }
}