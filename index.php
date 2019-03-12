<?php

    session_start();
    require_once('init.php');

    $logo_content = include_template('logo.php', []);

    if (is_auth_user()) {

        $filter_string = FILTER_ALL;

        $show_completed_tasks = isset($_GET['show_completed']) ? intval(trim(strip_tags($_GET['show_completed']))) : 1;

        if (isset($_GET['task_id']) && isset($_GET['check'])) {
            update_task_status_by_id($connection, intval(trim(strip_tags($_GET['task_id']))));
            header('Location: index.php');
        }

        $projects = is_auth_user() ? get_user_projects($connection, get_auth_user_property('id')) : [];
        $project_id = isset($_GET['project_id']) ? intval(strip_tags($_GET['project_id'])) : null;
        $project_existance = get_id_existance($connection, 'projects', $project_id);
        $is_ok = ($project_id) && was_error($project_existance) ? false : true;
        $search_string = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['find'])) {

            $search_string = isset($_POST['search']) ? trim(strip_tags($_POST['search'])) : '';
            $tasks = (is_auth_user() && !empty($search_string)) ?
                get_user_tasks($connection, get_auth_user_property('id'), $show_completed_tasks, $project_id, $search_string) : [];

        } else {

            $filter_string = isset($_GET['condition']) ? trim(strip_tags($_GET['condition'])) : '';
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
                'current_filter' => $filter_string

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

    } else {

        $page_content = include_template('guest.php', [
            'logo_content' => $logo_content,
        ]);
    }

    if (!$is_ok) {
        http_response_code(404);
        $page_content = include_template('404.php', [
            'logo_content' => $logo_content,
            'error_message' =>  '404. Проект с id= ' . $project_id . ' не найден!'
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


