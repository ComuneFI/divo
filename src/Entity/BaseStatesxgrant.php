<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\Statesxgrant
 *
 * @ORM\Entity()
 * @ORM\Table(name="Statesxgrant")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"base":"BaseStatesxgrant", "extended":"Statesxgrant"})
 */
class BaseStatesxgrant
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="`current`", type="string", length=255, nullable=true)
     */
    protected $current;

    /**
     * @ORM\Column(name="`next`", type="string", length=255, nullable=true)
     */
    protected $next;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $enabled;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $crackable;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $entity_ref;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\Statesxgrant
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
     * Set the value of current.
     *
     * @param string $current
     * @return \App\Entity\Statesxgrant
     */
    public function setCurrent($current)
    {
        $this->current = $current;

        return $this;
    }

    /**
     * Get the value of current.
     *
     * @return string
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * Set the value of next.
     *
     * @param string $next
     * @return \App\Entity\Statesxgrant
     */
    public function setNext($next)
    {
        $this->next = $next;

        return $this;
    }

    /**
     * Get the value of next.
     *
     * @return string
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * Set the value of enabled.
     *
     * @param boolean $enabled
     * @return \App\Entity\Statesxgrant
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get the value of enabled.
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set the value of crackable.
     *
     * @param boolean $crackable
     * @return \App\Entity\Statesxgrant
     */
    public function setCrackable($crackable)
    {
        $this->crackable = $crackable;

        return $this;
    }

    /**
     * Get the value of crackable.
     *
     * @return boolean
     */
    public function getCrackable()
    {
        return $this->crackable;
    }

    /**
     * Set the value of entity_ref.
     *
     * @param string $entity_ref
     * @return \App\Entity\Statesxgrant
     */
    public function setEntityRef($entity_ref)
    {
        $this->entity_ref = $entity_ref;

        return $this;
    }

    /**
     * Get the value of entity_ref.
     *
     * @return string
     */
    public function getEntityRef()
    {
        return $this->entity_ref;
    }

    public function __sleep()
    {
        return array('id', 'current', 'next', 'enabled', 'crackable', 'entity_ref');
    }
}