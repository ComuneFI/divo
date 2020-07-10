<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\Entexevento
 *
 * @ORM\Entity()
 * @ORM\Table(name="Entexevento", indexes={@ORM\Index(name="fk_Entexevento_Enti1_idx", columns={"ente_id"}), @ORM\Index(name="fk_Entexevento_Eventi1_idx", columns={"evento_id"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"base":"BaseEntexevento", "extended":"Entexevento"})
 */
class BaseEntexevento
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
     * @ORM\Column(type="integer")
     */
    protected $evento_id;

    /**
     * @ORM\Column(name="`off`", type="boolean", nullable=true)
     */
    protected $off;

    /**
     * @ORM\ManyToOne(targetEntity="Enti", inversedBy="entexeventos")
     * @ORM\JoinColumn(name="ente_id", referencedColumnName="id", nullable=false)
     */
    protected $enti;

    /**
     * @ORM\ManyToOne(targetEntity="Eventi", inversedBy="entexeventos")
     * @ORM\JoinColumn(name="evento_id", referencedColumnName="id", nullable=false)
     */
    protected $eventi;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Entexevento
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
     * @return \App\Entity\Entexevento
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
     * Set the value of evento_id.
     *
     * @param integer $evento_id
     * @return \App\Entity\Entexevento
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
     * Set the value of off.
     *
     * @param boolean $off
     * @return \App\Entity\Entexevento
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
     * Set Enti entity (many to one).
     *
     * @param \App\Entity\Enti $enti
     * @return \App\Entity\Entexevento
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

    /**
     * Set Eventi entity (many to one).
     *
     * @param \App\Entity\Eventi $eventi
     * @return \App\Entity\Entexevento
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
        return array('id', 'ente_id', 'evento_id', 'off');
    }
}