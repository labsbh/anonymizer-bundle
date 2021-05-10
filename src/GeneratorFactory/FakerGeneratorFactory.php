<?php

declare(strict_types=1);

namespace Labsbh\AnonymizerBundle\GeneratorFactory;

use Faker\Factory;
use Labsbh\AnonymizerBundle\Exception\MissingFormatterException;
use Labsbh\AnonymizerBundle\Exception\UnsupportedGeneratorException;
use Labsbh\AnonymizerBundle\Generator\Faker;
use Labsbh\AnonymizerBundle\Generator\GeneratorInterface;

class FakerGeneratorFactory extends Factory implements GeneratorFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getGenerator(array $config): GeneratorInterface
    {
        if ('faker' !== $config['generator']) {
            throw new UnsupportedGeneratorException($config['generator'] ?? null);
        }

        $formatter = $config['formatter'] ?? null;
        if (!$formatter) {
            throw new MissingFormatterException('You need to chose a "formatter" for "faker" generator');
        }

        $locale = $config['locale'] ?? self::DEFAULT_LOCALE;
        $generator = Factory::create($locale);

        $seed = $config['seed'] ?? false;
        if ($seed) {
            $generator->seed($seed);
        }

        if ($config['unique'] ?? false) {
            $generator = $generator->unique();
        }

        $optional = $config['optional'] ?? false;
        if ($optional) {
            $generator = $generator->optional($optional);
        }

        $arguments = $config['arguments'] ?? [];

        return new Faker($generator, $formatter, $arguments, $config);
    }
}
