# Migration guide

## 0.5

### BaseNotification

#### getBroadcastData

Notifications no longer build the `BroadcastMessage` themselves. `toBroadcast` moved to
`BaseNotification`, where it wraps the payload into a `BroadcastMessage` and applies the queue from
`chat.broadcast_queue`. The payload is now returned by the new abstract `getBroadcastData` method.

Move the body of your `toBroadcast` into `getBroadcastData` and return the payload array instead of a
`BroadcastMessage`.

Before:

```php
public function toBroadcast(): BroadcastMessage
{
    return new BroadcastMessage([
        'data' => $payload,
    ]);
}
```

After:

```php
public function getBroadcastData(): array
{
    return [
        'data' => $payload,
    ];
}
```

`getBroadcastData` is abstract, so a notification that still only defines `toBroadcast` fails to
load. Overriding `toBroadcast` is still allowed, but then applying `chat.broadcast_queue` to the
message is up to you.
