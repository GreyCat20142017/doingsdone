<?php

    require_once('init.php');
    require_once('./vendor/autoload.php');

    $notifies = get_notify_list($connection);

    $log = '<h3>Журнал отправки сообщений пользователям(' . date('d.m.Y H:i') . ')</h3>';

    if ($notifies && count($notifies) > 0) {
        try {

            $transport = new Swift_SmtpTransport('phpdemo.ru', 25);
            $transport->setUsername('keks@phpdemo.ru');
            $transport->setPassword('htmlacademy');

            foreach ($notifies as $notify) {

                $message_text = 'Уважаемый, ' . get_assoc_element($notify, 'username') . PHP_EOL .
                    'У Вас запланирована задача ' . get_assoc_element($notify, 'task_id') . ' на ' .
                    date('d.m.Y H:i:s', strtotime(get_assoc_element($notify, 'expiration_date')));

                $message = new Swift_Message('Уведомление от сервиса «Дела в порядке»');
                $type = $message->getHeaders()->get('Content-Type');
                $type->setValue('text/plain');
                $type->setParameter('charset', 'utf-8');
                /**
                 * Здесь должно бы быть get_assoc_element($notify, 'email'), но вдруг 'левые' адреса все-таки существуют.
                 * Поэтому... так...
                 */
                $message->setTo([TEST_EMAIL => get_assoc_element($notify, 'username')]);
                $message->setBody($message_text, 'text/plain');
                $message->setFrom('keks@phpdemo.ru', 'DoingsDone');
                $mailer = new Swift_Mailer($transport);
                $result = $mailer->send($message);

                $log = $log . '<p> Оповещение по задаче: ' . get_assoc_element($notify, 'task_id') . ' для ' . get_assoc_element($notify, 'username') . '</p>';
                $log = $log . '<p> <small> Email: ' . get_assoc_element($notify, 'email') . ', время:  ' . date('d.m.Y H:i:s') . ', статус: ' . ($result ? 'отправлено' : 'не удалось отправить') . '</small></p>';
                $log = $log . '<hr>';

            }

        } catch (Exception $e) {
        }

    } else {
        $log = $log . 'Нет данных для рассылки оповещений';
    }

    echo $log;
