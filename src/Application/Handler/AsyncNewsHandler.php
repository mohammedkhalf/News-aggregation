<?php

namespace App\Application\Handler;

use App\Application\Command\AsyncNewsCommand;
use App\Domain\Article\Repository\ArticleRepositoryInterface;
use App\Infrastructure\Api\GNewsHttpClient;
use App\Domain\Article\Article;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class AsyncNewsHandler
{
    public function __construct(
        private GNewsHttpClient $client,
        private ArticleRepositoryInterface $repository
    ) {}

    public function __invoke(AsyncNewsCommand $command): void
    {
        foreach ($this->client->fetch($command) as $dto) {
            $article = $this->repository->findByExternalId($dto['externalId']);
            if (!$article) {
                $article = Article::createFromDto($dto);
                $this->repository->upsert($article);
            } else {
                $article->updateContent($dto);
                $this->repository->upsert($article);
            }
        }
    }

}