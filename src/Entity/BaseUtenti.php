<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\Utenti
 *
 * @ORM\Entity()
 * @ORM\Table(name="Utenti", indexes={@ORM\Index(name="fk_Utenti_Enti1_idx", columns={"ente_id"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"base":"BaseUtenti", "extended":"Utenti"})
 */
class BaseUtenti
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
     * @ORM\Column(type="string", length=45)
     */
    protected $username;

    /**
     * @ORM\Column(type="text")
     */
    protected $psw;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $user_id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $data_evento;

    /**
     * @ORM\ManyToOne(targetEntity="Enti", inversedBy="utentis")
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
     * @return \App\Entity\Utenti
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
     * @return \App\Entity\Utenti
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
     * Set the value of username.
     *
     * @param string $username
     * @return \App\Entity\Utenti
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the value of username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the value of psw.
     *
     * @param string $psw
     * @return \App\Entity\Utenti
     */
    public function setPsw($psw)
    {
        $this->psw = $psw;

        return $this;
    }

    /**
     * Get the value of psw.
     *
     * @return string
     */
    public function getPsw()
    {
        return $this->psw;
    }

    /**
     * Set the value of user_id.
     *
     * @param integer $user_id
     * @return \App\Entity\Utenti
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * Get the value of user_id.
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set the value of data_evento.
     *
     * @param \DateTime $data_evento
     * @return \App\Entity\Utenti
     */
    public function setDataEvento($data_evento)
    {
        $this->data_evento = $data_evento;

        return $this;
    }

    /**
     * Get the value of data_evento.
     *
     * @return \DateTime
     */
    public function getDataEvento()
    {
        return $this->data_evento;
    }

    /**
     * Set Enti entity (many to one).
     *
     * @param \App\Entity\Enti $enti
     * @return \App\Entity\Utenti
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
        return array('id', 'ente_id', 'username', 'psw', 'user_id', 'data_evento');
    }
}