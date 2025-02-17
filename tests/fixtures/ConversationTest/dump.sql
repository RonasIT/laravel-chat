INSERT INTO users(id, email) VALUES
    (1, 'anisio.tier@example.com'),
    (2, 'fidel.kutch@example.com'),
    (3, 'alien.west@example.com');

INSERT INTO conversations(id, sender_id, recipient_id, last_updated_at, created_at, updated_at) VALUES
    (1, 1, 2, '2016-10-20 11:05:00', '2016-10-20 11:05:00' , '2016-10-20 11:05:00'),
    (2, 1, 2, '2016-10-20 11:05:00', '2016-10-20 11:05:00' , '2016-10-20 11:05:00'),
    (3, 1, 2, '2016-10-20 11:05:00', '2016-10-20 11:05:00' , '2016-10-20 11:05:00'),
    (4, 1, 2, '2016-10-20 11:05:00', '2016-10-20 11:05:00' , '2016-10-20 11:05:00'),
    (5, 1, 2, '2016-10-20 11:05:00', '2016-10-20 11:05:00' , '2016-10-20 11:05:00');

INSERT INTO messages(id, sender_id, recipient_id, conversation_id, text, is_read, updated_at, created_at) VALUES
    (1, 1, 2, 1, 'hi', true, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (2, 2, 1, 1, 'hi', false, '2016-10-20 11:05:00', '2016-10-20 11:05:00');