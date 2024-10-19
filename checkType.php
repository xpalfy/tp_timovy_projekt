<?php
function check(): void
{
    if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'There is no user logged in!'];
        header('Location: ../login.php');
        exit();
    }
    $logged_in = $_SESSION['user']['logged_in'];
    if (!$logged_in) {
        session_unset();
        session_destroy();
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'You do not have permission to access this page!'];
        header('Location: ../login.php');
        exit();
    }
}