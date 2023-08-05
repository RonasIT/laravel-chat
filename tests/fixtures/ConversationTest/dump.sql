INSERT INTO media(id, link, name, is_public, mime) VALUES
(1, 'https://some_url.com/html/1', 'some_name_1.jpg', true, 'image/jpg'),
(2, 'https://some_url.com/html/2', 'some_name_2.jpg', false, 'image/jpg'),
(3, 'https://some_url.com/html/3', 'some_1.jpg', true, 'image/jpg'),
(4, 'https://some_url.com/html/4', 'name_2.jpg', false, 'image/jpg');

INSERT INTO users(id, avatar_id, email, password, role_id, created_at, updated_at) VALUES
  (1, 1, 'user@example.com', '1', 2, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 2, 'second.user@example.com', '2', 2, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (3, 3, 'third.user@example.com', '2', 2, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (4, 4, 'fought.user@example.com', '2', 2, '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO conversations(id, sender_id, recipient_id, created_at, updated_at) VALUES
  (1, 1, 2, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 3, 4, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (3, 3, 1, '2016-10-20 11:05:00', '2016-10-20 11:05:00');

INSERT INTO messages(id, conversation_id, sender_id, recipient_id, text, is_read, created_at, updated_at) VALUES
  (1, 1, 1, 2, 'New message', false, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (2, 2, 3, 4, 'Some message', false, '2016-10-20 11:05:00', '2016-10-20 11:05:00'),
  (3, 3, 3, 1, 'Text', true, '2018-10-20 11:05:00', '2016-10-20 11:05:00'),
  (4, 1, 1, 2, 'Old', false, '2015-10-20 11:05:00', '2016-10-20 11:05:00'),
  (5, 3, 1, 3, 'Test', true, '2014-10-20 11:05:00', '2016-10-20 11:05:00'),
  (6, 3, 3, 1, 'All you need to do is follow damn train CJ', false, '2015-10-20 11:05:00', '2016-10-20 11:05:00'),
  (7, 3, 3, 1, 'Awesome!', false, '2017-10-20 11:05:00', '2016-10-20 11:05:00');
