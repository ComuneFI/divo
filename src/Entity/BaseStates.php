<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entity\States
 *
 * @ORM\Entity()
 * @ORM\Table(name="States")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"base":"BaseStates", "extended":"States"})
 */
class BaseStates
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
    protected $descr;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $nextstate;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $entity_ref;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entity\States
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
     * Set the value of descr.
     *
     * @param string $descr
     * @return \App\Entity\States
     */
    public function setDescr($descr)
    {
        $this->descr = $descr;

        return $this;
    }

    /**
     * Get the value of descr.
     *
     * @return string
     */
    public function getDescr()
    {
        return $this->descr;
    }

    /**
     * Set the value of code.
     *
     * @param string $code
     * @return \App\Entity\States
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get the value of code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set the value of nextstate.
     *
     * @param string $nextstate
     * @return \App\Entity\States
     */
    public function setNextstate($nextstate)
    {
        $this->nextstate = $nextstate;

        return $this;
    }

    /**
     * Get the value of nextstate.
     *
     * @return string
     */
    public function getNextstate()
    {
        return $this->nextstate;
    }

    /**
     * Set the value of entity_ref.
     *
     * @param string $entity_ref
     * @return \App\Entity\States
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
        return array('id', 'descr', 'code', 'nextstate', 'entity_ref');
    }
}