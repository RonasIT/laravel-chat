# Laravel Chat Plugin

[![Coverage Status](https://coveralls.io/repos/github/RonasIT/laravel-chat/badge.svg?branch=master)](https://coveralls.io/github/RonasIT/laravel-chat?branch=master)

## Table of Contents

- [Introduction](#introduction)
- [Installation](#installation)
- [Configuration](#configuration)
  - [Queue](#queue)
- [Integration with LaravelSwagger](#integration-with-laravelswagger)
- [API Endpoints](#api-endpoints)
  - [Conversations](#conversations)
  - [Messages](#messages)
- [Broadcast Events](#broadcast-events)
  - [Channel Authorization](#channel-authorization)
  - [Events](#events)
  - [Customizing Notifications](#customizing-notifications)
- [Contributing](#contributing)
- [License](#license)

## Introduction

This plugin adds the ability for users to work with chat functionalities in a Laravel application.

## Installation

1. Install the package using the following command:

```sh
composer require ronasit/laravel-chat
```

2. Publish the package configuration:

``` sh
php artisan vendor:publish --provider=RonasIT\\Chat\\ChatServiceProvider
```

3. If you use non default `App\Models\User` model - update `chat.classes.user_model` config.

## Configuration

The package config is merged with the published `config/chat.php`, so keys added by newer versions
of the package resolve to their defaults even if your published copy predates them. You only need to
re-publish the config when you want to see the new keys in the file itself.

### Queue

Chat notifications are queued. By default, they land on the application's default queue, which means
they compete with every other job you dispatch. Since chat events are latency-sensitive, you will
usually want them on a dedicated queue served by their own worker.

Set the queue name with the `CHAT_BROADCAST_QUEUE` environment variable, or directly through the
`chat.broadcast_queue` config key:

```dotenv
CHAT_BROADCAST_QUEUE=chat
```

The config value may also be a backed enum, so an application that keeps its queue names in one can
use it directly:

```php
// config/chat.php
'broadcast_queue' => QueueEnum::Chat,
```

Sending one chat notification pushes two jobs, and the setting moves **both** of them:

| Job | Pushed by |
|-----|-----------|
| `Illuminate\Notifications\SendQueuedNotifications` | the notification sender, when the notification is sent |
| `Illuminate\Broadcasting\BroadcastEvent` | the broadcast channel, while the first job is running |

Leaving the value as `null` keeps the default behavior — both jobs go to the application's default
queue.

The setting applies to every channel listed in `chat.default_channels`, including a channel class you
substituted for the default one. Notifications outside this package are unaffected.

Run a worker for the queue you configured:

```sh
php artisan queue:work --queue=chat
```

## Integration with [LaravelSwagger](https://github.com/RonasIT/laravel-swagger)

This package includes an OpenAPI documentation file. To include it in your project's documentation, you need to register it in the `auto-doc.additional_paths` config:

`vendor/ronasit/laravel-chat/documentation.json`

## API Endpoints

All routes are registered by default, you can change the route registration by calling `Route::chat()` in your routes file (e.g. `routes/api.php`).
- feel free to call `Route::chat()` helper inside any route wrappers like `group`, `prefix`, etc. to wrap package routes; **In this case default package's routes start to return 404.**
- calling `Route::chat()` without args will add all package routes inside the calling helper place;
- calling `Route::chat()` with `ChatRouteActionEnum` cases as arguments will register **only** the specified routes — all others are automatically disabled:

```php
// routes/api.php
use RonasIT\Chat\Enums\ChatRouteActionEnum;

Route::prefix('api')->group(function () {
    Route::middleware('auth_group')->group(function () {
        Route::chat(
            ChatRouteActionEnum::ConversationsSearch,
            ChatRouteActionEnum::MessageCreate,
        );
    });
});
```

All endpoints are using the current auth user context, so it critical to always wrap them into the `auth` middleware.

### Conversations

| Method | URL | Description |
|--------|-----|-------------|
| `GET` | `/conversations` | List all conversations the authenticated user is a member of. |
| `GET` | `/conversations/{id}` | Retrieve a single conversation by its ID. |
| `DELETE` | `/conversations/{id}` | Delete a conversation. |
| `GET` | `/users/{userId}/conversation` | Get the private conversation between the authenticated user and the specified user. |

### Messages

| Method | URL | Description |
|--------|-----|-------------|
| `GET` | `/messages` | List of messages related to the current user's conversations. |
| `POST` | `/messages` | Create a new message. |
| `POST` | `/messages/{id}/read-to` | Mark all messages in the target message's conversation as read up to the specified message ID. |
| `POST` | `/messages/{id}/pin` | Pin a message to its conversation. |
| `POST` | `/messages/{id}/unpin` | Unpin a message from its conversation. |

## Broadcast Events

The package uses Laravel's broadcasting system to notify conversation members in real time. All events are delivered over private channels named `chat.{userId}`, where `{userId}` is the ID of the recipient.

### Channel Authorization

A user may listen on only his own private channel `chat.{userId}`. The channel is authorized when the authenticated user's ID matches `{userId}`.

### Events

#### `conversation.created`

Sent to all conversation members **except the creator** when a new conversation is created.

This event is triggered when:
- A new group conversation is created.
- A private conversation is created implicitly (e.g., when a user sends the first message to another user via `recipient_id`).

**Payload:**

```jsonc
{
    "id": 1,
    "type": "private|group",
    "title": "string or null",
    "last_updated_at": "2024-01-01T00:00:00.000000Z",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "last_message": { /* MessageResource */ }
}
```

---

#### `conversation.updated`

Sent to **all conversation members** when a conversation is modified.

This event is triggered when:
- A conversation's properties (e.g., title, cover) are updated.
- A new message is created (the conversation's `last_updated_at` is updated as a side effect).
- A message is pinned or unpinned in the conversation.

**Payload:**

```jsonc
{
    "id": 1,
    "type": "private|group",
    "title": "string or null",
    "last_updated_at": "2024-01-01T00:00:00.000000Z",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "last_message": { /* MessageResource */ },
    "pinned_messages": [ /* MessageResource */ ],
    "members_count": 2,
    "unread_messages_count": 0
}
```

---

#### `conversation.deleted`

Sent to all conversation members **except the user who deleted it** when a conversation is removed.

This event is triggered when:
- A conversation is deleted via `DELETE /conversations/{id}`.

**Payload:**

```json
{
    "id": 1
}
```

---

#### `message.created`

Sent to all conversation members **except the sender** when a new message is posted.

This event is triggered when:
- A message is created via `POST /messages`.

**Payload:**

```jsonc
{
    "id": 1,
    "text": "Hello!",
    "conversation_id": 1,
    "is_read": false,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z",
    "sender": { /* UserResource */ }
}
```

---

#### `message.updated`

Sent to **all conversation members** when the read status of one or more messages changes.

This event is triggered when:
- A user marks messages as read via `POST /messages/{id}/read-to`.

**Payload:**

```jsonc
{
    "id": 1,
    "text": "Hello!",
    "conversation_id": 1,
    "is_read": true,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z",
    "sender": { /* UserResource */ }
}
```

### Customizing Notifications

#### Building notifications through the container

Chat notifications are resolved through the container with **named** arguments:

```php
app(MessageCreatedNotificationContract::class, [
    'messageId' => $message->id,
    'recipientId' => $recipient->id,
]);
```

If you subclass a notification and rebind its contract, keep the same constructor parameter names.
Renaming them (for example `messageId` to `id`) makes the container unable to satisfy the argument
and it throws a `BindingResolutionException` at send time.

#### Overriding the broadcast payload

The payload is built by `getBroadcastData()`, which returns a plain array. The public
`toBroadcast()` lives in `BaseNotification`, wraps that array into a `BroadcastMessage` and applies
the configured queue — override `getBroadcastData()` and the queue keeps working:

```php
class CustomMessageCreatedNotification extends MessageCreatedNotification
{
    public function getBroadcastData(): array
    {
        $message = app(MessageServiceContract::class)
            ->with('sender')
            ->find($this->messageId);

        return [
            'data' => app(MyMessageResource::class, [
                'resource' => $message,
            ]),
        ];
    }
}
```

#### Overriding the queue for one notification

`chat.broadcast_queue` applies to every chat notification. To move a single one — for example
`conversation.updated`, which is sent to every member on every new message — subclass it, override
`getQueueName()` and rebind its contract:

```php
class HeavyConversationUpdatedNotification extends ConversationUpdatedNotification
{
    protected function getQueueName(): UnitEnum|string|null
    {
        return QueueEnum::ChatHeavy;
    }
}
```

```php
// AppServiceProvider::register()
$this->app->bind(
    ConversationUpdatedNotificationContract::class,
    HeavyConversationUpdatedNotification::class,
);
```

Both jobs read the queue from this single method, so they cannot end up on different queues.

## Contributing

Thank you for considering contributing to the Laravel Chat plugin! The contribution guide can be found in the [Contributing guide](CONTRIBUTING.md).

## License

Laravel Chat plugin is open-sourced software licensed under the [MIT license](LICENSE).