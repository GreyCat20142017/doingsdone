<div class="page-wrapper">
    <div class="container container--with-sidebar">
        <header class="main-header">
            <?= $logo_content; ?>

            <div class="main-header__side">
                <a class="main-header__side-item button button--plus open-modal" href="form-task.php">Добавить
                    задачу</a>

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

                <a class="button button--transparent button--plus content__side-button"
                   href="form-project.php" target="project_add">Добавить проект</a>
            </section>

            <main class="content__main">
                <h2 class="content__main-heading">Список задач</h2>

                <form class="search-form" action="index.php" method="post">
                    <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">

                    <input class="search-form__submit" type="submit" name="" value="Искать">
                </form>

                <div class="tasks-controls">
                    <nav class="tasks-switch">
                        <a href="/" class="tasks-switch__item tasks-switch__item--active">Все задачи</a>
                        <a href="/" class="tasks-switch__item">Повестка дня</a>
                        <a href="/" class="tasks-switch__item">Завтра</a>
                        <a href="/" class="tasks-switch__item">Просроченные</a>
                    </nav>

                    <label class="checkbox">
                        <input
                            class="checkbox__input visually-hidden show_completed" <?= get_checked_attribute($show_completed); ?>
                        " type="checkbox">
                        <span class="checkbox__text">Показывать выполненные</span>
                    </label>
                </div>

                <table class="tasks">
                    <?php foreach ($tasks as $task): ?>
                        <?php if (!get_pure_data($task, 'status') || get_pure_data($task, 'status') && $show_completed): ?>
                            <tr class="tasks__item task <?= get_task_classname(get_pure_data($task, 'status'), get_pure_data($task, 'time_left')); ?>">
                                <td class="task__select">
                                    <label class="checkbox task__checkbox">
                                        <input class="checkbox__input visually-hidden task__checkbox" type="checkbox"
                                               value="<?= get_pure_data($task, 'status'); ?>" <?= get_checked_attribute(get_pure_data($task, 'status')); ?>>
                                        <span class="checkbox__text"> <?= get_pure_data($task, 'name'); ?></span>
                                    </label>
                                </td>

                                <td class="task__file">
                                    <a class="download-link" href="#"> <?= get_pure_data($task, 'file'); ?></a>
                                </td>

                                <td class="task__date"> <?= get_pure_data($task, 'expiration_date'); ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <!--показывать следующий тег <tr/>, если переменная $show_complete_tasks равна единице-->
                </table>
            </main>

        </div>
    </div>
</div>