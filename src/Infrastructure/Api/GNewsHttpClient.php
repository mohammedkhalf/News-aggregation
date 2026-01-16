<?php

namespace App\Infrastructure\Api;

use App\Application\Command\SyncNewsCommand;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GNewsHttpClient
{
    public function __construct(private HttpClientInterface $client, private string $apiKey) {}

    /**
     * @param SyncNewsCommand|object{keyword: string, language: string, fromDate: string, toDate: string} $command
     */
    public function fetch(SyncNewsCommand $command): iterable
    {
        $page = 1;
        do {
            $response = $this->client->request('GET', 'https://gnews.io/api/v4/search', [
                'query' => [
                    'q' => $command->keyword,
                    'lang' => $command->language,
                    'from' => $command->fromDate,
                    'to' => $command->toDate,
                    'page' => $page,
                    'token' => $this->apiKey,
                ],
            ]);

            $data = $response->toArray();
            foreach ($data['articles'] ?? [] as $article) {
                yield [
                    'externalId' => $article['url'], // URL as unique externalId
                    'title' => $article['title'],
                    'description' => $article['description'] ?? null,
                    'content' => $article['content'] ?? null,
                    'source' => $article['source']['name'],
                    'url' => $article['url'],
                    'imageUrl' => $article['image'] ?? null,
                    'language' => $article['language'] ?? 'en',
                    'publishedAt' => $article['publishedAt'],
                ];
            }

            $page++;
        } while (!empty($data['articles']));
    }

}