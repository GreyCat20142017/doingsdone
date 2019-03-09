<?php
    define('RECORDS_PER_PAGE', 4);
    define('ERROR_KEY', 'error');
    define('EMPTY_PROJECT', 'Выберите проект');

    define('FILE_RULE', 'file_validation');
    define('MAX_FILE_SIZE', 200000);

    define('VALID_FILE_TYPES', [
        'image/png',
        'image/jpeg',
        'text/plain',
        'application/pdf']);

    define('PATHS', [
        'files' => 'files/'
    ]);

    define('GET_DATA_STATUS', [
        'db_error' => 'Ошибка БД при получении данных',
        'no_data' => 'В выборке нет данных',
        'data_received' => 'Данные получены',
        'data_added' => 'Данные добавлены'
    ]);

    define('DDONE_SESSION', 'current_user');

    define('TEST_EMAIL', 'nrz3siaatg81@mail.ru');