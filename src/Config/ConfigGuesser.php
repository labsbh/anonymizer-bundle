<?php

declare(strict_types=1);

namespace Labsbh\AnonymizerBundle\Config;

use Doctrine\DBAL\Connection;
use Labsbh\AnonymizerBundle\Exception\GuesserMissingHintException;
use function Symfony\Component\String\u;

class ConfigGuesser
{
    /**
     * @var ConfigGuesserHint[]
     */
    private array $hints;

    public function __construct(array $hints)
    {
        $this->hints = [];
        foreach ($hints as $hint) {
            $this->hints[] = new ConfigGuesserHint($hint['formatter'], $hint['words'], $hint['arguments'] ?? [], $hint['date'] ?? false, $hint['unique'] ?? false);
        }
    }

    public function guess(Connection $connection): array
    {
        $hints         = [];
        $schemaManager = $connection->getSchemaManager();
        if (null === $schemaManager) {
            throw new \InvalidArgumentException('Connection should have a schema manager');
        }

        foreach ($schemaManager->listTables() as $table) {
            $tableName = $table->getName();

            $primaryKeys = $table->getPrimaryKey();
            if (null !== $primaryKeys) {
                $hints[$tableName]['primary_key'] = $primaryKeys->getColumns();
            }

            foreach ($table->getColumns() as $column) {
                $columnName = $column->getName();

                try {
                    $hints[$tableName]['fields'][$columnName] = $this->guessColumn($columnName);
                } catch (GuesserMissingHintException) {
                }
            }

            if (empty($hints[$tableName]['fields'])) {
                unset($hints[$tableName]);
            }
        }

        return $hints;
    }

    private function guessColumn(string $name): array
    {
        $columnWords = u($name)->snake()->split('_');

        foreach ($this->hints as $hint) {
            if (!empty(array_intersect($columnWords, $hint->getWords()))) {
                return $hint->getConfigArray();
            }
        }

        throw new GuesserMissingHintException();
    }
}
