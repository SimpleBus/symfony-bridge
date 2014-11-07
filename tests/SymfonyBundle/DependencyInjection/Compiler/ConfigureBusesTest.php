<?php

namespace SimpleBus\SymfonyBridge\Tests\SymfonyBundle\DependencyInjection\Compiler;

use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\ConfigureBuses;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ConfigureBusesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    private $mainBusId = 'main_bus';

    private $busTag = 'specialized_bus';

    protected function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->container->addCompilerPass(new ConfigureBuses($this->mainBusId, $this->busTag));
    }

    /**
     * @test
     */
    public function it_configures_a_chain_of_buses_according_to_the_given_priorities()
    {
        $this->createBusDefinition('bus100', 100);
        $this->createBusDefinition('bus-100', -100);
        $this->createBusDefinition('bus200', 200);

        $this->container->compile();

        $this->busChainContains(array('bus200', 'bus100', 'bus-100'));

        $this->mainBusIs('bus200');
    }

    private function createBusDefinition($id, $priority)
    {
        $definition = new Definition('stdClass');
        $definition->addTag($this->busTag, array('priority' => $priority));

        $this->container->setDefinition($id, $definition);

        return $definition;
    }

    private function busChainContains(array $ids)
    {
        $numberOfIds = count($ids);
        for ($i = 0; $i < $numberOfIds - 1; $i++) {
            $definition = $this->container->getDefinition($ids[$i]);
            $nextDefinitionId = $ids[$i + 1];
            $this->assertEquals(
                array(
                    array('setNext', array(new Reference($nextDefinitionId)))
                ),
                $definition->getMethodCalls()
            );
        }
    }

    private function mainBusIs($busId)
    {
        $this->assertSame($busId, (string) $this->container->getAlias($this->mainBusId));
    }
}
