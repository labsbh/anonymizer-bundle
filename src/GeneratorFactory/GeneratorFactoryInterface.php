<?php

declare(strict_types=1);

namespace Labsbh\AnonymizerBundle\GeneratorFactory;

use Labsbh\AnonymizerBundle\Exception\UnsupportedGeneratorException;
use Labsbh\AnonymizerBundle\Generator\GeneratorInterface;

interface GeneratorFactoryInterface
{
    /**
     * @phpstan-param array<string, mixed> $config
     *
     * @throws UnsupportedGeneratorException
     */
    public function getGenerator(array $config): GeneratorInterface;
}
