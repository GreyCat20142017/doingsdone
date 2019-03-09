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
                <h2 class="content__main-heading">Добавление проекта</h2>

                <form class="form" action="form-project.php" method="post">
                    <div class="form__row">
                        <label class="form__label" for="project_name">Название <sup>*</sup></label>

                        <input class="form__input <?= get_field_validation_classname($errors, 'name') ?>" type="text"
                               name="name" id="project_name" value="<?= get_pure_data($task, 'name'); ?>"
                               placeholder="Введите название проекта">
                        <p class="form__message"><?= get_field_validation_message($errors, 'name') ?></p>
                    </div>

                    <div class="form__row form__row--controls">
                        <p class="error-message"><?= get_form_validation_message($errors); ?></p>
                        <input class="button" type="submit" name="" value="Добавить">
                    </div>
                </form>
            </main>
        </div>
    </div>
</div>