# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

Tests must run inside the Docker container (requires PostgreSQL):
```bash
docker compose exec nginx php vendor/bin/phpunit
docker compose exec nginx php vendor/bin/phpunit --filter="testMethodName"
```

Code style (Laravel Pint):
```bash
vendor/bin/pint --test   # check
vendor/bin/pint          # auto-fix
```

## Architecture

This is a **publishable Laravel package** (`ronasit/laravel-chat`), not a standalone app. Source in `src/`, migrations published via `publishesMigrations()`.

### Service / Repository Pattern

- `ConversationService` and `MessageService` extend `EntityService` from `ronasit/laravel-helpers`, which proxies unknown method calls to the injected repository via `__call()` magic.
- `EntityControlTrait::update()` only applies fillable fields — passing a non-fillable field (e.g. `last_updated_at`) silently does nothing.
- All classes are bound through **contracts** (interfaces) in `ChatServiceProvider`. Always bind new classes there.

### Route Architecture

- `ChatServiceProvider` auto-loads `src/Http/routes/api.php` with `[auth, CheckManuallyRegisteredRoutesMiddleware]`.
- `ChatRouter` is a Route mixin. `Route::chat(ChatRouteActionEnum::X)` registers specific routes **without** the blocking middleware.
- `ChatRouter::$isBlockedBaseRoutes = true` causes base routes to return 404, used when the host app registers its own routes via `Route::chat()`.
- Adding a new route requires changes in **both** `api.php` and `ChatRouter` (and a new `ChatRouteActionEnum` case).

### Notifications & Broadcasting

- Notifications are broadcast-only; faked in tests via `Notification::fake()`.
- `assertBroadcastNotificationSent()` in `TestCase` compares notification payloads against JSON fixtures.
- Notification classes, resources, and broadcast resources are all swappable via contracts.

## Database Schema

Key tables and constraints:

| Table | Key columns | Notes |
|---|---|---|
| `conversations` | `id`, `creator_id` (nullable FK), `type` enum(private\|group), `title`, `cover_id`, `last_updated_at` | `last_updated_at` is NOT fillable |
| `conversation_member` | `conversation_id`, `member_id` | Pivot; unique on pair |
| `messages` | `id`, `conversation_id`, `sender_id`, `text`, `attachment_id` | Global scope appends `is_read` |
| `read_messages` | `message_id`, `user_id`, `created_at` | Replaces old `messages.is_read` boolean |
| `pinned_messages` | `conversation_id`, `message_id` | Tracks pinned messages per conversation |

## Test Structure

- **Framework**: Orchestra Testbench + PHPUnit; PostgreSQL required.
- Each test class has a `dump.sql` fixture that seeds the database in `setUp()`.
- JSON fixtures in `tests/fixtures/` are asserted via `assertEqualsFixture()`.
- Set `protected bool $globalExportMode = true` on a test class to regenerate JSON fixtures.
- `ConversationTest` and `MessageTest` are the primary integration test files.

## Configuration

`config/chat.php` exposes:
- `classes.user_model` — the host app's User model class
- `classes.media_model` — media model (from `ronasit/laravel-media`)
- `default_channels` — broadcast channel(s) for notifications