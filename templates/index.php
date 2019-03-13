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
                <a class="button content__side-button" href="notify.php" style="margin-top: 20px;">Оповещения</a>
            </section>

            <main class="content__main">
                <h2 class="content__main-heading">Список задач</h2>

                <?= $search_content; ?>

                <div class="tasks-controls">
                    <nav class="tasks-switch">
                        <?php foreach ($condition_descriptions as $condition_key => $condition_text): ?>
                            <a href="<?= get_href_by_current_filters($condition_key, $show_completed, $current_project); ?>"
                               class="tasks-switch__item <?= get_current_tab_classname($current_filter, $condition_key); ?>">
                                <?= $condition_text; ?>
                            </a>
                        <?php endforeach; ?>
                    </nav>

                    <label class="checkbox">
                        <input
                            class="checkbox__input visually-hidden show_completed" <?= get_checked_attribute($show_completed); ?>
                         type="checkbox">
                        <span class="checkbox__text">Показывать выполненные</span>
                    </label>
                </div>

                <?php if (count($tasks) > 0): ?>
                    <table class="tasks">
                        <?php foreach ($tasks as $task): ?>
                            <?php if (!get_pure_data($task, 'status') || get_pure_data($task, 'status') && $show_completed): ?>
                                <tr class="tasks__item task <?= get_task_classname(get_pure_data($task, 'status'), get_pure_data($task, 'time_left')); ?>">
                                    <td class="task__select">
                                        <label class="checkbox task__checkbox">
                                            <input class="checkbox__input visually-hidden task__checkbox"
                                                   type="checkbox" id="<?= get_pure_data($task, 'id'); ?>"
                                                   value="<?= get_pure_data($task, 'id'); ?>" <?= get_checked_attribute(get_pure_data($task, 'status')); ?>>
                                            <span class="checkbox__text"> <?= get_pure_data($task, 'name'); ?></span>
                                        </label>
                                    </td>

                                    <td class="task__file">
                                        <a class="download-link" href="<?= $path . get_pure_data($task, 'file'); ?>">
                                            <?= get_pure_data($task, 'file'); ?>
                                        </a>
                                    </td>

                                    <td class="task__date"> <?= get_pure_data($task, 'expiration_date'); ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <p>Не найдено задач по заданным критериям</p>
                <?php endif; ?>
            </main>

        </div>
    </div>
</div>