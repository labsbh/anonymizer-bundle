<?php

declare(strict_types=1);

namespace Labsbh\AnonymizerBundle;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Labsbh\AnonymizerBundle\Event\AfterAnonymizedRowEvent;
use Labsbh\AnonymizerBundle\Event\AfterAnonymizedTableEvent;
use Labsbh\AnonymizerBundle\Event\BeforeAnonymizedRowEvent;
use Labsbh\AnonymizerBundle\Event\BeforeAnonymizedTableEvent;
use Labsbh\AnonymizerBundle\Exception\InvalidAnonymousValueException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Anonymizer
{
    private ?EventDispatcherInterface $dispatcher;

    public function __construct(?EventDispatcherInterface $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @phpstan-param AnonymizedTable[] $tables
     *
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function __invoke(Connection $connection, array $tables): void
    {
        $dbPlatform = $connection->getDatabasePlatform();
        if (null === $dbPlatform) {
            throw new \RuntimeException('Can’t get database platform');
        }

        foreach ($tables as $table) {
            if ($table->isTruncate()) {
                $connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');
                $truncateQuery = $dbPlatform->getTruncateTableSql($table->getName());
                $connection->executeQuery($truncateQuery);
                $connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');
            } else {
                if (0 === count($table->getPrimaryKey())) {
                    $this->anonymizeByTable($connection, $table);
                    continue;
                }
                $this->anonymizeByRow($connection, $table);
            }
        }
    }

    /**
     * @throws Exception
     * @throws InvalidAnonymousValueException
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    private function anonymizeByRow(Connection $connection, AnonymizedTable $table): void
    {
        $fieldNames  = $table->getFieldNames();
        $primaryKeys = $table->getPrimaryKey();

        $stmt = $connection
            ->createQueryBuilder()
            ->select(implode(',', $fieldNames))
            ->from($table->getName())
            ->execute();
        while ($row = $stmt->fetchAssociative()) {
            $initialValues = [];
            $values        = [];
            foreach ($table->getFields() as $field) {
                $anonymizedValue           = $field->generate();
                $fieldName                 = $field->getName();
                $initialValues[$fieldName] = $row[$fieldName];

                if (null !== $anonymizedValue && !\is_string($anonymizedValue)) {
                    throw new InvalidAnonymousValueException('Generated value must be null or string');
                }

                $values[$fieldName] = $anonymizedValue;
            }

            $primaryKeyValues = [];
            foreach ($primaryKeys as $primaryKey) {
                $primaryKeyValues[$primaryKey] = $row[$primaryKey];
            }

            if (null !== $this->dispatcher) {
                $this->dispatcher->dispatch(new BeforeAnonymizedRowEvent($table->getName(), $initialValues, $values, $primaryKeyValues));
            }

            $connection->update($table->getName(), $values, $primaryKeyValues);

            if (null !== $this->dispatcher) {
                $this->dispatcher->dispatch(new AfterAnonymizedRowEvent($table->getName(), $initialValues, $values, $primaryKeyValues));
            }
        }
    }

    /**
     * @throws Exception
     */
    private function anonymizeByTable(Connection $connection, AnonymizedTable $table): void
    {
        $values = [];
        foreach ($table->getFields() as $field) {
            $values[$field->getName()] = $field->generate();
        }

        if (null !== $this->dispatcher) {
            $this->dispatcher->dispatch(new BeforeAnonymizedTableEvent($table->getName(), $values));
        }

        $connection->update($table->getName(), $values, [true => true]);

        if (null !== $this->dispatcher) {
            $this->dispatcher->dispatch(new AfterAnonymizedTableEvent($table->getName(), $values));
        }
    }
}
