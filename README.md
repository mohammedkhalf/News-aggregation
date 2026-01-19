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
- Get Article By Id

### Installtion

- **Run Containers**: docker compose up
- **Run Migrations**: docker compose exec php php bin/console doctrine:migrations:migrate 
- **Sync Using Command Line**: `podman compose exec php php bin/console app:async-news \
  --keyword=technology \
  --language=en
  `