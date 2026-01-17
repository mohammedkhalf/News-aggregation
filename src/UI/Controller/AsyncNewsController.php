<?php

namespace App\UI\Controller;

use App\UI\DTO\SyncNewsRequest;
use App\Application\Command\AsyncNewsCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AsyncNewsController extends AbstractController
{
    public function __construct(private MessageBusInterface $bus, private SerializerInterface $serializer){}
    #[Route('/api/async-news', name: 'api_async_news', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        /** @var SyncNewsRequest $data */
        $data = $this->serializer->deserialize(
            $request->getContent(),
            SyncNewsRequest::class,
            'json'
        );

        // Dispatch async message
        $this->bus->dispatch(
            new AsyncNewsCommand(
                keyword: $data->keyword,
                language: $data->language,
                fromDate: $data->from,
                toDate: $data->to
            )
        );

        return $this->json([
            'status' => 'ok',
            'message' => 'News sync job dispatched'
        ], Response::HTTP_ACCEPTED);
    }

}