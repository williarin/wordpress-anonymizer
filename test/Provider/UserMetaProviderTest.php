<?php

declare(strict_types=1);

namespace Williarin\WordpressAnonymizer\Test\Provider;

use Williarin\WordpressAnonymizer\Provider\UserMetaProvider;
use Williarin\WordpressAnonymizer\Test\TestCase;

class UserMetaProviderTest extends TestCase
{
    private UserMetaProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->provider = $this->container->get(UserMetaProvider::class);
    }

    public function testAnonymizeUserMeta(): void
    {
        $this->provider->anonymize();
        $userMeta = $this->getUserMeta();

        $original = [
            [
                'first_name' => '',
                'last_name' => '',
                'nickname' => 'admin',
                'description' => '',
            ],
            [
                'first_name' => 'Robert',
                'last_name' => 'Carter',
                'nickname' => 'bob',
                'description' => 'Nice man',
            ],
            [
                'first_name' => 'Ann',
                'last_name' => 'Jolly',
                'nickname' => 'ann',
                'description' => 'Nice woman',
            ],
            [
                'first_name' => 'William',
                'last_name' => 'Arin',
                'nickname' => 'will',
                'description' => 'Very nice man',
            ],
            [
                'first_name' => '',
                'last_name' => '',
                'nickname' => 'shopmanager',
                'description' => '',
            ],
            [
                'first_name' => '',
                'last_name' => '',
                'nickname' => 'justin',
                'description' => '',
            ],
            [
                'first_name' => '',
                'last_name' => '',
                'nickname' => 'otis',
                'description' => '',
            ],
        ];

        foreach ($original as $i => $meta) {
            foreach ($meta as $key => $value) {
                self::assertNotSame($value, $userMeta[$i][$key]);
            }
        }
    }

    private function getUserMeta(): array
    {
        return $this->connection->createQueryBuilder()
            ->addSelect("MAX(Case WHEN meta_key = 'first_name' THEN meta_value END) first_name")
            ->addSelect("MAX(Case WHEN meta_key = 'last_name' THEN meta_value END) last_name")
            ->addSelect("MAX(Case WHEN meta_key = 'nickname' THEN meta_value END) nickname")
            ->addSelect("MAX(Case WHEN meta_key = 'description' THEN meta_value END) description")
            ->from($this->tablePrefix . 'usermeta')
            ->groupBy('user_id')
            ->executeQuery()
            ->fetchAllAssociative()
        ;
    }
}
