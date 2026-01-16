<?php

namespace App\UI\Console;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:list-articles',
    description: 'List articles from the database'
)]
final class ListArticlesCommand extends Command
{
    public function __construct(
        private readonly Connection $connection
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'Limit number of articles', 10)
            ->addOption('count', 'c', InputOption::VALUE_NONE, 'Show only count');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('count')) {
            $count = $this->connection->fetchOne('SELECT COUNT(*) FROM articles');
            $io->success("Total articles in database: {$count}");
            return Command::SUCCESS;
        }

        $limit = (int) $input->getOption('limit');
        $articles = $this->connection->fetchAllAssociative(
            'SELECT id, external_id, title, source_name, language, published_at, created_at 
             FROM articles 
             ORDER BY created_at DESC 
             LIMIT ?',
            [$limit],
            [\PDO::PARAM_INT]
        );

        if (empty($articles)) {
            $io->warning('No articles found in the database.');
            return Command::SUCCESS;
        }

        $io->title('Articles in Database');
        $io->table(
            ['ID', 'External ID', 'Title', 'Source', 'Language', 'Published At', 'Created At'],
            array_map(function ($article) {
                return [
                    substr($article['id'], 0, 8) . '...',
                    substr($article['external_id'], 0, 30) . '...',
                    substr($article['title'], 0, 40) . '...',
                    $article['source_name'],
                    $article['language'],
                    $article['published_at'] ? (new \DateTime($article['published_at']))->format('Y-m-d H:i') : 'N/A',
                    $article['created_at'] ? (new \DateTime($article['created_at']))->format('Y-m-d H:i') : 'N/A',
                ];
            }, $articles)
        );

        $total = $this->connection->fetchOne('SELECT COUNT(*) FROM articles');
        $io->note("Total articles: {$total}");

        return Command::SUCCESS;
    }
}

