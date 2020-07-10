<?php

namespace App\Service;

class DivoEntity {

    private $name;
    private $offable;

    /**
     * Accept a name/description and set for default off-able to true.
     */
    public function __construct(string $name) 
    {
        $this->name = $name;
        $this->offable = true;
    }

    /**
     * Return the name set during its creation.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Return true if is off-able, otherwise is false
     */
    public function isOffable() 
    {
        return $this->offable;
    }

    /**
     * Switch down the off-able property.
     * This means that this entity doesn't have off flag to manage.
     */
    public function unsetOffable()
    {
        $this->offable = false;
    }

}