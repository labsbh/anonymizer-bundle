<?php

declare(strict_types=1);

namespace Labsbh\AnonymizerBundle\GeneratorFactory;

use Labsbh\AnonymizerBundle\Exception\UnsupportedGeneratorException;
use Labsbh\AnonymizerBundle\Generator\Constant;
use Labsbh\AnonymizerBundle\Generator\GeneratorInterface;

class ConstantGeneratorFactory implements GeneratorFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getGenerator(array $config): GeneratorInterface
    {
        if ('constant' !== $config['generator']) {
            throw new UnsupportedGeneratorException($config['generator'] ?? null);
        }

        return new Constant($config['value']);
    }
}
