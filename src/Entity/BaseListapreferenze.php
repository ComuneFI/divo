<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entity\Listapreferenze
 *
 * @ORM\Entity()
 * @ORM\Table(name="Listapreferenze")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"base":"BaseListapreferenze", "extended":"Listapreferenze"})
 */
class BaseListapreferenze
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
    protected $lista_desc;

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
     * @ORM\OneToMany(targetEntity="Listaxprincipale", mappedBy="listapreferenze")
     * @ORM\JoinColumn(name="id", referencedColumnName="lista_id", nullable=false)
     */
    protected $listaxprincipales;

    /**
     * @ORM\OneToMany(targetEntity="Rxpreferenze", mappedBy="listapreferenze")
     * @ORM\JoinColumn(name="id", referencedColumnName="listapreferenze_id", nullable=false)
     */
    protected $rxpreferenzes;

    /**
     * @ORM\OneToMany(targetEntity="Rxscrutiniliste", mappedBy="listapreferenze")
     * @ORM\JoinColumn(name="id", referencedColumnName="lista_preferenze_id", nullable=false)
     */
    protected $rxscrutinilistes;

    /**
     * @ORM\OneToMany(targetEntity="Secondarioxlista", mappedBy="listapreferenze")
     * @ORM\JoinColumn(name="id", referencedColumnName="lista_id", nullable=false)
     */
    protected $secondarioxlistas;

    public function __construct()
    {
        $this->listaxprincipales = new ArrayCollection();
        $this->rxpreferenzes = new ArrayCollection();
        $this->rxscrutinilistes = new ArrayCollection();
        $this->secondarioxlistas = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Listapreferenze
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
     * Set the value of lista_desc.
     *
     * @param string $lista_desc
     * @return \App\Entity\Listapreferenze
     */
    public function setListaDesc($lista_desc)
    {
        $this->lista_desc = $lista_desc;

        return $this;
    }

    /**
     * Get the value of lista_desc.
     *
     * @return string
     */
    public function getListaDesc()
    {
        return $this->lista_desc;
    }

    /**
     * Set the value of id_source.
     *
     * @param string $id_source
     * @return \App\Entity\Listapreferenze
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
     * @return \App\Entity\Listapreferenze
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
     * @return \App\Entity\Listapreferenze
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
     * Add Listaxprincipale entity to collection (one to many).
     *
     * @param \App\Entity\Listaxprincipale $listaxprincipale
     * @return \App\Entity\Listapreferenze
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
     * @return \App\Entity\Listapreferenze
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
     * Add Rxpreferenze entity to collection (one to many).
     *
     * @param \App\Entity\Rxpreferenze $rxpreferenze
     * @return \App\Entity\Listapreferenze
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
     * @return \App\Entity\Listapreferenze
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
     * Add Rxscrutiniliste entity to collection (one to many).
     *
     * @param \App\Entity\Rxscrutiniliste $rxscrutiniliste
     * @return \App\Entity\Listapreferenze
     */
    public function addRxscrutiniliste(Rxscrutiniliste $rxscrutiniliste)
    {
        $this->rxscrutinilistes[] = $rxscrutiniliste;

        return $this;
    }

    /**
     * Remove Rxscrutiniliste entity from collection (one to many).
     *
     * @param \App\Entity\Rxscrutiniliste $rxscrutiniliste
     * @return \App\Entity\Listapreferenze
     */
    public function removeRxscrutiniliste(Rxscrutiniliste $rxscrutiniliste)
    {
        $this->rxscrutinilistes->removeElement($rxscrutiniliste);

        return $this;
    }

    /**
     * Get Rxscrutiniliste entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRxscrutinilistes()
    {
        return $this->rxscrutinilistes;
    }

    /**
     * Add Secondarioxlista entity to collection (one to many).
     *
     * @param \App\Entity\Secondarioxlista $secondarioxlista
     * @return \App\Entity\Listapreferenze
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
     * @return \App\Entity\Listapreferenze
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
        return array('id', 'lista_desc', 'id_source', 'id_target', 'off');
    }
}