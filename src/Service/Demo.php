<?php
/**
 * Created by PhpStorm.
 * User: frost
 * Date: 5/25/16
 * Time: 11:09 PM
 */

namespace Lovelock\Kontainer\Service;


class Demo
{
    private $company;
    private $salary;

    public function __construct($company, $salary)
    {
        $this->company = $company;
        $this->salary = $salary;
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
    public function getSalary()
    {
        return $this->salary;
    }

    /**
     * @param mixed $salary
     */
    public function setSalary($salary)
    {
        $this->salary = $salary;
    }
}