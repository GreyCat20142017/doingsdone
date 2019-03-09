<?php

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['find'])) {

        $search_string = trim(get_pure_data($_GET, 'search'));
//        header('Location:/search-result.php?search=' . $search_string);

    }