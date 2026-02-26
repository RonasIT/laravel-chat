INSERT INTO users(id, email) VALUES
    (1, 'anisio.tier@example.com'),
    (2, 'fidel.kutch@example.com'),
    (3, 'alien.west@example.com'),
    (4, 'fourth.user@example.com');

INSERT INTO media(id, link, name, is_public, owner_id, preview_id) VALUES
   (1, 'test link', 'test name', true, 1, null),
   (2, 'test link-2', 'test name-2', true, 1, 1),
   (3, 'test link-3', 'test name-3', true, 1, null),
   (4, 'test link-4', 'test name-4', true, 1, 2);

INSERT INTO conversations(id, creator_id, type, last_updated_at, created_at, updated_at, cover_id) VALUES
    (1, null, 'private', '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null),
    (2, null, 'private', '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null),
    (3, null, 'private', '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null),
    (4, null, 'private', '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null),
    (5, null, 'private', '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null),
    (6, 1, 'group', '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00', 4),
    (7, 2, 'group', '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null),
    (8, 3, 'group', '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null);

INSERT INTO conversation_member(id, conversation_id, member_id, last_read_message_id) VALUES
    (1, 1, 1, null),
    (2, 1, 2, null),
    (3, 6, 1, null),
    (4, 6, 2, null),
    (5, 8, 1, null);

INSERT INTO messages(id, sender_id, conversation_id, text, updated_at, created_at) VALUES
    (1, 1, 1, 'hi', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (2, 2, 1, 'hi', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (6, 1, 6, 'hi', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (7, 2, 6, 'hi', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (8, 2, 6, 'hi', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

UPDATE conversation_member SET last_read_message_id  = 7 WHERE id = 3;