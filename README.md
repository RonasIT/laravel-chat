# Laravel Chat Plugin

[![Coverage Status](https://coveralls.io/repos/github/RonasIT/laravel-chat/badge.svg?branch=master)](https://coveralls.io/github/RonasIT/laravel-chat?branch=master)

## Table of Contents

- [Introduction](#introduction)
- [Installation](#installation)
- [Integration with LaravelSwagger](#integration-with-laravelswagger)
- [API Endpoints](#api-endpoints)
  - [Conversations](#conversations)
  - [Messages](#messages)
- [Broadcast Events](#broadcast-events)
  - [Channel Authorization](#channel-authorization)
  - [Events](#events)
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

3. Set your project's User model to the `chat.classes.user_model` config.

## Integration with [LaravelSwagger](https://github.com/RonasIT/laravel-swagger)

This package includes an OpenAPI documentation file. To include it in your project's documentation, you need to register it in the `auto-doc.additional_paths` config:

`vendor/ronasit/laravel-chat/documentation.json`

## API Endpoints

All routes are registered by default, you can change the route registration by calling `Route::chat()` in your routes file (e.g. `routes/api.php`).
- feel free to call `Route::chat()` helper inside any route wrappers like `group`, `prefix`, etc. to wrap package routes;
- calling `Route::chat()` without args will add all package routes inside the calling helper place;
- calling `Route::chat()` with `ChatRouteActionEnum` cases as arguments will register **only** the specified routes — all others are automatically disabled:

```php
// routes/api.php
use RonasIT\Chat\Enums\ChatRouteActionEnum;

Route::middleware('auth')->group(function () {
    Route::chat(
        ChatRouteActionEnum::ConversationsSearch,
        ChatRouteActionEnum::MessageCreate,
    );
});
```

All endpoints require authentication. The routes are protected by the `auth` middleware.

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

## Contributing

Thank you for considering contributing to the Laravel Chat plugin! The contribution guide can be found in the [Contributing guide](CONTRIBUTING.md).

## License

Laravel Chat plugin is open-sourced software licensed under the [MIT license](LICENSE).