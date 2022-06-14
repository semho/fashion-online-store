<?php
//признак передачи данных
$sendNewOrder = true;
//записываем полученный данные в переменные
//личные данные обязательные для заполнения
$name = strip_tags($_POST['name']);
$surname = strip_tags($_POST['surname']);
$phone = strip_tags($_POST['phone']);
$email = strip_tags($_POST['email']);
//необязательные для заполнения
$thirdName = strip_tags($_POST['thirdName']);
//способ доставки(dev-no/dev-yes)
$delivery = strip_tags($_POST['delivery']);
//обязательные поля способа доставки
$city = strip_tags($_POST['city']);
$street = strip_tags($_POST['street']);
$home = strip_tags($_POST['home']);
$aprt = strip_tags($_POST['aprt']);
//способ оплаты(cash/card)
$pay = strip_tags($_POST['pay']);
//комментарий к заказу
$comment = strip_tags($_POST['comment']);
//id товара
$product_id = strip_tags($_POST['currentProdId']);
//цена товара
$currentProdPrice = strip_tags($_POST['currentProdPrice']);
//цена доставки;
$addPrice = 0;

//записываем ошибку на пустоту заполнения, если она есть
$result = [];
//ошибка персональных данных
if (empty($name) || empty($surname) || empty($phone) || empty($email)) {
    $result['error'] = 'personal';
}
//ошибка доставки
if ($delivery == 'dev-yes' && (empty($city) || empty($street) || empty($home) || empty($aprt))) {
    $result['error'] = 'delivery';
}

if ($delivery == 'dev-yes') {
    $full_address = $city . ' ' . $street . ' ' . $home . ' ' . $aprt;
} else {
    $full_address = '';
}

//подключаем ядро
require $_SERVER["DOCUMENT_ROOT"] . '/src/core.php';

//если нет ошибок
if (empty($result['error'])) {
    //добавляем заказ
    $result['success'] = $newOrder;
    echo json_encode($result);
}


