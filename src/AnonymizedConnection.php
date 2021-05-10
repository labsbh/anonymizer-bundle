<?php

declare(strict_types=1);

namespace Labsbh\AnonymizerBundle;

use Doctrine\DBAL\Connection;
use Labsbh\AnonymizerBundle\GeneratorFactory\GeneratorFactoryInterface;

class AnonymizedConnection
{
    private GeneratorFactoryInterface $generatorFactory;

    private Connection $connection;

    private array $config;

    public function __construct(GeneratorFactoryInterface $generatorFactory, Connection $connection, array $config)
    {
        $this->generatorFactory = $generatorFactory;
        $this->connection       = $connection;
        $this->config           = $config;
    }

    public function getAnonymizedTables(): array
    {
        $anonymizedTables = [];

        foreach ($this->config as $tableName => $tableConfig) {
            $fields = [];

            foreach ($tableConfig['fields'] as $fieldName => $fieldConfig) {
                $generator = $this->generatorFactory->getGenerator($fieldConfig);
                $fields[]  = new AnonymizedField($fieldName, $generator);
            }

            $primaryKey = $tableConfig['primary_key'] ?? null;

            $anonymizedTables[] = new AnonymizedTable($tableName, $primaryKey, $fields, $tableConfig['truncate'] ?? false);
        }

        return $anonymizedTables;
    }

    /**
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }
}
