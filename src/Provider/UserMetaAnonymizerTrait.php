<?php

declare(strict_types=1);

namespace Williarin\WordpressAnonymizer\Provider;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;

/**
 * @property Connection $connection
 * @property string     $tablePrefix
 * @property string[]   $data
 *
 * @method void replaceMetaValues(bool $useTransactions, Result $rows, string $tableName, string $idFieldName)
 */
trait UserMetaAnonymizerTrait
{
    public function anonymize(bool $useTransactions = true): void
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('user_id')
            ->from($this->tablePrefix . 'usermeta')
            ->groupBy('user_id')
        ;

        foreach (array_keys($this->data) as $key) {
            $queryBuilder
                ->addSelect(sprintf(
                    "MAX(Case WHEN meta_key = '%s' THEN meta_value END) %s",
                    is_array($this->data[$key]) ? $this->data[$key]['name'] : $key,
                    $key,
                ))
            ;
        }

        $userMeta = $queryBuilder->executeQuery();

        $this->replaceMetaValues($useTransactions, $userMeta, 'usermeta', 'user_id');
    }
}
