<?php
/**
 * Created by PhpStorm.
 * User: frost
 * Date: 5/25/16
 * Time: 11:45 PM
 */

namespace Lovelock\Kontainer\Service;


use Lovelock\Kontainer\Reference\ServiceReference;

class Right extends ServiceReference
{
    public function getMethodName()
    {
        return __METHOD__;
    }
}