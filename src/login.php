<?php

//проверка на разавторизацию
if (isset($_GET['login']) && ($_GET['login'] == 'no')) {
    unset($_SESSION['auth']);
    session_destroy();
    header('Location: /');
    die();
}
//если куки уже есть обновляем ее на каждой странице
if (isset($_COOKIE['login'])) {
    setcookie('login', strip_tags($_COOKIE['login']), time() + 60 * 60 * 24 * 30, '/');
}
//флаг проверки на совпадения логин-пароль
$success = false;
//условие для проверки совпадения пары логин-пароль
$login = $_COOKIE['login'] ?? $_POST['login'] ?? null;



