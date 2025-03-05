<?php

namespace App\Constants;

use ReflectionClass;

abstract class BaseConstant
{
    public static function getConstants()
    {
        $oClass = new ReflectionClass(static::class);

        return $oClass->getConstants();
    }
}
