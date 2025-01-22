<?php
session_start();
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include_once $_SERVER['DOCUMENT_ROOT'] . '/components/User.php';
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manao</title>
    <meta name="description" content="Manao">
    <script type="text/javascript" src="/js/jquery.min.js"></script>
    <script src="/js/ajax.js"></script>
</head>

<body>

<?php if (User::checkAuth()): ?>

    <?= 'Hello ' . htmlspecialchars($_SESSION['login'], ENT_QUOTES); ?>

<?php else: ?>

    <a href="#" onclick="openbox('Auth'); return false">Вход</a> <a href="#"
                                                                    onclick="openbox('Register'); return false">Регистрация</a>

    <div id="Auth" style="display: none;">
        <form method="POST" id="formx" action="form.php" onsubmit="call()">
            <label for="login">
                login:
            </label>
            <input id="login" name="login" type="text"><br>

            <label for="password">
                password:
            </label>
            <input id="password" name="password" type="password">
            <input value="Send" type="submit">
        </form>
        <div id="results"></div>
    </div>

    <div id="Register" style="display: none;">
        <form method="POST" id="formx" action="reg.php" onsubmit="call()">
            <label for="login">
                login:
            </label>
            <input id="login" name="login" type="text"><br>

            <label for="email">
                email:
            </label>
            <input id="email" name="email" type="text"><br>

            <label for="password">
                password:
            </label>
            <input id="password" name="password" type="password"><br>

            <label for="confirm_password">
                confirm_password:
            </label>
            <input id="confirm_password" name="confirm_password" type="password"><br>
            <label for="name">
                name:
            </label>
            <input id="name" name="name" value="" type="text">
            <input value="Send" type="submit" name="data">
        </form>
        <div id="results"></div>
    </div>

    <div id="result_form"></div>

<?php endif; ?>
</body>
</html>

