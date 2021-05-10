<?php

declare(strict_types=1);

namespace Labsbh\AnonymizerBundle\DependencyInjection\Compiler;

use Labsbh\AnonymizerBundle\GeneratorFactory\ChainGeneratorFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ChainGeneratorFactoryCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('labsbh_anonymizer.chain_generator_factory')) {
            return;
        }

        $chainGeneratorFactoryDefinition = $container->findDefinition('labsbh_anonymizer.chain_generator_factory');
        $generatorFactories              = $container->findTaggedServiceIds('labsbh_anonymizer.generator_factory');

        foreach ($generatorFactories as $id => $tags) {
            $factoryClass = $container->getDefinition($id)->getClass();
            if (!is_a($factoryClass, ChainGeneratorFactory::class, true)) {
                $chainGeneratorFactoryDefinition->addMethodCall('addFactory', [new Reference($id)]);
            }
        }
    }
}
