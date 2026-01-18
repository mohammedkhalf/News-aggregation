<?php

declare(strict_types=1);

namespace App\UI\Controller;

use App\Domain\Article\Article;
use App\Domain\Article\Repository\ArticleRepositoryInterface;
use App\UI\DTO\ArticleListRequest;
use App\UI\DTO\ArticleListResponse;
use App\UI\DTO\ArticleResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticlesController extends AbstractController
{
    private const ALLOWED_SORT_FIELDS = ['publishedAt', 'createdAt', 'title'];
    private const UUID_PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';

    public function __construct(
        private readonly ArticleRepositoryInterface $repository
    ) {
    }

    #[Route('/api/articles', name: 'api_articles_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        try {
            // Parse query parameters
            $listRequest = ArticleListRequest::fromRequest($request->query->all());

            if (!in_array($listRequest->sortBy, self::ALLOWED_SORT_FIELDS, true)) {
                return $this->json([
                    'error' => 'Invalid sortBy parameter. Allowed values: ' . implode(', ', self::ALLOWED_SORT_FIELDS),
                ], Response::HTTP_BAD_REQUEST);
            }

            // Build filters
            $filters = [
                'language' => $listRequest->language,
                'source' => $listRequest->source,
                'sortBy' => $listRequest->sortBy,
                'sortOrder' => $listRequest->sortOrder,
            ];

            $filters = array_filter($filters, static fn($value): bool => $value !== null);

            // Fetch articles
            $articles = $this->repository->findAllWithFilters(
                $filters,
                $listRequest->limit,
                $listRequest->getOffset()
            );

            // Get total count
            $total = $this->repository->countWithFilters($filters);

            $articleResponses = array_map(
                static fn(Article $article): array => ArticleResponse::fromArticle($article)->toArray(),
                $articles
            );

            // Calculate pages
            $pages = (int) ceil($total / $listRequest->limit);

            // Build response
            $response = new ArticleListResponse(
                data: $articleResponses,
                total: $total,
                page: $listRequest->page,
                limit: $listRequest->limit,
                pages: $pages
            );

            return $this->json($response->toArray(), Response::HTTP_OK);

        } catch (\Exception $e) {
            return $this->json([
                'error' => 'An error occurred while fetching articles',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/articles/{id}', name: 'api_articles_get', methods: ['GET'])]
    public function get(string $id): JsonResponse
    {
        try {
            if (!preg_match(self::UUID_PATTERN, $id)) {
                return $this->json([
                    'error' => 'Invalid article ID format',
                ], Response::HTTP_BAD_REQUEST);
            }

            $article = $this->repository->findById($id);

            if ($article === null) {
                return $this->json([
                    'error' => 'Article not found',
                ], Response::HTTP_NOT_FOUND);
            }

            $response = ArticleResponse::fromArticle($article);

            return $this->json([
                'data' => $response->toArray(),
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return $this->json([
                'error' => 'An error occurred while fetching the article',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}