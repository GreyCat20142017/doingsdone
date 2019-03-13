<h1>Уведомление о предстоящей задаче</h1>
<p>Уважаемый, <?= get_pure_data($user, 'username'); ?></p>
<p>У Вас запланирована задача <?= get_pure_data($user, 'task'); ?> на <?= get_pure_data($user, 'expiration_date'); ?>
</p>
<small>Сервис "Дела в порядке"</small>
