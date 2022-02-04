<?php

declare(strict_types=1);

namespace Williarin\WordpressAnonymizer\Test\Provider;

use Williarin\WordpressAnonymizer\Provider\UserProvider;
use Williarin\WordpressAnonymizer\Test\TestCase;

class UserProviderTest extends TestCase
{
    private UserProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->provider = $this->container->get(UserProvider::class);
    }

    public function testAnonymizeUsers(): void
    {
        $this->provider->anonymize();
        $users = $this->getUsers();

        $original = [
            [
                'user_login' => 'admin',
                'user_pass' => '$P$B4GpYTuKoDZe3Weq0n6JeQtuFKVh/m.',
                'user_nicename' => 'admin',
                'user_email' => 'contact@example.com',
                'user_url' => 'http://localhost',
                'display_name' => 'admin',
            ],
            [
                'user_login' => 'bob',
                'user_pass' => '$P$BXBzOCmXP3jgN8KFiIjCdFPsmWFajN/',
                'user_nicename' => 'bob',
                'user_email' => 'bob@wp.local',
                'user_url' => '',
                'display_name' => 'bob',
            ],
            [
                'user_login' => 'ann',
                'user_pass' => '$P$BQNoq2BzYfmkLU5qYWUAUfqwCKBZQ90',
                'user_nicename' => 'ann',
                'user_email' => 'ann@wp.local',
                'user_url' => '',
                'display_name' => 'ann',
            ],
            [
                'user_login' => 'will',
                'user_pass' => '$P$BM3muhw3Gr05x9wlJMcAEX8n/xSVHX.',
                'user_nicename' => 'will',
                'user_email' => 'will@wp.local',
                'user_url' => '',
                'display_name' => 'will',
            ],
            [
                'user_login' => 'shopmanager',
                'user_pass' => '$P$Bhsc92ufyaPXpcdVq0M5BbMa2.Xwnf.',
                'user_nicename' => 'shopmanager',
                'user_email' => 'info@woocommerce.com',
                'user_url' => '',
                'display_name' => 'Shop Manager',
            ],
            [
                'user_login' => 'justin',
                'user_pass' => '$P$BNkush6nTDlLyHfCz6GiKqeJvsRq9s/',
                'user_nicename' => 'justin',
                'user_email' => 'justin@woo.local',
                'user_url' => '',
                'display_name' => 'justin',
            ],
            [
                'user_login' => 'otis',
                'user_pass' => '$P$BIsW3sWeHXDkYJx6CIbmHgzDVcUwdM/',
                'user_nicename' => 'otis',
                'user_email' => 'otis@woo.local',
                'user_url' => '',
                'display_name' => 'otis',
            ],
        ];

        foreach ($original as $i => $comment) {
            foreach ($comment as $key => $value) {
                self::assertNotSame($value, $users[$i][$key]);
            }
        }
    }

    private function getUsers(): array
    {
        return $this->connection->createQueryBuilder()
            ->select('user_login', 'user_pass', 'user_nicename', 'user_email', 'user_url', 'display_name')
            ->from($this->tablePrefix . 'users')
            ->executeQuery()
            ->fetchAllAssociative()
            ;
    }
}
