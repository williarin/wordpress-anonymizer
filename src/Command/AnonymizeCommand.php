<?php

declare(strict_types=1);

namespace Williarin\WordpressAnonymizer\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Williarin\WordpressAnonymizer\Anonymizer;

class AnonymizeCommand extends Command
{
    protected static $defaultName = 'app:anonymize';

    public function __construct(
        private Anonymizer $anonymizer
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->anonymizer->anonymize();

        $io = new SymfonyStyle($input, $output);
        $io->success('Database anonymized successfully.');

        return Command::SUCCESS;
    }
}
