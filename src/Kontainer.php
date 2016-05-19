<?php

namespace Lovelock\Kontainer;

use Exception\KontainerException;
use Exception\ServiceNotFoundException;
use \ReflectionClass;

class Kontainer implements KontainerInterface
{
    private $services;

    private $serviceStore;

    public function __construct(array $services = [])
    {
        $this->services = $services;
        $this->serviceStore = [];
    }

    public function get($name)
    {
        if (!$this->has($name)) {
            throw new ServiceNotFoundException('Service not found: ' . $name);
        }

        if (!isset($this->serviceStore[$name])) {
            $this->serviceStore[$name] = $this->createService($name);
        }

        return $this->serviceStore[$name];
    }

    public function has($name)
    {
        return isset($this->services[$name]);
    }

    public function createService($name)
    {
        $entry = &$this->services[$name];

        if (!is_array($entry) || !isset($entry['class'])) {
            throw new KontainerException($name . ' service must be an array containing a key \'class\'');
        } elseif (!class_exists($entry['class'])) {
            throw new KontainerException($name . ' service class does not exist: ' . $entry['class']);
        } elseif (isset($entry['lock'])) {
            throw new KontainerException($name . ' contains circular reference.');
        }

        $entry['lock'] = true;

        $arguments = isset($entry['arguments']) ? $this->resolveArguments($entry['arguments']) : [];

        $reflector = new ReflectionClass($entry['class']);
        $service = $reflector->newInstanceArgs($arguments);

        if (isset($entry['calls'])) {
            $this->initializeService($service, $name, $entry['calls']);
        }

        return $service;
    }

    public function resolveArguments(array $argumentDefinitions)
    {
        $arguments = [];

        foreach ($argumentDefinitions as $argumentDefinition) {
            //TODO
            if ($argumentDefinition instanceof ServiceReference) {
                $argumentServiceName = $argumentDefinition->getName();

                $arguments[] = $this->get($argumentServiceName);
            }
        }
    }
}
