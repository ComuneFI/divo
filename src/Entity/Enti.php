<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\BaseEnti;

/**
 * App\Entity\Enti
 *
 * @ORM\Entity()
 */
class Enti extends BaseEnti
{
    public function __toString(){
        return $this->getDescrizione();
    }
}