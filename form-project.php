<?php

    session_start();
    require_once('init.php');

    if (!is_auth_user()) {
        http_response_code(403);
        exit();
    }

    $filter_string = get_auth_user_property('current_filter', DEFAULT_FILTER);
    $show_completed_tasks = intval(get_auth_user_property('current_show_completed', DEFAULT_SHOW_COMPLETED));
    $project_id = intval(get_auth_user_property('current_project', DEFAULT_PROJECT));

    $errors = [];
    $project = [];

    $projects = is_auth_user() ? get_user_projects($connection, get_auth_user_property('id')) : [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $project = array_map(function ($item) {
            return trim(strip_tags($item));
        }, $_POST);
        /**
         * Описания полей для валидации. Если правила слишком специфичны, то в required для обязательных полей
         * нужно установить false, при этом заполнение контролировать специфическими правилами
         */
        $fields = [
            'name' => ['description' => 'Наименование', 'required' => true]
        ];

        $errors = get_validation_result($fields, $project, $_FILES);
        $status_ok = empty(get_form_validation_classname($errors)) && is_auth_user();

        if ($status_ok) {

            $add_status = add_project($connection, get_auth_user_property('id'), get_pure_data($project, 'name'), $errors);

            if ($add_status) {
                header('Location:/index.php');
            }
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
            'projects' => $projects,
            'show_completed' => $show_completed_tasks,
            'current_filter' => $filter_string,
            'current_project' => $project_id
        ]);

    $page_content = include_template('form-project.php',
        [
            'logo_content' => $logo_content,
            'user_content' => $user_content,
            'projects_content' => $projects_content,
            'errors' => $errors,
            'project' => $project
        ]);

    $layout_content = include_template('layout.php',
        [
            'page_content' => $page_content,
            'title' => 'Проекты'
        ]);

    print($layout_content);