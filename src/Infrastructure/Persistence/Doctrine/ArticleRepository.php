<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\Article\Article;
use App\Domain\Article\Repository\ArticleRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class ArticleRepository implements ArticleRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
    }

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

    public function findById(string $id): ?Article
    {
        return $this->em->getRepository(Article::class)
            ->find($id);
    }


    /**
     * @param array{language?: string, source?: string, sortBy?: string, sortOrder?: string} $filters
     * @return array<int, Article>
     */
    public function findAllWithFilters(array $filters, int $limit, int $offset): array
    {
        $qb = $this->createQueryBuilder($filters);

        $sortBy = $filters['sortBy'] ?? 'publishedAt';
        $sortOrder = $filters['sortOrder'] ?? 'DESC';

        $sortColumnMap = [
            'publishedAt' => 'a.publishedAt',
            'createdAt' => 'a.createdAt',
            'title' => 'a.title',
        ];

        $sortColumn = $sortColumnMap[$sortBy] ?? 'a.publishedAt';
        $qb->orderBy($sortColumn, $sortOrder);

        $qb->setMaxResults($limit)
            ->setFirstResult($offset);

        $result = $qb->getQuery()->getResult();

        return is_array($result) ? $result : [];
    }

    /**
     * @param array{language?: string, source?: string} $filters
     */
    public function countWithFilters(array $filters): int
    {
        $qb = $this->createQueryBuilder($filters);
        $qb->select('COUNT(a.id)');

        $result = $qb->getQuery()->getSingleScalarResult();

        return (int) $result;
    }

    /**
     * @param array{language?: string, source?: string} $filters
     */
    private function createQueryBuilder(array $filters): QueryBuilder
    {
        $qb = $this->em->createQueryBuilder()
            ->select('a')
            ->from(Article::class, 'a');

        // Filter by language
        if (!empty($filters['language'])) {
            $qb->andWhere('a.language = :language')
                ->setParameter('language', $filters['language']);
        }

        // Filter by source
        if (!empty($filters['source'])) {
            $qb->andWhere('a.sourceName = :source')
                ->setParameter('source', $filters['source']);
        }

        return $qb;
    }

}