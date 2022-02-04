<?php

declare(strict_types=1);

namespace Williarin\WordpressAnonymizer\Provider;

use Symfony\Component\Yaml\Yaml;

final class UserMetaProvider extends AbstractAnonymizerProvider
{
    use UserMetaAnonymizerTrait;

    protected function load(): void
    {
        $this->data = Yaml::parseFile(__DIR__ . '/../../config/provider/usermeta.yaml');
    }
}
