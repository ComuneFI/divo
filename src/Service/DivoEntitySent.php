<?php

namespace App\Service;

use App\Service\RTSentableInterface;

class DivoEntitySent extends DivoEntity implements RTSentableInterface {


    /**
     * It has sent flag.
     */
    public function isSentable()
    {
        return true;
    }

}