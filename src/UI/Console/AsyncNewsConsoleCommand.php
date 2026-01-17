<?php

namespace App\UI\Console;

use App\Application\Command\AsyncNewsCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:async-news',
    description: 'Dispatch async GNews synchronization'
)]
final class AsyncNewsConsoleCommand extends Command
{
    public function __construct(
        private readonly MessageBusInterface $bus
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('keyword', null, InputOption::VALUE_OPTIONAL)
            ->addOption('language', null, InputOption::VALUE_OPTIONAL)
            ->addOption('from', null, InputOption::VALUE_OPTIONAL)
            ->addOption('to', null, InputOption::VALUE_OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->bus->dispatch(
            new AsyncNewsCommand(
                keyword: $input->getOption('keyword'),
                language: $input->getOption('language'),
                fromDate: $input->getOption('from'),
                toDate: $input->getOption('to'),
            )
        );

        $output->writeln('<info>Sync job dispatched</info>');

        return Command::SUCCESS;
    }
}
