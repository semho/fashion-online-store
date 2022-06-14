<?php
require $_SERVER["DOCUMENT_ROOT"] . '/templates/header.php';
?>
<main class="page-authorization">
    <?php
    //вывод сообщения авторизации
    if (!empty($_POST) && !$success) {
        include $_SERVER["DOCUMENT_ROOT"] . '/include/error_message.php';
    } else if (isAuth()) {
        include $_SERVER["DOCUMENT_ROOT"] . '/include/success_message.php';
    }
    ?>
    <?php if (!isAuth()) : ?>
        <h1 class="h h--1">Авторизация</h1>
        <form class="custom-form" action="/admin/" method="post">
            <?php if (!isset($_COOKIE['login'])): ?>
                <input type="email" class="custom-form__input" required="" name="login" value="<?=(isset($_POST['login'])) ? htmlspecialchars($_POST['login']) : ''; ?>">
            <?php endif ?>
            <input type="password" class="custom-form__input" required="" name="password" value="<?=(isset($_POST['password'])) ? htmlspecialchars($_POST['password']) : ''; ?>">
            <button class="button" type="submit">Войти в личный кабинет</button>
        </form>
    <?php endif ?>

</main>
<?php
require $_SERVER["DOCUMENT_ROOT"] . '/templates/footer.php';
?>
