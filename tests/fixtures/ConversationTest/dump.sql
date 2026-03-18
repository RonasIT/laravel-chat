INSERT INTO users(id, email, name, avatar_id) VALUES
    (1, 'anisio.tier@example.com', 'Alice', null),
    (2, 'fidel.kutch@example.com', 'Bob', 2),
    (3, 'alien.west@example.com', 'Charlie', null);

INSERT INTO media(id, name, owner_id, is_public, link, preview_id, meta, created_at, updated_at) VALUES
    (1, 'group_chat_cover', 1, true, 'http://localhost/group_chat_cover.jpg', null, '{}', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (2, 'bob_avatar', 2, true, 'http://localhost/bob_avatar.jpg', null, '{}', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO conversations(id, creator_id, type, title, cover_id, last_updated_at, created_at, updated_at) VALUES
    (1, null, 'private', null, null, '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (2, null, 'private', null, null, '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (3, null, 'private', null, null, '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (4, null, 'private', null, null, '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (5, null, 'private', null, null, '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (6, 1, 'group', 'Group Chat', 1, '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (7, null, 'private', null, null, '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO conversation_member(conversation_id, member_id) VALUES
    (1, 1),
    (1, 2),
    (2, 1),
    (2, 2),
    (3, 1),
    (3, 2),
    (4, 1),
    (4, 2),
    (5, 1),
    (5, 2),
    (6, 1),
    (6, 2),
    (6, 3),
    (7, 2),
    (7, 3);

INSERT INTO messages(id, sender_id, conversation_id, text, attachment_id, updated_at, created_at) VALUES
    (1, 1, 1, 'hi', null, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (2, 2, 1, 'hi', null, '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO pinned_messages(id, conversation_id, message_id, created_at, updated_at) VALUES
    (1, 1, 1, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (2, 1, 2, '2016-10-20 11:05:00', '2016-10-20 11:05:00');
