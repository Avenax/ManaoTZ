<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/components/User.php';
if (filter_has_var(INPUT_POST, 'login') && filter_has_var(INPUT_POST, 'password')) {

    $result = array(
        'login' => $_POST["login"],
        'password' => $_POST["password"]
    );

    $answer = User::auth(json_encode($result));
    if (is_array($answer)) {
        foreach ($answer as $err) {
            echo $err . '<br />';
        }
    }
}
