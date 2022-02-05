<?php

declare(strict_types=1);

namespace Williarin\WordpressAnonymizer\Provider;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Faker\Generator;

abstract class AbstractAnonymizerProvider implements AnonymizerProviderInterface
{
    /** @var string[] */
    protected array $data;

    public function __construct(
        protected Connection $connection,
        protected Generator $faker,
        protected string $tablePrefix,
    ) {
        $this->load();
    }

    abstract protected function load(): void;

    protected function replaceValues(
        bool $useTransactions,
        Result $rows,
        string $tableName,
        string $idFieldName = 'ID'
    ): void {
        if ($useTransactions) {
            $this->connection->beginTransaction();
        }

        while (($row = $rows->fetchAssociative()) !== false) {
            foreach ($row as $key => $value) {
                if (!array_key_exists($key, $this->data)) {
                    continue;
                }

                $formatter = $this->data[$key];

                $this->connection->createQueryBuilder()
                    ->update($this->tablePrefix . $tableName)
                    ->set($key, ':value')
                    ->where(sprintf('%s = :id', $idFieldName))
                    ->setParameters([
                        'id' => $row[$idFieldName],
                        'value' => $this->faker->{$formatter},
                    ])
                    ->executeStatement()
                ;
            }
        }

        if ($useTransactions) {
            try {
                $this->connection->commit();
            } catch (\Exception $e) {
                $this->connection->rollBack();

                throw $e;
            }
        }
    }

    protected function replaceMetaValues(
        bool $useTransactions,
        Result $rows,
        string $tableName,
        string $idFieldName
    ): void {
        if ($useTransactions) {
            $this->connection->beginTransaction();
        }

        while (($row = $rows->fetchAssociative()) !== false) {
            foreach ($row as $key => $value) {
                if (!array_key_exists($key, $this->data)) {
                    continue;
                }

                $formatter = is_array($this->data[$key]) ? $this->data[$key]['type'] : $this->data[$key];

                $this->connection->createQueryBuilder()
                    ->update($this->tablePrefix . $tableName)
                    ->set('meta_value', ':value')
                    ->where('meta_key = :key')
                    ->andWhere(sprintf('%s = :id', $idFieldName))
                    ->setParameters([
                        'id' => $row[$idFieldName],
                        'key' => is_array($this->data[$key]) ? $this->data[$key]['name'] : $key,
                        'value' => $this->faker->{$formatter},
                    ])
                    ->executeStatement()
                ;
            }
        }

        if ($useTransactions) {
            try {
                $this->connection->commit();
            } catch (\Exception $e) {
                $this->connection->rollBack();

                throw $e;
            }
        }
    }
}
