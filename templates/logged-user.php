<?php if ($is_auth): ?>
    <div class="user-menu__image">
        <img src="../img/user.png" width="40" height="40" alt="Пользователь">
    </div>

    <div class="user-menu__data">
        <p><?= $user_name; ?></p>
        <a href="logout.php">Выйти</a>
    </div>
<?php endif; ?>