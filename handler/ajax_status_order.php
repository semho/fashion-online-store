<?php
//признак передачи данных
$sendNewStatus = true;
//статус
$newStatus = (int)($_POST['status']);
//id заказа
$orderId = (int)($_POST['id']);
//подключаем ядро
require $_SERVER["DOCUMENT_ROOT"] . '/src/core.php';
//меняем статус
echo json_encode($resultNewStatus);
