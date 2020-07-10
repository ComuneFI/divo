<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entity\Circoscrizioni
 *
 * @ORM\Entity()
 * @ORM\Table(name="Circoscrizioni", indexes={@ORM\Index(name="fk_Circoscrizioni_Eventi1_idx", columns={"evento_id"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"base":"BaseCircoscrizioni", "extended":"Circoscrizioni"})
 */
class BaseCircoscrizioni
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
    protected $evento_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $circ_desc;

    /**
     * @ORM\Column(name="`off`", type="boolean", nullable=true)
     */
    protected $off;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $id_source;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $id_target;

    /**
     * @ORM\OneToMany(targetEntity="Circoxcandidato", mappedBy="circoscrizioni")
     * @ORM\JoinColumn(name="id", referencedColumnName="circ_id", nullable=false)
     */
    protected $circoxcandidatos;

    /**
     * @ORM\OneToMany(targetEntity="Rxsezioni", mappedBy="circoscrizioni")
     * @ORM\JoinColumn(name="id", referencedColumnName="circo_id", nullable=false)
     */
    protected $rxsezionis;

    /**
     * @ORM\ManyToOne(targetEntity="Eventi", inversedBy="circoscrizionis")
     * @ORM\JoinColumn(name="evento_id", referencedColumnName="id", nullable=false)
     */
    protected $eventi;

    public function __construct()
    {
        $this->circoxcandidatos = new ArrayCollection();
        $this->rxsezionis = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Circoscrizioni
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
     * Set the value of evento_id.
     *
     * @param integer $evento_id
     * @return \App\Entity\Circoscrizioni
     */
    public function setEventoId($evento_id)
    {
        $this->evento_id = $evento_id;

        return $this;
    }

    /**
     * Get the value of evento_id.
     *
     * @return integer
     */
    public function getEventoId()
    {
        return $this->evento_id;
    }

    /**
     * Set the value of circ_desc.
     *
     * @param string $circ_desc
     * @return \App\Entity\Circoscrizioni
     */
    public function setCircDesc($circ_desc)
    {
        $this->circ_desc = $circ_desc;

        return $this;
    }

    /**
     * Get the value of circ_desc.
     *
     * @return string
     */
    public function getCircDesc()
    {
        return $this->circ_desc;
    }

    /**
     * Set the value of off.
     *
     * @param boolean $off
     * @return \App\Entity\Circoscrizioni
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
     * Set the value of id_source.
     *
     * @param string $id_source
     * @return \App\Entity\Circoscrizioni
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
     * @return \App\Entity\Circoscrizioni
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
     * Add Circoxcandidato entity to collection (one to many).
     *
     * @param \App\Entity\Circoxcandidato $circoxcandidato
     * @return \App\Entity\Circoscrizioni
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
     * @return \App\Entity\Circoscrizioni
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
     * Add Rxsezioni entity to collection (one to many).
     *
     * @param \App\Entity\Rxsezioni $rxsezioni
     * @return \App\Entity\Circoscrizioni
     */
    public function addRxsezioni(Rxsezioni $rxsezioni)
    {
        $this->rxsezionis[] = $rxsezioni;

        return $this;
    }

    /**
     * Remove Rxsezioni entity from collection (one to many).
     *
     * @param \App\Entity\Rxsezioni $rxsezioni
     * @return \App\Entity\Circoscrizioni
     */
    public function removeRxsezioni(Rxsezioni $rxsezioni)
    {
        $this->rxsezionis->removeElement($rxsezioni);

        return $this;
    }

    /**
     * Get Rxsezioni entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRxsezionis()
    {
        return $this->rxsezionis;
    }

    /**
     * Set Eventi entity (many to one).
     *
     * @param \App\Entity\Eventi $eventi
     * @return \App\Entity\Circoscrizioni
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
        return array('id', 'evento_id', 'circ_desc', 'off', 'id_source', 'id_target');
    }
}