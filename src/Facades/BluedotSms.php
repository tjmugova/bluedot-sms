<?php

namespace Tjmugova\BluedotSms\Facades;

use Illuminate\Support\Facades\Facade;

class BluedotSms extends Facade
{
    /**
     * Get the binding in the IoC container
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'bluedotsms';
    }
}