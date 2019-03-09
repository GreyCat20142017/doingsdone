<div class="page-wrapper">
    <div class="container container--with-sidebar">
        <header class="main-header">
            <?= $logo_content; ?>

            <div class="main-header__side">
                <a class="main-header__side-item button button--plus" href="form-task.php">Добавить задачу</a>

                <div class="main-header__side-item user-menu">
                    <?= $user_content; ?>
                </div>
            </div>
        </header>

        <div class="content">
            <section class="content__side">
                <h2 class="content__side-heading">Проекты</h2>

                <nav class="main-navigation">
                    <?= $projects_content; ?>
                </nav>

                <a class="button button--transparent button--plus content__side-button" href="form-project.php">Добавить
                    проект</a>
            </section>

            <main class="content__main">
                <h2 class="content__main-heading">Добавление задачи</h2>

                <form class="form" action="form-task.php" method="post">
                    <div class="form__row">
                        <label class="form__label" for="name">Название <sup>*</sup></label>

                        <input class="form__input <?= get_field_validation_classname($errors, 'name') ?>" type="text"
                               name="name" id="name" value="<?= get_pure_data($task, 'name'); ?>"
                               placeholder="Введите название">
                        <p class="form__message"><?= get_field_validation_message($errors, 'name') ?></p>
                    </div>

                    <div class="form__row">
                        <?= $projects_dropdown; ?>
                    </div>

                    <div class="form__row">
                        <label class="form__label" for="date">Дата выполнения</label>

                        <input
                            class="form__input form__input--date <?= get_field_validation_classname($errors, 'date') ?>"
                            type="date" name="date" id="date"
                            value="<?= empty(get_pure_data($task, 'date')) ? '' : '' . date('Y-m-d', strtotime(get_pure_data($task, 'date'))); ?>"
                            placeholder="Введите дату в формате ДД.ММ.ГГГГ">
                        <p class="form__message"><?= get_field_validation_message($errors, 'date') ?></p>
                    </div>

                    <div class="form__row">
                        <label class="form__label" for="preview">Файл</label>

                        <div class="form__input-file <?= get_field_validation_classname($errors, 'preview') ?>">
                            <input class="visually-hidden" type="file" name="preview" id="preview"
                                   value="<?= get_pure_data($task, 'preview'); ?>">

                            <label class="button button--transparent" for="preview">
                                <span>Выберите файл</span>
                            </label>
                        </div>
                        <p class="form__message"><?= get_field_validation_message($errors, 'preview') ?></p>
                    </div>

                    <div class="form__row form__row--controls">
                        <p class="error-message"><?= get_form_validation_message($errors) ?></p>
                        <input class="button" type="submit" name="add-task" value="Добавить">
                    </div>
                </form>
            </main>
        </div>
    </div>
</div>