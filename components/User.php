<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

class User {
    private static $xml = 'users.xml';
    private static $nameAttribute = 'user';
    private static $salt = '$2a$07$R.gJb2U2N.FmZ4hPp1y2CN$';

    public static function register($data) {

        self::checkFile();
        $err = [];
        $data = json_decode($data);
        if ($data->password != $data->confirm_password) {
            $err[] = 'Пароли не совпадают.';
        }

        if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            $err[] = 'Не верный формат Email.';
        }

        if (self::checkLogin($data->login) == true) {
            $err[] = 'Этот логин занят';
        }

        if (self::checkLogin($data->login, 'email') == true) {
            $err[] = 'Этот email занят';
        }

        if (mb_strlen($data->password) < 6) {
            $err[] = 'Слишком кароткий пароль.';
        }

        if (mb_strlen($data->login) < 3) {
            $err[] = 'Слишком кароткий логин.';
        }

        if (mb_strlen($data->name) < 3) {
            $err[] = 'Слишком кароткое имя.';
        }

        if (empty($err)) {

            $dom = new DOMDocument();
            $dom->load(self::$xml);

            $desserts = $dom->getElementsByTagName('users')->item(0);
            $user = $dom->createElement(self::$nameAttribute);
            $user->setAttribute("id", $data->login);

            $us = $desserts->appendChild($user);

            $login = $dom->createElement("login", $data->login);
            $password = $dom->createElement("password", self::passwordHash($data->password));
            $email = $dom->createElement("email", $data->email);
            $name = $dom->createElement("name", $data->name);

            $us->appendChild($login);
            $us->appendChild($email);
            $us->appendChild($name);
            $us->appendChild($password);

            $dom->save(self::$xml);
            self::setCookie($data->login, $data->password);
            header('Location: /');
            exit();
        }
        return $err;
    }

    /**
     * @return bool
     */
    public static function checkAuth() {

        if (!isset($_COOKIE['pass']) || !isset($_COOKIE['login']) || !isset($_SESSION['pass']) || !isset($_SESSION['login'])) {
            return false;
        } else if ($_COOKIE['pass'] != self::checkPassword($_COOKIE['login']) || self::checkPassword($_COOKIE['login']) == false) {
            return false;
        } else if ($_SESSION['pass'] != self::checkPassword($_SESSION['login']) || self::checkPassword($_SESSION['login']) == false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Вход
     * @param $data
     * @return array
     */
    public static function auth($data) {
        $err = [];

        $data = json_decode($data);
        if (self::checkLogin($data->login) == false) {
            $err[] = 'Неверный логин.';
        }

        if (self::checkPassword($data->login) != self::passwordHash($data->password)) {
            $err[] = 'Неверный пароль.';
        }

        if (empty($err)) {
            self::setCookie($data->login, $data->password);
            header('Location: /');
            exit();
        }
        return $err;
    }

    private static function setCookie($login, $password) {
        session_start();
        $_SESSION['login'] = $login;
        $_SESSION['pass'] = self::passwordHash($password);

        setcookie('login', $login, time() + 84600 * 365, '/');
        setcookie('pass', self::passwordHash($password), time() + 84600 * 365, '/');
    }

    /**
     * Возвращает пароль
     * @param $login
     * @return mixed
     */
    private static function checkPassword($login) {
        return self::getData($login)['password'];
    }

    /**
     * Возвращает логин и пароль
     * @param $login
     * @return array|bool
     */
    private static function getData($login) {

        $xmlDoc = new DOMDocument;
        $xmlDoc->load(self::$xml);
        $searchNode = $xmlDoc->getElementsByTagName(self::$nameAttribute);

        $data = [];
        foreach ($searchNode as $searchNodes) {
            $valueID = $searchNodes->getAttribute('id');

            if ($valueID == $login) {

                $login = $searchNodes->getElementsByTagName("login");
                $data['login'] = $login->item(0)->nodeValue;

                $password = $searchNodes->getElementsByTagName("password");
                $data['password'] = $password->item(0)->nodeValue;

                return $data;
            }
        }
        return false;
    }

    /**
     * Хешируем пароль
     * @param $password
     * @return string
     */
    private static function passwordHash($password) {
        return hash('md5', self::$salt . $password, false);
    }

    /**
     * Чекаем логин в бд
     * @param $login
     * @return bool
     */
    private static function checkLogin($login, $search = 'login') {
        $dom = new DOMDocument;
        $dom->load(self::$xml);

        $values = $dom->getElementsByTagName($search);

        // 2 вариант проверки логина/мыло в бд
        $arr = [];
        foreach ($values as $value) {
            // Имхо, чтобы не перебирать весь массив, нашли логин, который нам нужен и обрываем цикл, возвращаем истину
            if ($value == $login) {
                return true;
            }
            // если использовать 2 вариант, то заносим в массив все попавшиеся логины
            $arr[] = $value->nodeValue;
        }

        // проверяем, есть ли логин в массиве, если до, то успех
        if (in_array($login, $arr)) {
            return true;
        }

        return false;
    }

    private static function checkFile() {
        if (!file_exists(self::$xml)) {
            $xml = new DOMDocument('1.0','utf-8');
            $xml->appendChild($xml->createElement('users'));
            $xml->formatOutput = true;
            $xml->save(self::$xml);
        }
    }
}
