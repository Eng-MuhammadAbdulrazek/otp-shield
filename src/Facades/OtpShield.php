<?php

namespace OtpShield\Facades;

use Illuminate\Support\Facades\Facade;

class OtpShield extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'otpshield';
    }
}
