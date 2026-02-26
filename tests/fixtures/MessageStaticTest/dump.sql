INSERT INTO users(id, email) VALUES
    (1, 'anisio.tier@example.com'),
    (2, 'fidel.kutch@example.com'),
    (3, 'alien.west@example.com'),
    (4, 'alien.east@example.com'),
    (5, 'william.bob@example.com');

INSERT INTO media(id, name, owner_id, is_public, link, preview_id, meta, created_at, updated_at) VALUES
    (1, 'preview_Product main photo', 1 , true, 'http://localhost/test_preview_1.jpg', null, '{}', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (2, 'preview_Category Photo photo', 1, false, 'http://localhost/test_preview_2.jpg', null, '{}', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO conversations(id, type, last_updated_at, created_at, updated_at) VALUES
    (1, 'private', '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (2, 'private', '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (3, 'private', '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (4, 'private', '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (5, 'private', '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO conversation_member(id, conversation_id, member_id, last_read_message_id) VALUES
    (1,1, 1, null),
    (2,1, 2, null),
    (3,2, 1, null),
    (4,2, 2, null);

INSERT INTO messages(id, sender_id, conversation_id, text, updated_at, created_at, attachment_id) VALUES
    (1, 1, 1, 'hi', '2016-10-20 11:05:00', '2016-10-20 11:05:00', 1),
    (2, 1, 1, 'hi', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null),
    (3, 2, 1, 'hi', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null),
    (4, 1, 2, 'hi', '2016-10-20 11:05:00', '2016-10-20 11:05:00', null);

UPDATE conversation_member SET last_read_message_id = 1 WHERE id = 2