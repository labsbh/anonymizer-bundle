<?php

declare(strict_types=1);

namespace Labsbh\AnonymizerBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class BeforeAnonymizedRowEvent extends Event
{
    private string $table;

    /**
     * @phpstan-var array<string, mixed>
     */
    private array $initialValues;

    /**
     * @phpstan-var array<string, string>
     */
    private array $values;

    /**
     * @phpstan-var array<string, mixed>
     */
    private array $primaryKeys;

    /**
     * @phpstan-param array<string, string> $values
     * @phpstan-param array<string, mixed>  $initialValues
     * @phpstan-param array<string, mixed>  $primaryKeys
     */
    public function __construct(string $table, array $initialValues, array &$values, array &$primaryKeys)
    {
        $this->table         = $table;
        $this->initialValues = $initialValues;
        $this->values        = $values;
        $this->primaryKeys   = $primaryKeys;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @phpstan-return array<string, mixed>
     */
    public function getInitialValues(): array
    {
        return $this->initialValues;
    }

    /**
     * @phpstan-return array<string, string>
     */
    public function &getValues(): array
    {
        return $this->values;
    }

    /**
     * @phpstan-return array<string, mixed>
     */
    public function &getPrimaryKeys(): array
    {
        return $this->primaryKeys;
    }
}
