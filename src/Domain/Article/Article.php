<?php

namespace App\Domain\Article;

use App\Domain\Article\ValueObject\Language;
use App\Domain\Article\ValueObject\Source;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'articles')]
#[ORM\UniqueConstraint(name: 'uniq_external_id', columns: ['external_id'])]
class Article
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private string $id;

    #[ORM\Column(name: 'external_id', type: 'text', unique: true)]
    private string $externalId;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $content;

    #[ORM\Column(name: 'source_name', type: 'string', length: 255)]
    private string $sourceName;

    #[ORM\Column(type: 'string', length: 255)]
    private string $url;

    #[ORM\Column(name: 'image_url', type: 'string', length: 255, nullable: true)]
    private ?string $imageUrl;

    #[ORM\Column(type: 'string', length: 2)]
    private string $language;

    #[ORM\Column(name: 'published_at', type: 'datetime_immutable')]
    private DateTimeImmutable $publishedAt;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private DateTimeImmutable $updatedAt;

    private function __construct(
        string $externalId,
        string $title,
        ?string $description,
        ?string $content,
        Source $source,
        string $url,
        ?string $imageUrl,
        Language $language,
        DateTimeImmutable $publishedAt
    ) {
        $this->id = self::generateUuid();
        $this->externalId = $externalId;
        $this->title = $title;
        $this->description = $description;
        $this->content = $content;
        $this->sourceName = (string) $source;
        $this->url = $url;
        $this->imageUrl = $imageUrl;
        $this->language = (string) $language;
        $this->publishedAt = $publishedAt;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    private static function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public static function createFromDto(array $dto): self
    {
        return new self(
            $dto['externalId'],
            $dto['title'],
            $dto['description'] ?? null,
            $dto['content'] ?? null,
            new Source($dto['source']),
            $dto['url'],
            $dto['imageUrl'] ?? null,
            new Language($dto['language']),
            new DateTimeImmutable($dto['publishedAt'])
        );
    }

    public function updateContent(array $dto): void
    {
        $this->title = $dto['title'];
        $this->description = $dto['description'] ?? null;
        $this->content = $dto['content'] ?? null;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }

}