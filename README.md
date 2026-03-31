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

3. For Laravel <= 5.5 add `ronasit\Chat\ChatServiceProvider::class` to the `app.providers` list in config.
4. Set your project's User model to the `chat.classes.user_model` config.
5. All routes are registered by default, you can change the route registration by calling `Route::chat()` in your routes file (e.g. `routes/api.php`).
   - feel free to call `Route::chat()` helper inside any route wrappers like `group`, `prefix`, etc. to wrap package routes;
   - calling `Route::chat()` without args will add all package route inside the calling helper place;
   - calling `Route::chat()` with any args will add only routes with chosen actions;

## Integration with [LaravelSwagger](https://github.com/RonasIT/laravel-swagger)

This package includes an OpenAPI documentation file. To include it in your project's documentation, you need to register it in the `auto-doc.additional_paths` config:

`vendor/ronasit/laravel-chat/documentation.json`

## API Endpoints

All endpoints require authentication. The routes are protected by the `auth` middleware.

### Conversations

| Method | URL | Action | Description |
|--------|-----|--------|-------------|
| `GET` | `/conversations` | `conversations_search` | List all conversations the authenticated user is a member of. |
| `GET` | `/conversations/{id}` | `conversation_get` | Retrieve a single conversation by its ID. |
| `DELETE` | `/conversations/{id}` | `conversation_delete` | Delete a conversation. |
| `GET` | `/users/{userId}/conversation` | `conversation_get_by_user` | Get the private conversation between the authenticated user and the specified user. |

### Messages

| Method | URL | Action | Description |
|--------|-----|--------|-------------|
| `GET` | `/messages` | `messages_search` | List messages for a given conversation. |
| `POST` | `/messages` | `message_create` | Create a new message. |
| `POST` | `/messages/{id}/read-to` | `messages_read` | Mark all messages up to the specified message ID as read. |
| `POST` | `/messages/{id}/pin` | `message_pin` | Pin a message to its conversation. |
| `POST` | `/messages/{id}/unpin` | `message_unpin` | Unpin a message from its conversation. |

## Broadcast Events

The package uses Laravel's broadcasting system to notify conversation members in real time. All events are delivered over private channels named `chat.{userId}`, where `{userId}` is the ID of the recipient.

### Channel Authorization

A user may only listen on their own private channel `chat.{userId}`. The channel is authorized when the authenticated user's ID matches `{userId}`.

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