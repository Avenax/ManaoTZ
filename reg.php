<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/components/User.php';
if (filter_has_var(INPUT_POST, 'data')) {

    $result = array(
        'login' = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_SPECIAL_CHARS),
        'password' => $_POST["password"],
        'confirm_password' => $_POST["confirm_password"],
        'email' => $_POST["email"],
        'name' = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS)
    );

    $answer = User::register(json_encode($result));
    if (is_array($answer)) {
        foreach ($answer as $err) {
            echo $err . '<br />';
        }
    }
}
