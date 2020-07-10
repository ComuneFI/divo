<?php

namespace App\Service;

use App\Service\RTSentableInterface;

class DivoEntityStatic extends DivoEntity implements RTSentableInterface {

    /**
     * It doesn't have sent flag.
     */
    public function isSentable()
    {
        return false;
    }

}