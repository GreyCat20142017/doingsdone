<?php

    require_once('constants.php');
    require_once('connection.php');
    require_once('mysql_helper.php');
    require_once('db_functions.php');
    require_once('validation_functions.php');
    require_once('session_functions.php');

    /**
     * Функция принимает два аргумента: имя файла шаблона и ассоциативный массив с данными для этого шаблона.
     * Функция возвращает строку — итоговый HTML-код с подставленными данными или описание ошибки
     * @param $name string
     * @param $data array
     * @return false|string
     */
    function include_template ($name, $data) {
        $name = 'templates/' . $name;
        if (!is_readable($name)) {
            return 'Шаблон с именем ' . $name . ' не существует или недоступен для чтения';
        }
        ob_start();
        extract($data);
        require $name;
        return ob_get_clean();
    }

    /**
     * Функция проверяет существование ключа ассоциативного массива и возвращает значение по ключу, если
     * существуют ключ и значение. В противном случае будет возвращена пустая строка или пустой массив (если передан
     * третий параметр, запрашивающий пустой массив в случае отсутствия значения)
     * @param array $data
     * @param string $key
     * @param bool $array_return
     * @return array|string
     */
    function get_assoc_element ($data, $key, $array_return = false) {
        $empty_value = $array_return ? [] : '';
        return isset($data) && is_array($data) && array_key_exists($key, $data) && isset($data[$key]) ? $data[$key] : $empty_value;
    }

    /**
     * Функция проверяет существование ключа ассоциативного массива и устанавливает значение по ключу,
     * если существуют ключ. Возвращает true в случае успеха.
     * @param $data
     * @param $key
     * @param $value
     * @return bool
     */
    function set_assoc_element ($data, $key, $value) {
        $result = false;
        if (isset($data) && array_key_exists($key, $data) && isset($data[$key])) {
            $data[$key] = $value;
            $result = true;
        }
        return $result;
    }

    /**
     * Функция проверяет существование элемента массива и возвращает его, если он существует.
     * В противном случае будет возвращена пустая строка
     * @param $array
     * @param $index
     * @param boolean $array_return
     * @return any|string|array
     */
    function get_element ($array, $index, $array_return = false) {
        $empty_value = $array_return ? [] : '';
        return is_array($array) && isset($array[$index]) ? $array[$index] : $empty_value;
    }

    /**
     * Функция для предотвращения пустых атрибутов class в шаблоне.
     * Возвращает часть тега с названием класса, либо пустую строку
     * @param string $classname
     * @return string
     */
    function get_classname ($classname) {
        return empty($classname) ? '' : ' class="' . $classname . '" ';
    }

    /**
     * Функция проверяет наличие данных в массиве по ключу, фильтрует содержимое функцией strip_tags и убирает пробелы
     * @param $data
     * @param $key
     * @return string
     */
    function get_pure_data ($data, $key) {
        return isset($data) && array_key_exists($key, $data) && isset($data[$key]) ? trim(strip_tags($data[$key])) : '';
    }

    /**
     * Функция возвращает значение атрибута selected для выпадающего списка
     * @param $element_id
     * @param $current_id
     * @return string
     */
    function get_selected_state ($element_id, $current_id) {
        return $element_id === $current_id ? ' selected ' : '';
    }

    /** Функция пытается получить параметр msg из массива _GET. В случае неудачи выводит стандартное сообщение.
     * @param $get
     * @param string $standard_message
     * @return string
     */
    function get_error_info (&$get, $standard_message = 'Данной страницы не существует на сайте.') {
        $message = get_pure_data($get, 'msg');
        return empty($message) ? $standard_message : $message;
    }

    /**
     * Функция позаимствована на просторах интернета. Проверяет является ли нечто существующей папкой.
     * @param $folder
     * @return bool
     */
    function folder_exists ($folder) {
        $path = realpath($folder);
        return ($path !== false AND is_dir($path));
    }

    /**
     * Функция проверяет, существует ли путь, при отсутствии - пытается создать. Возвращает true, если путь существует
     * @param $base
     * @return bool
     */
    function check_and_repair_path ($base) {
        $result = folder_exists($base);
        return $result ? $result : mkdir(trim($base), 0700, true);
    }

    /**
     * Функция возвращает название класса для кнопки пагинации активной страницы
     * @param $page
     * @param $active
     * @return string
     */
    function get_active_page_classname ($page, $active) {
        return ($page === $active) ? 'pagination-item-active' : '';
    }

    /**
     * Возвращает текст href для кнопки пагинации "Назад"
     * @param $pagination_context
     * @param $active
     * @param $pre_page_string
     * @return string
     */
    function get_prev_href ($pagination_context, $active, $pre_page_string) {
        return $active > 1 ? ' href="' . $pagination_context . '.php?' . $pre_page_string . 'page=' . ($active - 1) . '"' : '';
    }

    /**
     * Функция возвращает текст href для кнопки пагинации "Вперед"
     * @param $pagination_context
     * @param $active
     * @param $last
     * @param $pre_page_string
     * @return string
     */
    function get_next_href ($pagination_context, $active, $last, $pre_page_string) {
        return $active < $last ? ' href="' . $pagination_context . '.php?' . $pre_page_string . 'page=' . ($active + 1) . '"' : '';
    }

    /**
     * Функция возвращает текст href для кнопки пагинации № n
     * @param $pagination_context
     * @param $page
     * @param $pre_page_string
     * @return string
     */
    function get_page_href ($pagination_context, $page, $pre_page_string) {
        return 'href="' . $pagination_context . '.php?' . $pre_page_string . 'page=' . ($page) . '"';
    }

    /**
     * Функция возвращает время в формате H:i:s, принимая в качестве параметра количество оставшихся секунд.
     * @param $seconds_left
     * @return string
     */
    function get_formatted_time_from_seconds ($seconds_left) {
        $seconds_left = empty($seconds_left) ? 0 : $seconds_left;
        $days = floor($seconds_left / (3600 * 24));
        $time = floor($seconds_left % (3600 * 24));
        $parts = explode(':', gmdate('H:i:s', $time));
        $parts[0] = intval($parts[0]) + $days * 24;
        return implode(':', $parts);
    }

    /** Функция возвращает атрибут в зависимости от передаваемого параметра
     * @param $value
     * @return string
     */
    function get_checked_attribute ($value) {
        return ($value) ? ' checked ' : '';
    }

    /**
     * Функция возвращает имя класса для задачи в зависимости от ее статуса и времени, оставшегося до завершения
     * @param $status
     * @param $seconds_left
     * @return string
     */
    function get_task_classname ($status, $seconds_left) {
        $seconds_left = empty($seconds_left) ? 0 : $seconds_left;
        $result = ($status) ? 'task--completed' : '';
        $result .= ($seconds_left <= 24 * 3600) ? ' ' . 'task--important' : '';
        return $result;
    }

    /**
     * Функция возвращает имя активного класса при совпадении индексов вкладок
     * @param $current
     * @param $tab
     * @return string
     */
    function get_current_tab_classname ($current, $tab) {
        return $current === $tab ? ' tasks-switch__item--active ' : '';
    }

    /**
     * Функция возвращает ссылку в зависимости от установленных фильтров и текущего проекта
     * @param $current_filter
     * @param $show_completed
     * @param null $project_id
     * @return string
     */
    function get_href_by_current_filters ($current_filter, $show_completed, $project_id = null) {
        return 'index.php?condition=' . $current_filter . '&show_completed=' . $show_completed . ($project_id ? '&project_id=' . $project_id : '');
    }

    /**
     * Функция возвращает инлайновый стиль для активного проекта, если он определен. Иначе - пустую строку.
     * @param $current_project
     * @param $project_id
     * @return string
     */
    function get_active_project_style ($current_project, $project_id) {
        return (intval($current_project) === intval($project_id)) ? ' style = "color: #6e45e2;" ' : '';
    }

    /**
     * Функция возвращает атрибут класса для гостевой страницы
     * @param $need_background
     * @return string
     *
     */
    function get_body_classname ($need_background) {
        return get_classname(($need_background) ?  'body-background' : '');
    }

    /**
     * Функция возвращает "чистое" читабельное имя файла без уникального идентификатора, либо изначальное имя файла
     * @param $name
     * @return bool|string
     */
    function get_pure_filename ($name) {
        return substr($name, 0, mb_strlen(UI_START)) === UI_START ? substr($name, TRANCATED_COUNT) : $name;
    }

    /**
     * Функция с просторов интернета, проверяющая, существует ли файл.
     * @param $filePath
     * @return bool
     */
    function fileExists ($filePath) {
        return is_file($filePath) && file_exists($filePath);
    }