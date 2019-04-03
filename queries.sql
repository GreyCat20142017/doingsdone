USE ddone;

# Чистка таблиц
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE tasks;
TRUNCATE TABLE projects;
TRUNCATE TABLE users;
SET FOREIGN_KEY_CHECKS = 1;

# Добавление пары пользователей
INSERT INTO users (name, email, user_password, registration_date)
VALUES ('Василий Пупкин', 'vasya@mail.ru', '$2y$10$0GYFabnO4kWUhOhvSaOQGOsT3zHGyQBsSuRcgbtlUIV19u84TEEgW',
        DATE_ADD(NOW(), INTERVAL -2 MONTH)),
       ('Василиса Пупкина', 'vasilisaPupkina@mail.ru', '$2y$10$0GYFabnO4kWUhOhvSaOQGOsT3zHGyQBsSuRcgbtlUIV19u84TEEgW',
        DATE_ADD(NOW(), INTERVAL -3 WEEK)),
       ('Кукушкин К.К', 'qq@qq.qq', '$2y$10$0pkXRScT4SJY7zUNzExa9uamBERCt4dvqezPNS69MNVsjpjBI7Un.',
        DATE_ADD(NOW(), INTERVAL -1 MONTH));


INSERT INTO projects (user_id, name)
VALUES (1, 'Входящие'),
       (1, 'Учеба'),
       (1, 'Работа'),
       (1, 'Домашние дела'),
       (1, 'Авто');

INSERT INTO tasks (user_id, name, completion_date, project_id, status, expiration_date, file)
VALUES (1, 'Собеседование в IT компании', '2019-12-01', 3, 0, DATE_ADD(NOW(), INTERVAL 3 WEEK), 'Резюме.pdf'),
       (1, 'Выполнить тестовое задание', '2019-12-25', 3, 0, DATE_ADD(NOW(), INTERVAL 2 WEEK), 'Архив.zip'),
       (1, 'Сделать задание первого раздела', '2019-12-21', 2, 1, DATE_ADD(NOW(), INTERVAL 1 WEEK),
        'Tекст задания.pdf'),
       (1, 'Встреча с другом', '2019-12-22', 1, 0, DATE_ADD(NOW(), INTERVAL 5 DAY), ''),
       (1, 'Купить корм для кота', NULL, 4, 0, DATE_ADD(NOW(), INTERVAL 1 WEEK), ''),
       (1, 'Заказать пиццу', NULL, 4, 0, DATE_ADD(NOW(), INTERVAL 5 HOUR), '');

SET @userid = 1;
SET @projectid = 3;
SET @taskid = 4;

# получить список из всех проектов для одного пользователя;
SELECT id, name
FROM projects
WHERE user_id = @userid;

# получить список из всех задач для одного проекта;
SELECT t.id, p.name AS project, t.name, t.status, t.expiration_date
FROM tasks AS t
       JOIN projects AS p ON t.project_id = p.id
WHERE t.project_id = @projectid;

# пометить задачу как выполненную;
UPDATE tasks
SET status = 1
WHERE id = @taskid;

# обновить название задачи по её идентификатору.
UPDATE tasks
SET name = 'Встреча с подругой'
WHERE id = @taskid;


