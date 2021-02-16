<?php

namespace SimpleBus\SymfonyBridge\Tests\Functional\SmokeTest;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use SimpleBus\SymfonyBridge\DoctrineOrmBridgeBundle;
use SimpleBus\SymfonyBridge\SimpleBusCommandBusBundle;
use SimpleBus\SymfonyBridge\SimpleBusEventBusBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Kernel;

class DoctrineTestKernel extends Kernel
{
    private string $tempDir;

    public function __construct(string $environment, bool $debug)
    {
        parent::__construct($environment, $debug);

        $this->tempDir = sys_get_temp_dir().'/simplebus-symfony-bridge';
    }

    /**
     * @return Bundle[]
     */
    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new SimpleBusCommandBusBundle(),
            new SimpleBusEventBusBundle(),
            new DoctrineOrmBridgeBundle(),
            new MonologBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(sprintf('%s/%s.yml', __DIR__, $this->environment));
    }

    public function getCacheDir(): string
    {
        return $this->tempDir.'/cache';
    }

    public function getLogDir(): string
    {
        return $this->tempDir.'/logs';
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    protected function getContainerClass(): string
    {
        return parent::getContainerClass().sha1(__NAMESPACE__);
    }
}
