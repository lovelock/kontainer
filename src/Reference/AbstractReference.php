<?php
/**
 * Created by PhpStorm.
 * User: frost
 * Date: 5/25/16
 * Time: 12:12 AM
 */

namespace Lovelock\Kontainer\Reference;


abstract class AbstractReference
{
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}