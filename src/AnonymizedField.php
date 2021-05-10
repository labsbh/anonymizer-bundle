<?php

declare(strict_types=1);

namespace Labsbh\AnonymizerBundle;

use Labsbh\AnonymizerBundle\Generator\GeneratorInterface;

class AnonymizedField
{
    private string $name;

    private GeneratorInterface $generator;

    public function __construct(string $name, GeneratorInterface $generator)
    {
        $this->name      = $name;
        $this->generator = $generator;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function generate(): ?string
    {
        return ($this->generator)();
    }
}
