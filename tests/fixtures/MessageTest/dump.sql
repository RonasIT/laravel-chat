INSERT INTO users(id, email) VALUES
    (1, 'anisio.tier@example.com'),
    (2, 'fidel.kutch@example.com'),
    (3, 'alien.west@example.com'),
    (4, 'alien.east@example.com'),
    (5, 'william.bob@example.com');

INSERT INTO media(id, name, owner_id, is_public, link, preview_id, meta, created_at, updated_at) VALUES
    (1, 'preview_Product main photo', 1 , true, 'http://localhost/test_preview_1.jpg', null, '{}', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO conversations(id, sender_id, recipient_id, last_updated_at, created_at, updated_at) VALUES
    (1, 1, 2, '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (2, 1, 3, '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (3, 1, 4, '2016-10-20 11:05:00', '2016-10-20 11:05:00',  '2016-10-20 11:05:00'),
    (4, 1, 5, '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (5, 2, 3, '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO messages(id, sender_id, recipient_id, conversation_id, text, is_read, updated_at, created_at, attachment_id) VALUES
    (1, 1, 2, 1, 'hi', true, '2016-10-20 11:05:00', '2016-10-20 11:05:00', 1),
    (2, 1, 3, 2, 'hi', true, '2016-10-20 11:05:00', '2016-10-20 11:05:00', null),
    (3, 1, 4, 3, 'hi', false, '2016-10-20 11:05:00', '2016-10-20 11:05:00', null),
    (4, 1, 5, 4, 'hi', false, '2016-10-20 11:05:00', '2016-10-20 11:05:00', null),
    (5, 2, 3, 5, 'hi', false, '2016-10-20 11:05:00', '2016-10-20 11:05:00', null);
