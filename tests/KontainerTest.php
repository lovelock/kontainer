<?php
/**
 * Created by PhpStorm.
 * User: frost
 * Date: 5/25/16
 * Time: 10:59 PM
 */

namespace Lovelock\Kontainer\Test;


use Lovelock\Kontainer\Exception\KontainerException;
use Lovelock\Kontainer\Kontainer;
use Lovelock\Kontainer\Reference\ServiceReference;
use Lovelock\Kontainer\Service\Demo;
use Lovelock\Kontainer\Service\Right;
use Lovelock\Kontainer\Service\Solaris;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPUnit_Framework_TestCase;


class KontainerTest extends PHPUnit_Framework_TestCase
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger('kontainer');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../debug.log', Logger::DEBUG));
    }

    public function testContainerHasService()
    {
        $services = [
            'demo' => [
                'name' => 'demo',
            ]
        ];

        $container = new Kontainer($services);

        $this->assertTrue($container->has('demo'));
        $this->assertFalse($container->has('test'));
    }

    public function testContainerCreateService()
    {
        $services = [
            'demo' => [
                'name' => 'demo',
                'class' => Demo::class,
                'arguments' => [
                    'mycompany',
                    'mysalary',
                ]
            ],
        ];

        $container = new Kontainer($services);
        try {
            $demoService = $container->createService('demo');
        } catch (KontainerException $e) {
            $demoService = false;
            $this->logger->debug('create service failed ' . $e->getMessage());
        }

        $this->assertTrue($demoService->returnTrue());
        $this->assertEquals('mycompany', $demoService->getCompany());
        $this->assertEquals('mysalary', $demoService->getSalary());

        try {
            $testService = $container->createService('test');
        } catch (KontainerException $e) {
            $testService = false;
            $this->logger->debug('create service failed ' . $e->getMessage());
        }

        $this->assertFalse($testService);

    }

    public function testCreateServiceWithServiceAsParameter()
    {
        $services = [
            'demo' => [
                'name' => 'demo',
                'class' => Demo::class,
                'arguments' => [
                    'solaris',
                    'mysalary',
                ]
            ],
            'solaris' => [
                'name' => 'solaris',
                'class' => Solaris::class,
                'arguments' => [
                    Solaris::class,
                    new Right('right'),
                ],
            ],
            'right' => [
                'name' => 'right',
                'class' => Right::class,
                'arguments' => [
                    Right::class,
                ],
            ],
        ];

        $container = new Kontainer($services);
        try {
            $demoService = $container->createService('demo');
        } catch (KontainerException $e) {
            $demoService = false;
            $this->logger->debug('create service failed ' . $e->getMessage());
        }

        $this->assertTrue($demoService->returnTrue());
        $this->assertNotEquals('mycompany', $demoService->getCompany());
        $this->assertEquals('mysalary', $demoService->getSalary());

        try {
            $solarisService = $container->createService('solaris');
        } catch (KontainerException $e) {
            $solarisService = false;
            $this->logger->debug('create service failed ' . $e->getMessage());
        }

        $this->assertEquals('Lovelock\\Kontainer\\Service\\Solaris', $solarisService->getName());
        $this->assertEquals('Lovelock\\Kontainer\\Service\\Right::getMethodName', $solarisService->getRight()->getMethodName());
    }

    public function testIsInstanceOfReference()
    {
        $services = [
            'demo' => [
                'name' => 'demo',
                'class' => Demo::class,
                'arguments' => [
                    'mycompany',
                    'mysalary',
                ]
            ],
            'right' => [
                'name' => 'right',
                'class' => Right::class,
                'arguments' => [
                    Right::class,
                ],
            ],
        ];

        $container = new Kontainer($services);
        try {
            $demoService = $container->createService('demo');
        } catch (KontainerException $e) {
            $demoService = false;
            $this->logger->debug('create service failed ' . $e->getMessage());
        }

        $this->assertEquals('Lovelock\\Kontainer\\Service\\Demo', $demoService->getName());
        $this->assertFalse($demoService instanceof ServiceReference);

        try {
            $rightService = $container->createService('right');
        } catch (KontainerException $e) {
            $rightService = false;
            $this->logger->debug('create service failed ' . $e->getMessage());
        }

        $this->assertTrue($rightService instanceof ServiceReference);
    }
}
