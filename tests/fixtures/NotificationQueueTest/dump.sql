INSERT INTO users(id, email, first_name, last_name, avatar_id) VALUES
    (1, 'anisio.tier@example.com', 'Anisio', 'Tier', null),
    (2, 'fidel.kutch@example.com', 'Fidel', 'Kutch', null);

INSERT INTO conversations(id, creator_id, type, title, cover_id, last_updated_at, created_at, updated_at) VALUES
    (1, null, 'private', null, null, '2016-10-20 14:00:00', '2016-10-20 11:05:00', '2016-10-20 14:00:00');

INSERT INTO conversation_member(conversation_id, member_id) VALUES
    (1, 1),
    (1, 2);

INSERT INTO messages(id, sender_id, conversation_id, text, attachment_id, updated_at, created_at) VALUES
    (1, 2, 1, 'hi', null, '2016-10-20 14:00:00', '2016-10-20 14:00:00');
