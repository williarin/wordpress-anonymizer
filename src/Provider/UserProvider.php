<?php

declare(strict_types=1);

namespace Williarin\WordpressAnonymizer\Provider;

use Symfony\Component\Yaml\Yaml;

final class UserProvider extends AbstractAnonymizerProvider
{
    public function anonymize(): void
    {
        $users = $this->connection->createQueryBuilder()
            ->select('ID', ...array_keys($this->data))
            ->from($this->tablePrefix . 'users')
            ->executeQuery()
        ;

        $this->replaceValues($users, 'users');
    }

    protected function load(): void
    {
        $this->data = Yaml::parseFile(__DIR__ . '/../../config/provider/users.yaml');
    }
}
