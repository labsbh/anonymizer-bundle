<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Doctrine\Persistence\ManagerRegistry;
use Labsbh\AnonymizerBundle\Anonymizer;
use Labsbh\AnonymizerBundle\Command\AnonymizeCommand;
use Labsbh\AnonymizerBundle\Command\GuessConfigCommand;
use Labsbh\AnonymizerBundle\Config\ConfigGuesser;
use Labsbh\AnonymizerBundle\GeneratorFactory\ChainGeneratorFactory;
use Labsbh\AnonymizerBundle\GeneratorFactory\ConstantGeneratorFactory;
use Labsbh\AnonymizerBundle\GeneratorFactory\FakerGeneratorFactory;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator
        ->services()
        ->defaults()
        ->autowire(false)
        ->autoconfigure(true);

    $services
        ->set('labsbh_anonymizer.constant_generator_factory', ConstantGeneratorFactory::class);
    $services
        ->set('labsbh_anonymizer.faker_generator_factory', FakerGeneratorFactory::class);
    $services
        ->set('labsbh_anonymizer.chain_generator_factory', ChainGeneratorFactory::class);

    $services
        ->set('labsbh_anonymizer.anonymizer', Anonymizer::class)
        ->alias(Anonymizer::class, 'labsbh_anonymizer.anonymizer');

    $services
        ->set('labsbh_anonymizer.config_guesser', ConfigGuesser::class);

    $services
        ->set('labsbh_anonymizer.anonymize_command', AnonymizeCommand::class)
        ->args(
            [
                service(ManagerRegistry::class),
                service('labsbh_anonymizer.chain_generator_factory'),
                service('labsbh_anonymizer.anonymizer'),
            ])
        ->tag('console.command', ['command' => 'labsbh:anonymizer:anonymize']);

    $services
        ->set('labsbh_anonymizer.guess_config_command', GuessConfigCommand::class)
        ->args(
            [
                service(ManagerRegistry::class),
                service('labsbh_anonymizer.config_guesser'),
                param('kernel.project_dir'),
            ])
        ->tag('console.command', ['command' => 'labsbh:anonymizer:guess']);
};
