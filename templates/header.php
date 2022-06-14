<?php
error_reporting(E_ALL);
ini_set('display_errors', true);
//начало сессии
session_start();
//подключаем ядро
require_once $_SERVER["DOCUMENT_ROOT"] . '/src/core.php';
//используем буферизацию для возможности добавления новых заголовков
ob_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <title>Fashion</title>

  <meta name="description" content="Fashion - интернет-магазин">
  <meta name="keywords" content="Fashion, интернет-магазин, одежда, аксессуары">

  <meta name="theme-color" content="#393939">

  <link rel="preload" href="/img/intro/coats-2018.jpg" as="image">
  <link rel="preload" href="/fonts/opensans-400-normal.woff2" as="font" crossorigin>
  <link rel="preload" href="/fonts/roboto-400-normal.woff2" as="font" crossorigin>
  <link rel="preload" href="/fonts/roboto-700-normal.woff2" as="font" crossorigin>

  <link rel="icon" href="/img/favicon.png">
  <link rel="stylesheet" href="/css/style.min.css">

  <script src="/js/jquery/jquery-1.12.4.js"></script>
  <script src="/js/jquery/jquery-ui.js"></script>
  <script src="/js/scripts.js" defer></script>
  <script src="/js/main.js" defer></script>
</head>
<body>
<header class="page-header">
    <a class="page-header__logo" href="/">
        <img src="/img/logo.svg" alt="Fashion">
    </a>
    <nav class="page-header__menu">
        <?php showMenu($mainMenu, 'sort', 'SORT_ACS', 'main-menu--header'); ?>
    </nav>
    <?php if (isAuth()) : ?>
        <div class="control-panel">
            <ul class="control-panel__list">
                <li class="control-panel__item">
                    <a href="/admin/orders/" class="control-panel__link">Список заказов</a>
                </li>
                <?php if (isAdmin($groupAdmin)) :?>
                    <li class="control-panel__item">
                        <a href="/admin/products/" class="control-panel__link">Список товаров</a>
                    </li>
                    <li class="control-panel__item">
                        <a href="/admin/products/add.php" class="control-panel__link">Добавить товар</a>
                    </li>
                <?php endif ?>
            </ul>
        </div>
    <?php endif ?>
    <div class="auth">
        <?php if (!isAuth()): ?>
            <a class="auth__in" href="/admin/">Авторизация</a>
        <?php else: ?>
            <a class="auth__exit" href="/admin/?login=no">Выйти</a>
        <?php endif ?>
    </div>
</header>
