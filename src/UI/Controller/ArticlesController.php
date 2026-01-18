<?php

namespace App\UI\Controller;

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
    public function __construct(
        private ArticleRepositoryInterface $repository
    ) {}

    #[Route('/api/articles', name: 'api_articles_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        try {
            // Parse query parameters
            $listRequest = ArticleListRequest::fromRequest($request->query->all());

            // Validate sortBy
            $allowedSortFields = ['publishedAt', 'createdAt', 'title'];
            if (!in_array($listRequest->sortBy, $allowedSortFields)) {
                return $this->json([
                    'error' => 'Invalid sortBy parameter. Allowed values: ' . implode(', ', $allowedSortFields),
                ], Response::HTTP_BAD_REQUEST);
            }

            // Build filters
            $filters = [
                'language' => $listRequest->language,
                'source' => $listRequest->source,
                'sortBy' => $listRequest->sortBy,
                'sortOrder' => $listRequest->sortOrder,
            ];

            // Remove null filters
            $filters = array_filter($filters, fn($value) => $value !== null);

            // Fetch articles
            $articles = $this->repository->findAllWithFilters(
                $filters,
                $listRequest->limit,
                $listRequest->getOffset()
            );

            // Get total count
            $total = $this->repository->countWithFilters($filters);

            // Convert to DTOs
            $articleResponses = array_map(
                fn($article) => ArticleResponse::fromArticle($article)->toArray(),
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
            // Validate UUID format
            if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $id)) {
                return $this->json([
                    'error' => 'Invalid article ID format',
                ], Response::HTTP_BAD_REQUEST);
            }

            $article = $this->repository->findById($id);

            if (!$article) {
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