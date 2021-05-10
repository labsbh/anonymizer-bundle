<?php

declare(strict_types=1);

namespace Labsbh\AnonymizerBundle\Config;

use function Symfony\Component\String\u;

final class ConfigGuesserHint
{
    private string $formatter;

    private array $words;

    private array $arguments;

    private bool $date;

    private bool $unique;

    public function __construct(string $formatter, array $words, array $arguments = [], bool $date = false, bool $unique = false)
    {
        $this->formatter = $formatter;
        $this->words     = array_map(static fn ($word) => u($word)->camel()->toString(), $words);
        $this->arguments = $arguments;
        $this->date      = $date;
        $this->unique    = $unique;
    }

    public function getWords(): array
    {
        return $this->words;
    }

    public function getConfigArray(): array
    {
        $config = [
            'generator' => 'faker',
            'formatter' => $this->formatter,
        ];

        if (!empty($this->arguments)) {
            $config['arguments'] = $this->arguments;
        }

        if ($this->unique) {
            $config['unique'] = $this->unique;
        }

        if ($this->date) {
            $config['date_format'] = 'Y-m-d H:i:s';
        }

        return $config;
    }

}
