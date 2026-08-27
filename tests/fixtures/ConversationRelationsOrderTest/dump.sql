INSERT INTO users(id, email, first_name, last_name, avatar_id) VALUES
    (1, 'anisio.tier@example.com', 'Alice', 'Doe', null),
    (2, 'fidel.kutch@example.com', 'Bob', 'Lewis', null),
    (3, 'alien.west@example.com', 'Charlie', null, null);

INSERT INTO conversations(id, creator_id, type, title, cover_id, last_updated_at, created_at, updated_at) VALUES
    (1, 1, 'group', 'Group Chat', null, '2016-10-20 11:05:00', '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO conversation_member(id, conversation_id, member_id) VALUES
    (30, 1, 1),
    (10, 1, 2),
    (20, 1, 3);

INSERT INTO messages(id, sender_id, conversation_id, text, attachment_id, updated_at, created_at) VALUES
    (3, 1, 1, 'three', null, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (9, 2, 1, 'nine', null, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (5, 3, 1, 'five', null, '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO read_messages(id, message_id, member_id, created_at, updated_at) VALUES
    (7, 3, 3, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (2, 3, 1, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
    (4, 3, 2, '2016-10-20 11:05:00', '2016-10-20 11:05:00');
