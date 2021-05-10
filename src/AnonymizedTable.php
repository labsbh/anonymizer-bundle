<?php

declare(strict_types=1);

namespace Labsbh\AnonymizerBundle;

class AnonymizedTable
{
    private string $name;

    /**
     * @phpstan-var string[]
     */
    private array $primaryKey;

    /**
     * @phpstan-var AnonymizedField[]
     */
    private array $fields;

    private bool $truncate;

    /**
     * @phpstan-param string[]          $primaryKey
     * @phpstan-param AnonymizedField[] $fields
     */
    public function __construct(string $name, array $primaryKey, array $fields, bool $truncate)
    {
        if ($truncate && $fields) {
            throw new \InvalidArgumentException(
                \sprintf('Invalid configuration of table "%s". Table can be either anonymized or truncated.', $name)
            );
        }

        $this->name       = $name;
        $this->primaryKey = $primaryKey;
        $this->fields     = $fields;
        $this->truncate   = $truncate;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @phpstan-return string[]
     */
    public function getPrimaryKey(): array
    {
        return $this->primaryKey;
    }

    /**
     * @phpstan-return AnonymizedField[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Get the names of all known fields.
     *
     * @return string[]
     */
    public function getFieldNames(): array
    {
        return [
            ...$this->primaryKey,
            ...array_map(static fn (AnonymizedField $field): string => $field->getName(), $this->fields),
        ];
    }

    public function isTruncate(): bool
    {
        return $this->truncate;
    }
}
