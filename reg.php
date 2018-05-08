<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/components/User.php';
if (filter_has_var(INPUT_POST, 'data')) {

    $result = array(
        'login' => $_POST["login"],
        'password' => $_POST["password"],
        'confirm_password' => $_POST["confirm_password"],
        'email' => $_POST["email"],
        'name' => $_POST["name"]
    );

    $answer = User::register(json_encode($result));
    if (is_array($answer)) {
        foreach ($answer as $err) {
            echo $err . '<br />';
        }
    }
}
