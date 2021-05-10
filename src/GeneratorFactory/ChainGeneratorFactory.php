<?php

declare(strict_types=1);

namespace Labsbh\AnonymizerBundle\GeneratorFactory;

use Labsbh\AnonymizerBundle\Exception\UnsupportedGeneratorException;
use Labsbh\AnonymizerBundle\Generator\GeneratorInterface;

class ChainGeneratorFactory implements GeneratorFactoryInterface
{
    /**
     * @phpstan-var GeneratorFactoryInterface[]
     */
    private array $factories = [];

    public function addFactory(GeneratorFactoryInterface $factory): self
    {
        $this->factories[] = $factory;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getGenerator(array $config): GeneratorInterface
    {
        foreach ($this->factories as $factory) {
            try {
                return $factory->getGenerator($config);
            } catch (UnsupportedGeneratorException) {
            }
        }

        throw new UnsupportedGeneratorException($config['generator'] ?? null);
    }
}
