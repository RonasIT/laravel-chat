INSERT INTO users(id, email, first_name, last_name, avatar_id) VALUES
    (1, 'anisio.tier@example.com', 'Alice', 'Doe', null),
    (2, 'fidel.kutch@example.com', 'Bob', 'Lewis', null),
    (3, 'alien.west@example.com', 'Charlie', null, null),
    (4, 'diana.prince@example.com', 'Diana', 'Prince', null);

INSERT INTO conversations(id, creator_id, type, title, cover_id, last_updated_at, created_at, updated_at) VALUES
    (1, null, 'private', null, null, '2016-10-20 14:00:00', '2016-10-20 11:05:00', '2016-10-20 14:00:00'),
    (2, null, 'private', null, null, '2016-10-20 13:00:00', '2016-10-20 11:05:00', '2016-10-20 13:00:00'),
    (6, 1, 'group', 'Group Chat', null, '2016-10-20 09:00:00', '2016-10-20 11:05:00', '2016-10-20 09:00:00'),
    (7, 1, 'group', 'Second Group Chat', null, '2016-10-20 08:00:00', '2016-10-20 11:05:00', '2016-10-20 08:00:00'),
    (8, 2, 'group', 'Other Creator Chat', null, '2016-10-20 07:00:00', '2016-10-20 11:05:00', '2016-10-20 07:00:00'),
    (9, 4, 'group', 'Fourth Creator Chat', null, '2016-10-20 06:00:00', '2016-10-20 11:05:00', '2016-10-20 06:00:00');

INSERT INTO conversation_member(conversation_id, member_id) VALUES
    (1, 1),
    (1, 2),
    (2, 1),
    (2, 2),
    (6, 1),
    (6, 2),
    (6, 3),
    (7, 1),
    (7, 2),
    (8, 2),
    (8, 3),
    (9, 3),
    (9, 4);
