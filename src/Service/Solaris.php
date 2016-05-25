<?php
/**
 * Created by PhpStorm.
 * User: frost
 * Date: 5/25/16
 * Time: 11:40 PM
 */

namespace Lovelock\Kontainer\Service;


use Lovelock\Kontainer\Reference\ServiceReference;

class Solaris extends ServiceReference
{
    private $right;

    public function __construct($name, Right $right)
    {
        parent::__construct($name);
        $this->right = $right;
    }

    public function getName()
    {
        return __CLASS__;
    }

    public function returnTrue()
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return mixed
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * @param mixed $right
     */
    public function setRight($right)
    {
        $this->right = $right;
    }
}