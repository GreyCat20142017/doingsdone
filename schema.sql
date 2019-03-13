DROP DATABASE IF EXISTS ddone;

CREATE DATABASE ddone DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;

USE ddone;

CREATE TABLE users (
  id                INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  registration_date TIMESTAMP    NOT NULL        DEFAULT CURRENT_TIMESTAMP,
  email             VARCHAR(128) NOT NULL UNIQUE DEFAULT '',
  name              CHAR(30)     NOT NULL        DEFAULT '',
  user_password     VARCHAR(254) NOT NULL        DEFAULT ''
);

CREATE TABLE projects (
  id      INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL DEFAULT 0,
  name    VARCHAR(50)
);

CREATE TABLE tasks (
  id              INT UNSIGNED     NOT NULL AUTO_INCREMENT PRIMARY KEY,
  project_id      INT UNSIGNED     NOT NULL DEFAULT 0,
  user_id         INT UNSIGNED     NOT NULL DEFAULT 0,
  creation_date   TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  completion_date TIMESTAMP,
  status          TINYINT UNSIGNED NOT NULL DEFAULT 0,
  name            CHAR(64)         NOT NULL DEFAULT '',
  file            CHAR(50)         NOT NULL DEFAULT '',
  expiration_date TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX status_expiration ON tasks (status, expiration_date);

CREATE FULLTEXT INDEX tasks_ft_search ON tasks (name);


ALTER TABLE tasks
  ADD CONSTRAINT fk_project_tasks FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE;
ALTER TABLE tasks
  ADD CONSTRAINT fk_user_tasks FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE;
ALTER TABLE projects
  ADD CONSTRAINT fk_user_projects FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE;