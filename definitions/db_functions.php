<?php
    /**
     * Функция принимает ассоциативный массив с параметрами подключения к БД (host, user, password, database)
     * Возвращает соединение или false
     * @param $config
     * @return mysqli
     */
    function get_connection ($config) {
        $connection = mysqli_connect($config['host'], $config['user'], $config['password'], $config['database']);
        if ($connection) {
            mysqli_set_charset($connection, "utf8");
        }
        return $connection;
    }

    /**
     * Функция принимает соединение, текст запроса и пользовательское сообщение для вывода в случае ошибки.
     * Возвращает либо данные, полученные из БД в виде массива, либо ассоциативный массив с описанием ошибки
     * @param $connection
     * @param $query
     * @param string $user_error_message
     * @param bool $single
     * @return array|null
     */
    function get_data_from_db (&$connection, $query, $user_error_message, $single = false) {
        $data = [[ERROR_KEY => $user_error_message]];
        if ($connection) {
            $result = mysqli_query($connection, $query);
            if ($result) {
                $data = $single ? mysqli_fetch_assoc($result) : mysqli_fetch_all($result, MYSQLI_ASSOC);
            } else {
                $data = [[ERROR_KEY => mysqli_error($connection)]];
            }
        }
        return $data;
    }

    /**
     * Функция устанавливает, имел ли место факт ошибки при получении данных, анализируя переданный по ссылке массив,
     * полученный функцией get_data_from_db
     * @param $data
     * @return bool
     */
    function was_error (&$data) {
        return isset($data[0]) && array_key_exists(ERROR_KEY, $data[0]);
    }

    /**
     * Функция для совместного использования с функцией was_error. Возвращает описание ошибки.
     * @param array $data
     * @return string
     */
    function get_error_description (&$data) {
        return isset($data[0]) ? get_assoc_element($data[0], ERROR_KEY) : 'Неизвестная ошибка...';
    }

    /**
     * Функция возвращает в виде ассоциативного массива список проектов либо пустой массив (в случае ошибки или отсутствия данных)
     * @param $connection
     * @param $user_id
     * @return array
     */
    function get_user_projects ($connection, $user_id) {
        $sql = 'SELECT p.id, p.name, CASE WHEN tt.tasks_amount IS NULL THEN 0 ELSE tt.tasks_amount END AS tasks_amount
                    FROM projects AS p
                           LEFT OUTER JOIN (SELECT COUNT(*) AS tasks_amount, t.project_id
                                            FROM tasks AS t
                                            WHERE t.user_id = ' . mysqli_real_escape_string($connection, $user_id) . '
                                            GROUP BY t.project_id) AS tt ON p.id = tt.project_id
                    WHERE user_id =' . mysqli_real_escape_string($connection, $user_id) . ';';
        $data = get_data_from_db($connection, $sql, 'Невозможно получить данные о проектах');
        return (!$data || was_error($data)) ? [] : $data;
    }

    /**
     * Функция возвращает в виде ассоциативного массива список задач либо пустой массив (в случае ошибки или отсутствия данных)
     * @param $connection
     * @param $user_id
     * @param $show_completed
     * @param $project_id
     * @param $search_string
     * @param string $filter_string
     * @return array|null
     */
    function get_user_tasks ($connection, $user_id, $show_completed, $project_id, $search_string, $filter_string = 'none') {
        $show_condition = $show_completed ? '' : ' AND status = 0 ';
        $project_condition = $project_id ? ' AND project_id = ' . mysqli_real_escape_string($connection, $project_id) . ' ' : '';
        $filter_condition = get_assoc_element(FILTER_CONDITION, mysqli_real_escape_string($connection, $filter_string));
        $filter_condition = empty($filter_condition) ? '' : ' AND ' . $filter_condition;
        $search_condition = empty($search_string) ? '' : ' AND  MATCH(name) AGAINST("' . $search_string . '" IN BOOLEAN MODE)';
        $sql = 'SELECT id, name , file, expiration_date, status, 
                      GREATEST(0, TIMESTAMPDIFF(SECOND, NOW(), expiration_date))  AS time_left FROM tasks 
                      WHERE user_id = ' . mysqli_real_escape_string($connection, $user_id) .
            $show_condition . $project_condition . $search_condition . $filter_condition .
            ' ORDER BY id DESC;';
        $data = get_data_from_db($connection, $sql, 'Невозможно получить данные о задачах');
        return (!$data || was_error($data)) ? [] : $data;
    }

    /**
     * Функция проверяет существование ключа в указанной таблице БД
     * @param $connection
     * @param $table
     * @param $id
     * @return array|null
     */
    function get_id_existance ($connection, $table, $id) {
        $data = [[ERROR_KEY => 'Id =  ' . $id . ' в таблице ' . $table . ' не существует! ']];
        $sql = 'SELECT id FROM ' . $table . ' WHERE id = ' . mysqli_real_escape_string($connection, $id) . ' LIMIT 1;';
        $result = get_data_from_db($connection, $sql, 'Невозможно получить id из таблицы ' . $table, true);
        if ($result) {
            $data = $result;
        }
        return $data;
    }

    /**
     * Функция возращает ошибку, если невозможно получить данные из БД, массив с id пользователя, если пользователь
     * с таким email существует, null - если не было ошибки и такого пользователя нет в БД
     * @param $connection
     * @param $email
     * @return null || array
     */
    function get_id_by_email ($connection, $email) {
        $sql = 'SELECT id FROM users WHERE email = "' . mysqli_real_escape_string($connection, $email) . '" LIMIT 1;';
        return get_data_from_db($connection, $sql, 'Невозможно получить id пользователя', true);
    }

    /**
     * Функция возвращает true в случае успешного добавления пользователя, false - в случае ошибки
     * Если пользователь с таким email уже сушествовал - возвращается массив c id.
     * @param $connection
     * @param $user
     * @return bool || array
     */
    function add_user ($connection, $user) {

        $user_status = get_id_by_email($connection, get_assoc_element($user, 'email'));

        if ($user_status) {
            return $user_status;
        }

        $sql = 'INSERT INTO users ( email, name, user_password) 
                          VALUES ( ?, ?, ?)';

        $stmt = db_get_prepare_stmt($connection, $sql, [
            get_assoc_element($user, 'email'),
            get_assoc_element($user, 'name'),
            password_hash(get_assoc_element($user, 'password'), PASSWORD_DEFAULT)
        ]);

        $res = mysqli_stmt_execute($stmt);

        return ($res) ? true : false;
    }

    /**
     * Функция возвращает результат запроса в виде ассоциативного массива со статусом и данными
     * @param $connection
     * @param $email
     * @return array|null
     */
    function get_user_by_email ($connection, $email) {
        $sql = 'SELECT id, email, user_password, name FROM users WHERE email="' . mysqli_real_escape_string($connection, $email) . '" LIMIT 1;';
        $data = get_data_from_db($connection, $sql, 'Невозможно получить данные пользователя', true);
        if (!$data) {
            $result = ['status' => get_assoc_element(GET_DATA_STATUS, 'no_data'), 'data' => null];
        } else if (was_error($data)) {
            $result = ['status' => get_assoc_element(GET_DATA_STATUS, 'db_error'), 'data' => null];
        } else {
            $result = ['status' => get_assoc_element(GET_DATA_STATUS, 'data_received'), 'data' => $data];
        }
        return $result;
    }

    /**
     * Функция возвращает true или false в зависимости от результата добавления проекта. В случае наличия ошибок, добавбляет описание
     * ошибки в соответствующий массив
     * @param $connection
     * @param $current_user
     * @param $name
     * @param $errors
     * @return bool
     */
    function add_project ($connection, $current_user, $name, &$errors) {

        $user_status = get_id_existance($connection, 'users', $current_user);
        $project_status = get_project_status($connection, $current_user, $name);
        $was_errors = false;

        if (was_error($user_status) || was_error($project_status)) {
            add_error_message($errors, 'name', 'Попытка использовать некорректные данные  или ошибка при проверке данных.');
            $was_errors = true;
        }

        if ($project_status && !was_error($project_status)) {
            add_error_message($errors, 'name', 'Проект с таким имененм уже есть в БД!');
            $was_errors = true;
        }

        if ($was_errors) {
            return false;
        }

        $sql = 'INSERT INTO projects (user_id, name) 
                          VALUES ( ?, ?)';
        $stmt = db_get_prepare_stmt($connection, $sql, [
            $current_user,
            $name
        ]);
        $res = mysqli_stmt_execute($stmt);
        return $res ? true : false;
    }

    /**
     * Функция возращает ошибку, если невозможно получить данные из БД, массив с id проекта, если проект
     * с таким названием существует, null - если не было ошибки и такого пользователя нет в БД
     * @param $connection
     * @param $user_id
     * @param $name
     * @return null || array
     */
    function get_project_status ($connection, $user_id, $name) {
        $sql = 'SELECT id FROM projects WHERE user_id = ' . mysqli_real_escape_string($connection, $user_id) .
            ' AND name="' . mysqli_real_escape_string($connection, $name) . '" LIMIT 1;';
        return get_data_from_db($connection, $sql, 'Невозможно получить id проекта по названию', true);
    }

    /**
     * Функция принимает соединение и массив с данными формы. Возвращает либо массив с id добавленной записи задачи, либо массив с ошибкой
     * В случае попытки использовать несуществующие id пользователя или id проекта возвращает ошибку
     * @param $connection
     * @param $task
     * @param int $current_user
     * @return array
     */
    function add_task ($connection, $task, $current_user = 1) {
        $current_project = get_assoc_element($task, 'project');
        $user_status = get_id_existance($connection, 'users', $current_user);
        $project_status = get_id_existance($connection, 'projects', $current_project);

        if (was_error($project_status) || was_error($user_status)) {
            return ['error' => 'Попытка использовать несуществующие данные для добавления задачи. Задача не будет добавлена!'];
        }

        $sql = 'INSERT INTO tasks (project_id, user_id,  name, file, expiration_date) 
                          VALUES ( ?, ?, ?, ?, ?)';
        $stmt = db_get_prepare_stmt($connection, $sql, [
            $current_project,
            $current_user,
            get_assoc_element($task, 'name'),
            get_assoc_element($task, 'preview'),
            date('Y-m-d H:i:s', strtotime(get_assoc_element($task, 'date')))
        ]);
        $res = mysqli_stmt_execute($stmt);
        return ($res) ? ['id' => mysqli_insert_id($connection)] : ['error' => mysqli_error($connection)];
    }

    /**
     * Функция инвертирует статус задачи
     * @param $connection
     * @param $task_id
     * @return bool|mysqli_result
     */
    function update_task_status_by_id ($connection, $task_id) {
        $sql = 'UPDATE tasks 
                  SET status = (CASE WHEN status = 1 THEN 0 ELSE 1 END), completion_date = (CASE WHEN status = 1 THEN NOW() ELSE NULL END)
                  WHERE id = ' . mysqli_real_escape_string($connection, $task_id) . ';';
        return mysqli_query($connection, $sql);
    }

    /**
     * Функция проверяет существование ключа в указанной таблице БД
     * @param $connection
     * @param $user_id
     * @param $id
     * @return bool
     */
    function get_project_existance ($connection, $user_id, $id) {
        $sql = 'SELECT id FROM projects  WHERE id = ' . mysqli_real_escape_string($connection, $id) .
            ' AND user_id = ' . mysqli_real_escape_string($connection, $user_id) . ' LIMIT 1;';
        $result = get_data_from_db($connection, $sql, 'Невозможно получить id проекта для пользователя', true);
        return $result ? true : false;
    }

    /**
     * Функция возвращает список задач с данными пользователей для отправки оповещений либо пустой массив
     * @param $connection
     * return array
     * @return array|null
     */
    function get_notify_list ($connection) {
        $sql = 'SELECT t.id, t.name, t.expiration_date, u.name AS username, u.email
                FROM tasks AS t
                       JOIN users AS u ON t.user_id = u.id
                WHERE status = 0
                        AND t.expiration_date > NOW()
                        AND t.expiration_date <= TIMESTAMPADD(HOUR, 1, NOW())';
        $data = get_data_from_db($connection, $sql, 'Невозможно получить данные для выявления победителей');
        return (!$data || was_error($data)) ? [] : $data;
    }