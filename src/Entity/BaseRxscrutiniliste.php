<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\Rxscrutiniliste
 *
 * @ORM\Entity()
 * @ORM\Table(name="Rxscrutiniliste", indexes={@ORM\Index(name="fk_Scrutiniliste_Listapreferenze1_idx", columns={"lista_preferenze_id"}), @ORM\Index(name="fk_Scrutiniliste_Rxsezioni1_idx", columns={"rxsezione_id"}), @ORM\Index(name="fk_Rxscrutiniliste_Actionlogs1_idx", columns={"actionlogs_id"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"base":"BaseRxscrutiniliste", "extended":"Rxscrutiniliste"})
 */
class BaseRxscrutiniliste
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
    protected $lista_preferenze_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $rxsezione_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $voti_tot_lista;

    /**
     * @ORM\Column(name="`off`", type="boolean", nullable=true)
     */
    protected $off;

    /**
     * @ORM\Column(name="`timestamp`", type="datetime", nullable=true)
     */
    protected $timestamp;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    protected $sent;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $ins_date;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $actionlogs_id;

    /**
     * @ORM\ManyToOne(targetEntity="Listapreferenze", inversedBy="rxscrutinilistes")
     * @ORM\JoinColumn(name="lista_preferenze_id", referencedColumnName="id", nullable=false)
     */
    protected $listapreferenze;

    /**
     * @ORM\ManyToOne(targetEntity="Rxsezioni", inversedBy="rxscrutinilistes")
     * @ORM\JoinColumn(name="rxsezione_id", referencedColumnName="id", nullable=false)
     */
    protected $rxsezioni;

    /**
     * @ORM\ManyToOne(targetEntity="Actionlogs", inversedBy="rxscrutinilistes")
     * @ORM\JoinColumn(name="actionlogs_id", referencedColumnName="id")
     */
    protected $actionlogs;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Rxscrutiniliste
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
     * Set the value of lista_preferenze_id.
     *
     * @param integer $lista_preferenze_id
     * @return \App\Entity\Rxscrutiniliste
     */
    public function setListaPreferenzeId($lista_preferenze_id)
    {
        $this->lista_preferenze_id = $lista_preferenze_id;

        return $this;
    }

    /**
     * Get the value of lista_preferenze_id.
     *
     * @return integer
     */
    public function getListaPreferenzeId()
    {
        return $this->lista_preferenze_id;
    }

    /**
     * Set the value of rxsezione_id.
     *
     * @param integer $rxsezione_id
     * @return \App\Entity\Rxscrutiniliste
     */
    public function setRxsezioneId($rxsezione_id)
    {
        $this->rxsezione_id = $rxsezione_id;

        return $this;
    }

    /**
     * Get the value of rxsezione_id.
     *
     * @return integer
     */
    public function getRxsezioneId()
    {
        return $this->rxsezione_id;
    }

    /**
     * Set the value of voti_tot_lista.
     *
     * @param integer $voti_tot_lista
     * @return \App\Entity\Rxscrutiniliste
     */
    public function setVotiTotLista($voti_tot_lista)
    {
        $this->voti_tot_lista = $voti_tot_lista;

        return $this;
    }

    /**
     * Get the value of voti_tot_lista.
     *
     * @return integer
     */
    public function getVotiTotLista()
    {
        return $this->voti_tot_lista;
    }

    /**
     * Set the value of off.
     *
     * @param boolean $off
     * @return \App\Entity\Rxscrutiniliste
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
     * @return \App\Entity\Rxscrutiniliste
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
     * Set the value of sent.
     *
     * @param integer $sent
     * @return \App\Entity\Rxscrutiniliste
     */
    public function setSent($sent)
    {
        $this->sent = $sent;

        return $this;
    }

    /**
     * Get the value of sent.
     *
     * @return integer
     */
    public function getSent()
    {
        return $this->sent;
    }

    /**
     * Set the value of ins_date.
     *
     * @param \DateTime $ins_date
     * @return \App\Entity\Rxscrutiniliste
     */
    public function setInsDate($ins_date)
    {
        $this->ins_date = $ins_date;

        return $this;
    }

    /**
     * Get the value of ins_date.
     *
     * @return \DateTime
     */
    public function getInsDate()
    {
        return $this->ins_date;
    }

    /**
     * Set the value of actionlogs_id.
     *
     * @param integer $actionlogs_id
     * @return \App\Entity\Rxscrutiniliste
     */
    public function setActionlogsId($actionlogs_id)
    {
        $this->actionlogs_id = $actionlogs_id;

        return $this;
    }

    /**
     * Get the value of actionlogs_id.
     *
     * @return integer
     */
    public function getActionlogsId()
    {
        return $this->actionlogs_id;
    }

    /**
     * Set Listapreferenze entity (many to one).
     *
     * @param \App\Entity\Listapreferenze $listapreferenze
     * @return \App\Entity\Rxscrutiniliste
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
     * Set Rxsezioni entity (many to one).
     *
     * @param \App\Entity\Rxsezioni $rxsezioni
     * @return \App\Entity\Rxscrutiniliste
     */
    public function setRxsezioni(Rxsezioni $rxsezioni = null)
    {
        $this->rxsezioni = $rxsezioni;

        return $this;
    }

    /**
     * Get Rxsezioni entity (many to one).
     *
     * @return \App\Entity\Rxsezioni
     */
    public function getRxsezioni()
    {
        return $this->rxsezioni;
    }

    /**
     * Set Actionlogs entity (many to one).
     *
     * @param \App\Entity\Actionlogs $actionlogs
     * @return \App\Entity\Rxscrutiniliste
     */
    public function setActionlogs(Actionlogs $actionlogs = null)
    {
        $this->actionlogs = $actionlogs;

        return $this;
    }

    /**
     * Get Actionlogs entity (many to one).
     *
     * @return \App\Entity\Actionlogs
     */
    public function getActionlogs()
    {
        return $this->actionlogs;
    }

    public function __sleep()
    {
        return array('id', 'lista_preferenze_id', 'rxsezione_id', 'voti_tot_lista', 'off', 'timestamp', 'sent', 'ins_date', 'actionlogs_id');
    }
}