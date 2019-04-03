<?php
    /**
     * Функция возращает название класса для формы на основе переданного массива с результатами валидации
     * @param $errors
     * @param string $status
     * @return string
     */
    function get_form_validation_classname (&$errors, $status = '') {
        return isset($errors) && array_reduce($errors, function ($total, $item) {
            $total += is_array($item) ? count($item) : 0;
            return $total;
        }) > 0 || !empty($status) ? 'form--invalid' : '';
    }

    /**
     * Функция возращает текст сообщения для формы на основе переданного массива с результатами валидации
     * @param $errors
     * @return string
     */
    function get_form_validation_message (&$errors) {
        return isset($errors) && array_reduce($errors, function ($total, $item) {
            $total += is_array($item) ? count($item) : 0;
            return $total;
        }) > 0 ? 'Пожалуйста, исправьте ошибки в форме' : '';
    }

    /**
     * Функция возращает название класса для обертки поля формы на основе переданного массива с результатами валидации и названия поля
     * Для изображения передается название класса.
     * @param $errors
     * @param $field_name
     * @param string $success_classname
     * @return string
     */
    function get_field_validation_classname (&$errors, $field_name, $success_classname = '') {
        $success_classname = is_array($errors) && count($errors) === 0 ? '' : $success_classname;
        $field_errors = get_assoc_element($errors, $field_name);
        return is_array($field_errors) && count($field_errors) > 0 ? 'form__input--error' : $success_classname;
    }

    /**
     * Функция возвращает описание ошибок валадации для поля по массиву ошибок и названию поля (название поля - ключ в массиве с ошибками)
     * @param $errors
     * @param $field_name
     * @return string
     */
    function get_field_validation_message (&$errors, $field_name) {
        $field_errors = get_assoc_element($errors, $field_name);
        return is_array($field_errors) ? join('. ', $field_errors) : '';
    }

    /**
     * Функция возвращает результат валидации в виде ассоциативного массива с ключом 'Имя поля' по массиву с описанием полей формы
     * Сначала добавляются результаты проверок на required, затем - результаты дополнительных специфических
     * @param $fields
     * @param $form_data
     * @param $files
     * @return array
     */
    function get_validation_result ($fields, $form_data, &$files) {
        $errors = [];
        foreach ($fields as $field_name => $field) {
            $errors[$field_name] = [];
            $current_field = get_assoc_element($form_data, $field_name);
            if (get_assoc_element($field, 'required') &&
                empty($current_field) &&
                !in_array(FILE_RULE, get_assoc_element($field, 'validation_rules', true))) {
                add_error_message($errors, $field_name, 'Поле ' . get_assoc_element($field, 'description') . ' (' . $field_name . ') необходимо заполнить');
            }
            if (isset($field['validation_rules']) && is_array($field['validation_rules'])) {
                foreach ($field['validation_rules'] as $rule) {
                    $is_required = get_assoc_element($field, 'required');
                    $is_special = get_assoc_element($field, 'special');
                    $result = ($rule === FILE_RULE) ?
                        get_file_validation_result($field_name, $files, $is_required) :
                        get_additional_validation_result($rule, $current_field);
                    if (!empty($result) && ($is_required || $is_special)) {
                        add_error_message($errors, $field_name, $result);
                    }
                }
            }
        }
        return $errors;
    }

    /**
     * Функция возвращает результат проверки правильности выбора значения из списка категорий
     * @param $project_value
     * @return string
     */
    function get_project_validation_result ($project_value) {
        return empty($project_value) || ($project_value === EMPTY_PROJECT) ? 'Необходимо выбрать категорию!' : '';
    }

    /**
     * Функция проверяет, явяется ли параметр датой в формате ДД.ММ.ГГГГ больше текущей минимум на 1 день
     * @param $date
     * @return string
     */
    function get_task_date_validation_result ($date) {
        $error_message = 'Необходима дата в формате ДД.ММ.ГГГГ больше или равна текущей ';
        $now = date_create("now");
        $new_date = date_create_from_format('d.m.Y', $date);
        if (!$new_date || $date !== date_format($new_date, 'd.m.Y')) {
            $status = $error_message;
        } else {
            $status = ($new_date >= $now) ? '' : $error_message;
        }
        return $status;
    }

    /**
     * Функция-распределитель для вызова дополнительных проверок (правильности email и т.д)
     * @param $kind
     * @param $current_field
     * @return string
     */
    function get_additional_validation_result ($kind, $current_field) {
        switch ($kind) {
            case 'project_validation':
                return get_project_validation_result($current_field);
            case 'email_validation':
                return !filter_var($current_field, FILTER_VALIDATE_EMAIL) ? 'Email должен быть корректным' : '';
            case 'lot_date_validation':
                return get_task_date_validation_result($current_field);
            case 'auth_validation':
                return is_auth_user() ? '' : 'Необходимо авторизоваться';

            default:
                return '';
        }
    }

    /**
     * Функция проверяет, являются ли загружаемые файлы допустимого типа. Дальнейшие действия по загрузке и проверке вынесены в другую функцию
     * @param $field_name
     * @param $files
     * @param $is_required
     * @return string
     */
    function get_file_validation_result ($field_name, &$files, $is_required) {
        if (isset($files[$field_name]['name'])) {
            if (get_assoc_element($files[$field_name], 'error') !== 0) {
                return 'Файл не загружен';
            }
            $tmp_name = $files[$field_name]['tmp_name'];
            $file_size = $files[$field_name]['size'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $file_type = finfo_file($finfo, $tmp_name);
            $is_ok = in_array($file_type, VALID_FILE_TYPES) && ($file_size <= MAX_FILE_SIZE);
            return $is_ok ? '' : 'Загружаемый файл должен быть в формате jpeg, png, txt или pdf и размером не более 200Кб';
        }
        return $is_required ? 'Необходимо загрузить файл в формате jpeg, png, txt или pdf  (не более 200Кб)' : '';
    }

    /**
     * Фильтрует список описаний полей для последующей обработки загрузки изображений
     * @param $fields
     * @return array
     */
    function get_file_fields ($fields) {
        return array_filter($fields, function ($item) {
            return
                isset($item['validation_rules']) && is_array($item['validation_rules']) ?
                    in_array(FILE_RULE, $item['validation_rules'])
                    : false;
        });
    }

    /**
     * Функция пытается загрузить файл и записать путь к нему в данные формы, либо пополняет массив ошибок
     * @param $file_fields
     * @param $files
     * @param $errors
     * @param $file_path
     * @param $data
     */
    function try_upload_files ($file_fields, &$files, &$errors, $file_path, &$data) {
        foreach ($file_fields as $field_name => $field) {
            $tmp_name = $files[$field_name]['tmp_name'];
            if (!empty($tmp_name) && is_uploaded_file($tmp_name)) {
                $path = UI_START . uniqid('', true) . '_' .
                    pathinfo($files[$field_name]['name'], PATHINFO_FILENAME) . '.' . pathinfo($files[$field_name]['name'], PATHINFO_EXTENSION);
                if (check_and_repair_path($file_path)) {
                    move_uploaded_file($tmp_name, $file_path . $path);
                    $data[$field_name] = $path;
                } else {
                    add_error_message($errors, $field_name, 'Произошла ошибка при создании папки для загрузки файлов');
                }
            } else {
                $is_required = get_assoc_element($field, 'required');
                if ($is_required) {
                    add_error_message($errors, $field_name, 'Загрузка файла невозможна: ' . $files[$field_name]['tmp_name']);
                }
            }
        }
    }

    /**
     * Функция добавляет описание ошибки в предназначенный для этого массив
     * @param $errors
     * @param $field_name
     * @param $error_message
     */
    function add_error_message (&$errors, $field_name, $error_message) {
        if (isset($errors) && array_key_exists($field_name, $errors)) {
            array_push($errors[$field_name], $error_message);
        }
    }