<?php

declare(strict_types=1);

namespace Williarin\WordpressAnonymizer\Provider;

use Symfony\Component\Yaml\Yaml;

final class CommentProvider extends AbstractAnonymizerProvider
{
    public function anonymize(): void
    {
        $comments = $this->connection->createQueryBuilder()
            ->select('comment_ID', ...array_keys($this->data))
            ->from($this->tablePrefix . 'comments')
            ->executeQuery()
        ;

        $this->replaceValues($comments, 'comments', 'comment_ID');
    }

    protected function load(): void
    {
        $this->data = Yaml::parseFile(__DIR__ . '/../../config/provider/comments.yaml');
    }
}
