<?php

    session_start();
    require_once('init.php');

    $errors = [];
    $user = [];
    $db_user = [];
    $status_text = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $user = array_map(function ($item) {
            return trim(strip_tags($item));
        }, $_POST);

        $status_text = 'Вход на сайт невозможен';
        /**
         * Описания полей для валидации. Если правила слишком специфичны, то в required для обязательных полей
         * нужно установить false, при этом заполнение контролировать специфическими правилами
         */
        $fields = [
            'email' => ['description' => 'E-mail', 'required' => true, 'validation_rules' => ['email_validation']],
            'password' => ['description' => 'Пароль', 'required' => true]
        ];

        $errors = get_validation_result($fields, $user, $_FILES);

        $status_ok = empty(get_form_validation_classname($errors));

        if ($status_ok) {

            $db_status_ok = false;

            $search_result = get_user_by_email($connection, get_assoc_element($user, 'email'));

            switch (get_assoc_element($search_result, 'status')) {

                case get_assoc_element(GET_DATA_STATUS, 'data_received'):
                    $db_status_ok = true;
                    $db_user = get_assoc_element($search_result, 'data');
                    if (!password_verify(get_assoc_element($user, 'password'), get_assoc_element($db_user, 'user_password'))) {
                        $db_status_ok = false;
                        add_error_message($errors, 'password', 'Вы ввели неверный пароль');
                    }
                    break;

                case get_assoc_element(GET_DATA_STATUS, 'no_data'):
                    add_error_message($errors, 'email', 'Пользователь с таким email не зарегистрирован на сайте');
                    break;

                case get_assoc_element(GET_DATA_STATUS, 'db_error'):
                    add_error_message($errors, 'email', 'Произошла ошибка при получении данных пользователей');
                    break;

                default:
                    break;
            }

            if ($db_status_ok) {
                $status_text = '';

                $_SESSION[DDONE_SESSION] = [
                    'id' => get_assoc_element($db_user, 'id'),
                    'name' => get_assoc_element($db_user, 'name')
                ];;

                header('Location: index.php');
            }
        }
    }

    $logo_content = include_template('logo.php', []);

    $page_content = include_template('form-authorization.php', [
        'logo_content' => $logo_content,
        'errors' => $errors,
        'user' => $user,
        'status' => $status_text
    ]);

    $layout_content = include_template('layout.php',
        [
            'page_content' => $page_content,
            'title' => 'Вход на сайт',
            'is_auth' => is_auth_user(),
            'user_name' => get_auth_user_property('name')
        ]);

    print($layout_content);