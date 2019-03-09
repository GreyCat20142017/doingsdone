<?php if (was_error($categories)): ?>
    <label class="form__label" for="project">Проект</label>
    <select
        class="form__input form__input--select <?= get_field_validation_classname($errors, 'project') ?>"
        name="project" id="project">
        <option><?= get_error_description($projects); ?></option>
    </select>
    <p class="form__message">Ошибка при получении данных</p>
<?php else: ?>
    <label class="form__label" for="project">Проект</label>
    <select
        class="form__input form__input--select <?= get_field_validation_classname($errors, 'project') ?>"
        name="project" id="project">
        <option value="0"><?= $empty_project; ?></option>
        <?php foreach ($projects as $project): ?>
            <option value="<?= get_assoc_element($project, 'id'); ?>"
                <?= get_selected_state(get_assoc_element($project, 'id'), $current); ?>>
                <?= get_assoc_element($project, 'name'); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <span class="form__error"><?= get_field_validation_message($errors, 'project') ?></span>
<?php endif; ?>
