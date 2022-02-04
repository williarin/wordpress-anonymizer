<?php

declare(strict_types=1);

namespace Williarin\WordpressAnonymizer\Provider;

interface AnonymizerProviderInterface
{
    public function anonymize(): void;
}
