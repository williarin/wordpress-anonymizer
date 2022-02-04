<?php

declare(strict_types=1);

namespace Williarin\WordpressAnonymizer\Test\Provider;

use Williarin\WordpressAnonymizer\Provider\CommentProvider;
use Williarin\WordpressAnonymizer\Test\TestCase;

class CommentProviderTest extends TestCase
{
    private CommentProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->provider = $this->container->get(CommentProvider::class);
    }

    public function testAnonymizeComments(): void
    {
        $this->provider->anonymize();
        $comments = $this->getComments();

        $original = [
            [
                'comment_author' => 'A WordPress Commenter',
                'comment_author_email' => 'wapuu@wordpress.example',
                'comment_author_url' => 'https://wordpress.org/',
                'comment_author_IP' => '',
            ],
            [
                'comment_author' => 'Mr Robinson',
                'comment_author_email' => 'robinson@wp.local',
                'comment_author_url' => '',
                'comment_author_IP' => '201.202.203.204',
            ],
            [
                'comment_author' => 'Jeff Park',
                'comment_author_email' => 'jeff@wp.local',
                'comment_author_url' => '',
                'comment_author_IP' => '201.202.203.205',
            ],
            [
                'comment_author' => 'Linda Johnson',
                'comment_author_email' => 'linda@wp.local',
                'comment_author_url' => '',
                'comment_author_IP' => '201.202.203.206',
            ],
        ];

        foreach ($original as $i => $comment) {
            foreach ($comment as $key => $value) {
                self::assertNotSame($value, $comments[$i][$key]);
            }
        }
    }

    private function getComments(): array
    {
        return $this->connection->createQueryBuilder()
            ->select('comment_author', 'comment_author_email', 'comment_author_url', 'comment_author_IP')
            ->from($this->tablePrefix . 'comments')
            ->executeQuery()
            ->fetchAllAssociative()
            ;
    }
}
