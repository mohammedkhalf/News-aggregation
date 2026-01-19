# News Aggregation Service

A News Aggregation system built with Domain-Driven Design (DDD) architecture.

## Architecture Overview

The system is organized into four main layers following DDD principles:

### 1. Domain Layer (`src/Domain/`)
- **Entities**: `Article`,
- **Value Objects**: `Language`, `Source`
- **Interfaces**: `ArticleRepositoryInterface`


### 2. Application Layer (`src/Application/`)
- `Command`: `AsyncNewsCommand` 
- `Handler`: `AsyncNewsHandler` - - Messenger Handler


### 3. Infrastructure Layer (`src/Infrastructure/`)
- **Persistence**: `ArticleRepository` - Database persistence
- **Migrations**:  Database Migration Files
- **GNewsHttpClient**: `GNewsHttpClient` - For Calling External Service

### 4. Presentation Layer (`src/UI/`)
- **Controllers**: `ArticlesController`, `AsyncNewsController`
- **Console**: Console Command for Background Sync
 - **DTO**:  `ArticleListRequest`, `ArticleListResponse`, `ArticleResponse`, `SyncNewsRequest`

## Features

### News Aggregation Ingestion
- Third Party Integration : `https://gnews.io/`
- Handle pagination and API limits
- Store retrieved articles locally
- Implement a synchronization mechanism
- Avoid duplicate articles.
- Update existing articles if content changes.
- Ensure idempotency.
- Symfony Console Command
- secured API endpoint
- Get Articles Locally 
- Get Article By ID
- phpstan for Code Quality & Static Analysis

### Installtion

- **Run Docker Containers**: `docker compose up`
- **Run Migrations**: `docker compose exec php php bin/console doctrine:migrations:migrate`
- **Sync Using Command Line**: `podman compose exec php php bin/console app:async-news \
  --keyword=technology \
  --language=en
  `
- **Sync Using Secure API Endpoint ** By : `http://localhost:8080/api/async-news` with Raw json object as Example `{
  "keyword": "technology",
  "language": "en",
  "from": "2026-01-10",
  "to": "2026-01-17"
  }
  `

- **Message Broker Consume (Symfony Messenger)**: `docker compose exec php php bin/console messenger:consume async -vv`

- **Code Quality and Analysis**: 
  - `docker compose exec php composer require --dev phpstan/phpstan phpstan/phpstan-doctrine phpstan/phpstan-symfony phpstan/extension-installer`
  - `docker compose exec php composer phpstan`

### Endpoint

- **GET /api/articles (with pagination, filtering, sorting)**: `GET http://localhost:8080/api/articles?language=en&source=BBC&sortBy=publishedAt&sortOrder=DESC&page=1&limit=10`
- **GET /api/articles/{id}**: `GET http://localhost:8080/api/articles/{id}`

### Unit Test

- Running Unit Test : `docker compose exec php vendor/bin/phpunit`