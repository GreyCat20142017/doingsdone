<?php

    session_start();
    require_once('init.php');

    $logo_content = include_template('logo.php', []);

    $page_content = include_template('guest.php', [
        'logo_content' => $logo_content,
    ]);

    $layout_content = include_template('layout.php',
        [
            'page_content' => $page_content,
            'title' => '«Дела в порядке»',
            'is_auth' => is_auth_user(),
            'user_name' => get_auth_user_property('name')
        ]);

    print($layout_content);