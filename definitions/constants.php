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

    define('FILTER_ALL', 'all');
    define('FILTER_TODAY', 'today');
    define('FILTER_TOMORROW', 'tomorrow');
    define('FILTER_EXPIRED', 'expired');

    define('FILTER_CONDITION', [
        FILTER_ALL => '',
        FILTER_TODAY => ' expiration_date >= TIMESTAMPADD(DAY, 0, DATE (NOW())) AND expiration_date < TIMESTAMPADD(DAY, 1, DATE(NOW())) ',
        FILTER_TOMORROW => ' expiration_date >= TIMESTAMPADD(DAY, 1, DATE (NOW())) AND expiration_date < TIMESTAMPADD(DAY, 2, DATE(NOW())) ',
        FILTER_EXPIRED => ' completion_date IS NULL AND expiration_date <= NOW() '
    ]);

    define('FILTER_TEXT', [
        FILTER_ALL => 'Все задачи',
        FILTER_TODAY => 'Повестка дня',
        FILTER_TOMORROW => 'Завтра',
        FILTER_EXPIRED => 'Просроченные'
    ]);

    define('DEFAULT_FILTER', FILTER_ALL);
    define('DEFAULT_PROJECT', null);
    define('DEFAULT_SHOW_COMPLETED', 1);

