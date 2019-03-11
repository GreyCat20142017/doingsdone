<?php

    session_start();
    require_once('init.php');

    if (!is_auth_user()) {
        header('Location: guest.php');
    }

    $filter_string = FILTER_ALL;

    $show_completed_tasks = isset($_GET['show_completed']) ? intval(trim(strip_tags($_GET['show_completed']))) : 1;

    if (isset($_GET['task_id']) && isset($_GET['check'])) {
        update_task_status_by_id($connection, intval(trim(strip_tags($_GET['task_id']))));
        header('Location: index.php');
    }

    $projects = is_auth_user() ? get_user_projects($connection, get_auth_user_property('id')) : [];
    $search_string = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['find'])) {
            $search_string = $_POST['search'] ?? '';
            $search_string = trim(strip_tags($search_string));
        }

        $tasks = (is_auth_user() && !empty($search_string)) ?
            get_user_tasks($connection, get_auth_user_property('id'), $show_completed_tasks, $search_string) : [];
    } else {
        $filter_string = isset($_GET['condition']) ? trim(strip_tags($_GET['condition'])) : '';
        $tasks = is_auth_user() ?
            get_user_tasks($connection, get_auth_user_property('id'), $show_completed_tasks, '', $filter_string) : [];
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
            'condition_descriptions' => FILTER_TEXT,
            'path' => get_assoc_element(PATHS, 'files')
        ]);

    $layout_content = include_template('layout.php',
        [
            'page_content' => $page_content,
            'title' => 'Дела в порядке',
            'is_auth' => is_auth_user(),
            'user_name' => get_auth_user_property('name')
        ]);

    print($layout_content);


