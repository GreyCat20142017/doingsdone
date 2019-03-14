<form class="search-form" action="index.php" method="post">
    <input class="search-form__input" type="text" name="search" value="<?= strip_tags($search_string); ?>"
           placeholder="Поиск по задачам">

    <input class="search-form__submit" type="submit" name="find" value="Искать">
</form>