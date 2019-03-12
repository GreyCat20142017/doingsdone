<ul class="main-navigation__list">
    <?php foreach ($projects as $project): ?>
        <li class="main-navigation__list-item">
            <a class="main-navigation__list-item-link"
               href="<?= get_href_by_current_filters($current_filter, $show_completed, get_pure_data($project, 'id')); ?>">
                <?= get_pure_data($project, 'name'); ?>
            </a>
            <span
                class="main-navigation__list-item-count"> <?= get_pure_data($project, 'tasks_amount'); ?>
            </span>
        </li>
    <?php endforeach; ?>
</ul>