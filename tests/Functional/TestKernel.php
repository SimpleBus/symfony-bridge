<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use SimpleBus\SymfonyBridge\SimpleBusCommandBusBundle;
use SimpleBus\SymfonyBridge\DoctrineOrmBridgeBundle;
use SimpleBus\SymfonyBridge\SimpleBusEventBusBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    private $tempDir;
    private $configPath;

    public function __construct($environment, $debug, $name, $configPath)
    {
        parent::__construct($environment, $debug);

        $this->tempDir = sys_get_temp_dir() . '/' . uniqid();
        mkdir($this->tempDir, 0777, true);

        $this->name = $name;
        $this->configPath = $configPath;
    }

    public function registerBundles()
    {
        return array(
            new DoctrineBundle(),
            new SimpleBusCommandBusBundle(),
            new SimpleBusEventBusBundle(),
            new DoctrineOrmBridgeBundle(),
            new MonologBundle()
        );
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->configPath);
    }

    public function getCacheDir()
    {
        return $this->tempDir . '/cache';
    }

    public function getLogDir()
    {
        return $this->tempDir . '/logs';
    }
}
