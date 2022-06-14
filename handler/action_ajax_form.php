<?php

//записываем полученный данные в переменные
$id = strip_tags($_POST['categoryId']);
$new = (int)($_POST['new']);
$sale = (int)($_POST['sale']);
$minPrice = (float)($_POST['min']);
$maxPrice = (float)($_POST['max']);
$page = (int)($_POST['page']);
//селект сортировки по цене и названию
$sort = strip_tags($_POST['sort']);
$direction = strip_tags($_POST['direction']);

require $_SERVER["DOCUMENT_ROOT"] . '/src/core.php';

//получаем все продукты по данным из фильтра и отправляем обратно клиенту в виде json
echo json_encode($allProductsFilter);





