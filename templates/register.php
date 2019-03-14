<div class="page-wrapper">
    <div class="container container--with-sidebar">
        <header class="main-header">
            <?= $logo_content; ?>

            <div class="main-header__side">
                <a class="main-header__side-item button button--transparent" href="form-authorization.php">Войти</a>
            </div>
        </header>

        <div class="content">
            <section class="content__side">
                <p class="content__side-info">Если у вас уже есть аккаунт, авторизуйтесь на сайте</p>

                <a class="button button--transparent content__side-button" href="form-authorization.php">Войти</a>
            </section>
            <main class="content__main">
                <h2 class="content__main-heading">Регистрация аккаунта</h2>

                <form class="form" action="register.php" method="post">
                    <div class="form__row">
                        <label class="form__label" for="email">E-mail <sup>*</sup></label>

                        <input class="form__input <?= get_field_validation_classname($errors, 'email') ?>" type="text"
                               name="email" id="email"
                               value="<?= get_pure_data($user, 'email'); ?>"
                               placeholder="Введите e-mail">

                        <p class="form__message"><?= get_field_validation_message($errors, 'email') ?></p>
                    </div>

                    <div class="form__row">
                        <label class="form__label" for="name">Имя <sup>*</sup></label>

                        <input class="form__input <?= get_field_validation_classname($errors, 'name') ?>" type="text"
                               name="name" id="name"
                               value="<?= get_pure_data($user, 'name'); ?>"
                               placeholder="Введите имя">
                        <p class="form__message"><?= get_field_validation_message($errors, 'name') ?></p>
                    </div>

                    <div class="form__row">
                        <label class="form__label" for="password">Пароль <sup>*</sup></label>

                        <input class="form__input" type="password" name="password" id="password"
                               value="<?= get_pure_data($user, 'password'); ?>"
                               placeholder="Введите пароль">
                        <p class="form__message"><?= get_field_validation_message($errors, 'password') ?></p>
                    </div>


                    <div class="form__row form__row--controls">
                        <p class="error-message"><?= get_form_validation_message($errors) . $status ?></p>

                        <input class="button" type="submit" name="" value="Зарегистрироваться">
                    </div>
                </form>
            </main>
        </div>
    </div>
</div>