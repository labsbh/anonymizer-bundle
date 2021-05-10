<?php

declare(strict_types=1);

namespace Labsbh\AnonymizerBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class AfterAnonymizedTableEvent extends Event
{
    private string $table;

    /**
     * @phpstan-var array<string, string>
     */
    private array $values;

    /**
     * @phpstan-param array<string, string> $values
     */
    public function __construct(string $table, array $values)
    {
        $this->table  = $table;
        $this->values = $values;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @phpstan-return array<string, string>
     */
    public function getValues(): array
    {
        return $this->values;
    }
}
