<?php

declare(strict_types=1);

namespace Labsbh\AnonymizerBundle\DependencyInjection;

use Labsbh\AnonymizerBundle\GeneratorFactory\GeneratorFactoryInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class LabsbhAnonymizerExtension extends Extension
{
    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.php');

        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $configGuesserDefinition = $container->findDefinition('labsbh_anonymizer.config_guesser');
        $configGuesserDefinition->setArgument('$hints', $config['hints']);

        $anonymizeCommandDefinition = $container->findDefinition('labsbh_anonymizer.anonymize_command');
        $anonymizeCommandDefinition->setArgument('$defaultConfig', $config);

        $container
            ->registerForAutoconfiguration(GeneratorFactoryInterface::class)
            ->addTag('labsbh_anonymizer.generator_factory');
    }
}
