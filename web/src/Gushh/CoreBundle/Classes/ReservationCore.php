<?php 

namespace Gushh\CoreBundle\Classes;

/**
* ReservationCore
*/
class ReservationCore
{
    protected $code;

    public function __construct($code)
    {
        $this->code = $code;
    }
}