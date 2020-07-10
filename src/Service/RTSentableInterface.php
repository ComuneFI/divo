<?php

namespace App\Service;

/**
 * Who wants to implement this interface must satisfy the contract of:
 * -isSentable()
 */
interface RTSentableInterface {

    public function isSentable();
 
} 