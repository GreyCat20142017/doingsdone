<?php

    session_start();
    require_once('init.php');
    require_once('search.php');

    if (!is_auth_user()) {
        header('Location: guest.php');
    }

    $show_completed_tasks = 1;

    $projects = is_auth_user() ? get_user_projects($connection, get_auth_user_property('id')) : [];
    $tasks = is_auth_user() ? get_user_tasks($connection, get_auth_user_property('id'), $show_completed_tasks) : [];
    $search_string = '';

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

    $page_content = include_template('index.php',
        [
            'logo_content' => $logo_content,
            'user_content' => $user_content,
            'projects_content' => $projects_content,
            'show_completed' => $show_completed_tasks,
            'tasks' => $tasks,
            'projects' => $projects
        ]);

//    $search_content = include_template('search.php', ['search_string' => $search_string]);
    $search_content = '';

    $layout_content = include_template('layout.php',
        [
            'page_content' => $page_content,
            'search_content' => $search_content,
            'title' => 'Дела в порядке',
            'is_auth' => is_auth_user(),
            'user_name' => get_auth_user_property('name')
        ]);

    print($layout_content);


