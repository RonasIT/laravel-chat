INSERT INTO users(id, email) VALUES
    (1, 'anisio.tier@example.com'),
    (2, 'fidel.kutch@example.com'),
    (3, 'alien.west@example.com'),
    (4, 'alien.east@example.com'),
    (5, 'william.bob@example.com');

INSERT INTO media(id, name, owner_id, is_public, link, preview_id, meta, created_at, updated_at) VALUES
    (1, 'preview_Product main photo', 1, true, 'http://localhost/test_preview_1.jpg', null, '{}', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (2, 'preview_Category Photo photo', 1, false, 'http://localhost/test_preview_2.jpg', null, '{}', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO conversations(id, creator_id, type, title, cover_id, last_updated_at, created_at, updated_at) VALUES
    (1, null, 'private', null, null, '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (2, null, 'private', null, null, '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (3, null, 'private', null, null, '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (4, null, 'private', null, null, '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (5, null, 'private', null, null, '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO conversation_member(conversation_id, member_id) VALUES
    (1, 1),
    (1, 2),
    (2, 1),
    (2, 3),
    (3, 1),
    (3, 4),
    (4, 1),
    (4, 5),
    (5, 2),
    (5, 3);

INSERT INTO messages(id, sender_id, conversation_id, text, attachment_id, updated_at, created_at) VALUES
    (1, 1, 1, 'hi', 1, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (2, 1, 2, 'hi', null, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (3, 1, 3, 'hi', null, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (4, 1, 4, 'hi', null, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (5, 2, 5, 'hi', null, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (6, 3, 5, 'hi', null, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (7, 3, 5, 'hi', null, '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO read_messages(id, message_id, member_id, created_at, updated_at) VALUES
    (1, 2, 1, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (2, 2, 3, '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO pinned_messages(id, conversation_id, message_id, created_at, updated_at) VALUES
    (1, 1, 1, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (2, 5, 7, '2016-10-20 11:05:00', '2016-10-20 11:05:00');
