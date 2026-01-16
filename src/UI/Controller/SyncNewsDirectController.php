<?php

namespace App\UI\Controller;

use App\Application\Service\SyncNewsService;
use App\UI\DTO\SyncNewsRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class SyncNewsDirectController extends AbstractController
{
    public function __construct(
        private SyncNewsService $syncService,
        private SerializerInterface $serializer
    ) {}

    #[Route('/api/sync-news-direct', name: 'api_sync_news_direct', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        /** @var SyncNewsRequest $data */
        $data = $this->serializer->deserialize(
            $request->getContent(),
            SyncNewsRequest::class,
            'json'
        );

        try {
            // Sync directly (synchronous, no Messenger/Redis)
            $result = $this->syncService->sync(
                keyword: $data->keyword ?? '',
                language: $data->language ?? 'en',
                fromDate: $data->from ?? date('Y-m-d', strtotime('-7 days')),
                toDate: $data->to ?? date('Y-m-d')
            );

            return $this->json([
                'status' => 'ok',
                'message' => 'News synced successfully',
                'result' => $result,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

