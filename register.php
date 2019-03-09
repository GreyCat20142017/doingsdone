<?php

    session_start();
    require_once('init.php');

    $errors = [];
    $user = [];

    $status_text = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $user = array_map(function ($item) {
            return trim(strip_tags($item));
        }, $_POST);
        /**
         * Описания полей для валидации. Если правила слишком специфичны, то в required для обязательных полей
         * нужно установить false, при этом заполнение контролировать специфическими правилами
         */
        $fields = [
            'email' => ['description' => 'E-mail', 'required' => true, 'validation_rules' => ['email_validation']],
            'password' => ['description' => 'Пароль', 'required' => true],
            'name' => ['description' => 'Имя', 'required' => true],
        ];

        $errors = get_validation_result($fields, $user, $_FILES);

        $status_ok = empty(get_form_validation_classname($errors));


        if ($status_ok) {

            $add_result = add_user($connection, $user);

            if ($add_result) {
                if (isset($add_result['id'])) {
                    add_error_message($errors, 'email', 'Пользователь с таким email уже существует!');
                } else {
                    header('Location: index.php');
                }
            } else {

                $status_text = 'Не удалось добавить пользователя в БД';
            }
        }
    }

    $logo_content = include_template('logo.php', []);

    $page_content = include_template('register.php', [
        'logo_content' => $logo_content,
        'errors' => $errors,
        'user' => $user,
        'status' => $status_text
    ]);


    $layout_content = include_template('layout.php',
        [
            'page_content' => $page_content,
            'title' => 'Регистрация',
            'is_auth' => is_auth_user(),
            'user_name' => get_auth_user_property('name')
        ]);

    print($layout_content);