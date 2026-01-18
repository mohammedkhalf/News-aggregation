<?php

namespace App\Application\Service;

use App\Application\Command\AsyncNewsCommand;
use App\Domain\Article\Repository\ArticleRepositoryInterface;
use App\Infrastructure\Api\GNewsHttpClient;
use App\Domain\Article\Article;

class SyncNewsService
{
    public function __construct(
        private GNewsHttpClient $client,
        private ArticleRepositoryInterface $repository
    ) {}

    public function sync(
        string $keyword,
        string $language,
        string $fromDate,
        string $toDate
    ): array {
        $createdCount = 0;
        $updatedCount = 0;
        $errors = [];

        try {
            // Create a command object for the client
            $command = new AsyncNewsCommand(
                keyword: $keyword,
                language: $language,
                fromDate: $fromDate,
                toDate: $toDate
            );

            foreach ($this->client->fetch($command) as $dto) {
                try {
                    $article = $this->repository->findByExternalId($dto['externalId']);
                    
                    if (!$article) {
                        $article = Article::createFromDto($dto);
                        $this->repository->upsert($article);
                        $createdCount++;
                    } else {
                        $article->updateContent($dto);
                        $this->repository->upsert($article);
                        $updatedCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = [
                        'externalId' => $dto['externalId'] ?? 'unknown',
                        'error' => $e->getMessage(),
                    ];
                }
            }
        } catch (\Exception $e) {
            $errors[] = [
                'externalId' => 'general Exception',
                'error' => $e->getMessage(),
            ];
        }

        return [
            'created' => $createdCount,
            'updated' => $updatedCount,
            'total' => $createdCount + $updatedCount,
            'errors' => $errors,
        ];
    }
}

