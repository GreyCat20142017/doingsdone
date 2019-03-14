<?php

    session_start();
    require_once('init.php');

    $is_ok = true;
    $logo_content = include_template('logo.php', []);

    if (is_auth_user()) {

        $filter_string = get_auth_user_property('current_filter', DEFAULT_FILTER);
        $show_completed_tasks = intval(get_auth_user_property('current_show_completed', DEFAULT_SHOW_COMPLETED));
        $project_id = intval(get_auth_user_property('current_project', DEFAULT_PROJECT));
        $search_string = '';

        if (isset($_GET['task_id']) && isset($_GET['check'])) {
            update_task_status_by_id($connection, intval(trim(strip_tags($_GET['task_id']))));
            header('Location: index.php');
        }

        if (isset($_GET['project_id'])) {
            $project_id = intval(strip_tags($_GET['project_id']));
        }

        if (isset($_GET['condition'])) {
            $filter_string = trim(strip_tags($_GET['condition']));
        }

        if (isset($_GET['show_completed'])) {
            $show_completed_tasks = intval(trim(strip_tags($_GET['show_completed'])));
        }

        $is_ok = ($project_id) ? get_project_existance($connection, intval(get_auth_user_property('id')), $project_id) : true;

        $projects = $is_ok ? get_user_projects($connection, get_auth_user_property('id')) : [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['find'])) {

            $search_string = isset($_POST['search']) ? trim(strip_tags($_POST['search'])) : '';

            /**
             * Если не сбрасывать значения текущих фильтра, проекта и т.д.
             */
            // $tasks = (is_auth_user() && !empty($search_string)) ? get_user_tasks($connection, get_auth_user_property('id'), $show_completed_tasks, $project_id, $search_string) : [];*/

            $filter_string = DEFAULT_FILTER;
            $show_completed_tasks = DEFAULT_SHOW_COMPLETED;
            $project_id = DEFAULT_PROJECT;
            $tasks = (is_auth_user() && !empty($search_string)) ? get_user_tasks($connection, get_auth_user_property('id'), $show_completed_tasks, $project_id, $search_string) : [];

        } else {

            $tasks = is_auth_user() ?
                get_user_tasks($connection, get_auth_user_property('id'), $show_completed_tasks, $project_id, '', $filter_string) : [];

        }

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

        $search_content = include_template('search.php', ['search_string' => $search_string]);

        $page_content = include_template('index.php',
            [
                'logo_content' => $logo_content,
                'user_content' => $user_content,
                'projects_content' => $projects_content,
                'search_content' => $search_content,
                'show_completed' => $show_completed_tasks,
                'tasks' => $tasks,
                'projects' => $projects,
                'current_filter' => $filter_string,
                'current_project' => $project_id,
                'condition_descriptions' => FILTER_TEXT,
                'path' => get_assoc_element(PATHS, 'files')
            ]);

        if (!$is_ok) {

            http_response_code(404);
            $page_content = include_template('404.php', [
                'logo_content' => $logo_content,
                'error_message' => 'Ошибка 404. Проект с id= ' . $project_id . ' текущего пользователя не найден!'
            ]);

        } else {

            $_SESSION[DDONE_SESSION]['current_project'] = $project_id;
            $_SESSION[DDONE_SESSION]['current_filter'] = $filter_string;
            $_SESSION[DDONE_SESSION]['current_show_completed'] = $show_completed_tasks;

        }

    } else {

        $page_content = include_template('guest.php', [
            'logo_content' => $logo_content,
        ]);
    }

    $layout_content = include_template('layout.php',
        [
            'page_content' => $page_content,
            'title' => 'Дела в порядке',
            'is_auth' => is_auth_user(),
            'user_name' => get_auth_user_property('name')
        ]);

    print($layout_content);


