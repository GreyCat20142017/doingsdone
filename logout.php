<?php
    session_start();
    require_once('init.php');

    logout_current_user();
    header('Location: index.php');