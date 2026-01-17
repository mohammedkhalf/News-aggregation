<?php

namespace App\Application\Handler;

use App\Application\Command\SyncNewsCommand;
use App\Domain\Article\Repository\ArticleRepositoryInterface;
use App\Infrastructure\Api\GNewsHttpClient;

class SyncNewsHandler
{
    public function __construct(
        private GNewsHttpClient $client,
        private ArticleRepositoryInterface $repository
    ) {}

    public function __invoke(SyncNewsCommand $command): void
    {
        foreach ($this->client->fetch($command) as $dto) {
            $article = $this->repository->findByExternalId($dto['externalId']);
            if (!$article) {
                $article = \App\Domain\Article\Article::createFromDto($dto);
                $this->repository->upsert($article);
            } else {
                $article->updateContent($dto);
                $this->repository->upsert($article);
            }
        }
    }

}