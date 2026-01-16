<?php

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\Article\Article;
use App\Domain\Article\Repository\ArticleRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class ArticleRepository implements ArticleRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function findByExternalId(string $externalId): ?Article
    {
        return $this->em->getRepository(Article::class)
            ->findOneBy(['externalId' => $externalId]);
    }

    public function upsert(Article $article): void
    {
        $this->em->persist($article);
        $this->em->flush();
    }
}