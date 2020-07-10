<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Rxliste;

/**
 * App\Entity\Rxliste
 *
 * @ORM\Entity()
 */
class Rxliste extends BaseRxliste
{
    public function __toString(){
        return $this->getListaDesc();
    }
}