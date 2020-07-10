<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\Listaxprincipale
 *
 * @ORM\Entity()
 * @ORM\Table(name="Listaxprincipale", indexes={@ORM\Index(name="fk_Listaxprincipale_Listapreferenze1_idx", columns={"lista_id"}), @ORM\Index(name="fk_Listaxprincipale_Candidatiprincipali1_idx", columns={"candidato_principale_id"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"base":"BaseListaxprincipale", "extended":"Listaxprincipale"})
 */
class BaseListaxprincipale
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
    protected $candidato_principale_id;

    /**
     * @ORM\Column(name="`off`", type="boolean", nullable=true)
     */
    protected $off;

    /**
     * @ORM\Column(type="integer")
     */
    protected $posizione;

    /**
     * @ORM\ManyToOne(targetEntity="Listapreferenze", inversedBy="listaxprincipales")
     * @ORM\JoinColumn(name="lista_id", referencedColumnName="id", nullable=false)
     */
    protected $listapreferenze;

    /**
     * @ORM\ManyToOne(targetEntity="Candidatiprincipali", inversedBy="listaxprincipales")
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
     * @return \App\Entity\Listaxprincipale
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
     * @return \App\Entity\Listaxprincipale
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
     * Set the value of candidato_principale_id.
     *
     * @param integer $candidato_principale_id
     * @return \App\Entity\Listaxprincipale
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
     * Set the value of off.
     *
     * @param boolean $off
     * @return \App\Entity\Listaxprincipale
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
     * @return \App\Entity\Listaxprincipale
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
     * @return \App\Entity\Listaxprincipale
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
     * Set Candidatiprincipali entity (many to one).
     *
     * @param \App\Entity\Candidatiprincipali $candidatiprincipali
     * @return \App\Entity\Listaxprincipale
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
        return array('id', 'lista_id', 'candidato_principale_id', 'off', 'posizione');
    }
}