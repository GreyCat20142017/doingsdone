<?php

    session_start();
    require_once('init.php');

    if (!is_auth_user()) {
        http_response_code(403);
        exit();
    }

    $errors = [];
    $task = [];

    $projects = is_auth_user() ? get_user_projects($connection, get_auth_user_property('id')) : [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['date'])) {
            $_POST['date'] = empty(trim($_POST['date'])) ? '' : '' . date('d.m.Y', strtotime(strip_tags($_POST['date'])));
        }
        $task = array_map(function ($item) {
            return trim(strip_tags($item));
        }, $_POST);
        /**
         * Описания полей для валидации. Если правила слишком специфичны, то в required для обязательных полей
         * нужно установить false, при этом заполнение контролировать специфическими правилами
         */
        $fields = [
            'project' => ['description' => 'Проект', 'required' => false, 'validation_rules' => ['project_validation'], 'special' => true],
            'name' => ['description' => 'Наименование', 'required' => true],
            'date' => ['description' => 'Дата выполнения', 'required' => true, 'validation_rules' => ['project_date_validation']],
            'preview' => ['description' => 'Файл', 'required' => true, 'validation_rules' => [FILE_RULE]]
        ];
        $errors = get_validation_result($fields, $task, $_FILES);
        $status_ok = empty(get_form_validation_classname($errors)) && is_auth_user();
        $file_fields = get_file_fields($fields);
        if ($status_ok) {
            try_upload_files($file_fields, $_FILES, $errors, get_assoc_element(PATHS, 'files'), 'file', $task);
            $add_result = false ;//add_lot($connection, $lot, get_auth_user_property('id'));
            if (isset($add_result) && array_key_exists('id', $add_result)) {
                header('Location: index.php');
            }
        }
        /**
         * Если были ошибки, изображения нужно загрузить снова в любом случае
         */
        $_FILES = [];
        foreach ($file_fields as $key_file_field => $image_field) {
            $description = get_assoc_element($fields, $key_file_field);
            set_assoc_element($description, 'errors', []);
        }
    }

    $logo_content = include_template('logo.php', []);

    $user_content = include_template('logged-user.php',
        [
            'is_auth' => is_auth_user(),
            'user_name' => get_auth_user_property('name')
        ]);

    $projects_content = include_template('projects.php',
        [
            'projects' => $projects
        ]);

    $projects_dropdown = include_template('projects_dropdown.php',
        [
            'projects' => $projects,
            'empty_project' => EMPTY_PROJECT,
            'errors' => $errors,
            'current' => get_assoc_element($task, 'project')
        ]);

    $page_content = include_template('form-task.php',
        [
            'logo_content' => $logo_content,
            'user_content' => $user_content,
            'projects_content' => $projects_content,
            'projects_dropdown' => $projects_dropdown,
            'errors' => $errors,
            'task' => $task
        ]);

    $layout_content = include_template('layout.php',
        [
            'page_content' => $page_content,
            'title' => 'Проекты'
        ]);
    print($layout_content);