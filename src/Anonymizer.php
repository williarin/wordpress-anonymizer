<?php

declare(strict_types=1);

namespace Williarin\WordpressAnonymizer;

use Williarin\WordpressAnonymizer\Provider\AbstractAnonymizerProvider;

final class Anonymizer
{
    /**
     * @param AbstractAnonymizerProvider[] $providers
     */
    public function __construct(
        private iterable $providers
    ) {
    }

    public function anonymize(): void
    {
        foreach ($this->providers as $provider) {
            $provider->anonymize();
        }
    }
}
