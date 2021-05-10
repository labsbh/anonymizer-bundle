<?php

declare(strict_types=1);

namespace Labsbh\AnonymizerBundle\Generator;

class Faker implements GeneratorInterface
{
    private mixed $generator;

    private string $formatter;

    private array $arguments;

    private array $config;

    public function __construct(mixed $generator, string $formatter, array $arguments, array $config)
    {
        $this->generator = $generator;
        $this->formatter = $formatter;
        $this->arguments = $arguments;
        $this->config    = $config;
    }

    public function __invoke(): ?string
    {
        $value = $this->generator->format($this->formatter, $this->arguments);
        if ($value instanceof \DateTime) {
            $format = $this->config['date_format'] ?? null;

            return $value->format($format);
        }

        return $value;
    }
}
