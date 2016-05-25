<?php

namespace Lovelock\Kontainer;


use Lovelock\Kontainer\Exception\KontainerException;
use Lovelock\Kontainer\Exception\ServiceNotFoundException;
use ReflectionClass;

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
            if ($argumentDefinition instanceof ServiceReference) {
                $argumentServiceName = $argumentDefinition->getName();

                $arguments[] = $this->get($argumentServiceName);
            } else {
                $arguments[] = $argumentDefinition;
            }
        }

        return $arguments;
    }

    public function initializeService($service, $name, array $callDefinitions)
    {
        foreach ($callDefinitions as $callDefinition) {
            if (!is_array($callDefinition) || !isset($callDefinition['method'])) {
                throw new KontainerException($name . ' must be arrays containing a \'method\' key');
            } elseif (!is_callable($service, $callDefinition['method'])) {
                throw new KontainerException($name . ' asks for a callable method ' . $callDefinition['method']);
            }

            $arguments = isset($callDefinition['arguments']) ? $this->resolveArguments($callDefinition['arguments']) : [];
            call_user_func_array([$service, $callDefinition['method']], $arguments);
        }
    }
}
