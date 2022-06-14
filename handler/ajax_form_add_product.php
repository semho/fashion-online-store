<?php
//массив результатов
$result = [];
//директория где лежат файлы
$dirUpload = '/img/products/';
//директория для загрузки
$uploadPath = $_SERVER['DOCUMENT_ROOT'] . $dirUpload;
//проверки на пустоту
if (!isset($_POST['category']) || empty($_POST['category'])) {
    $result['error'] = 'Выберите одну или несколько категорий.';
}

if ($_FILES['product-photo']['size'] == 0 && !isset($_POST['preload'])) {
    $result['error'] = 'Добавьте изображение товара.';
}

foreach ($_POST as $value) {
    if (empty($value)) {
        $result['error'] = 'Заполните все данные о товаре.';
    }
}

$arrCategories = [
    1 => 'female',
    2 => 'male',
    3 => 'children',
    4 => 'access',
];
//возвращает строку с заменой запрещенных символов на подчеркивание
function replaceName($string): string
{
    return preg_replace('/[^-_.\w-]/', '_', $string);
}

if (empty($result['error'])) {
    $idProdIsset = false;
    if (!empty($_POST['id'])) {
        $idProdIsset = true;
    }
    $addProd = true;
    //переменная для новинок
    if (empty($_POST['new'])) {
        $new = 0;
    } else {
        $new = (int)($_POST['new']);
    }
    //переменная для распрадажи
    if (empty($_POST['sale'])) {
        $sale = 0;
    } else {
        $sale = (int)($_POST['new']);
    }
    //переменная активности товара
    if (empty($_POST['active'])) {
        $active = 0;
    } else {
        $active = (int)($_POST['active']);
    }
    //массив с категориями в виде строки
    $category = $_POST['category'];
    //массив с номерами категорий
    $categoryDC = [];
    foreach ($category as $item) {
        $categoryDC[] = array_search($item, $arrCategories);
    }
    //переменная для названия товара
    $name = strip_tags($_POST['product-name']);
    //переменная для цены товара
    $price = strip_tags($_POST['product-price']);

    if (!isset($_POST['preload'])) {
         //создаем директорию загрузки если ее нет
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        //сам файл
        $file = $_FILES['product-photo'];
        //его имя
        $imgName = replaceName($file['name']);
        //перемещаем в директорию
        move_uploaded_file($file['tmp_name'], $uploadPath . $imgName);
    } else {
        $imgName = $_POST['preload'];
    }


    //собираем все в один массив
    $dataAddProd = [
        'fname' => $name,
        'fprice' => $price,
        'fis_active' => $active,
        'fimg' => $imgName,
        'fis_new' => $new,
        'fis_sale' => $sale,
        'fcat' => $categoryDC,
    ];
    //если есть id товара, добавим id в массив
    if ($idProdIsset) {
        $dataAddProd = ['fid' => $_POST['id']] + $dataAddProd;
    }

    require $_SERVER["DOCUMENT_ROOT"] . '/src/core.php';
    //изменяем товар в БД, если паретр с id не пустой
    if ($idProdIsset) {
        $result['success'] = $changeProductDB;
    //иначе мы добавляем товар в БД
    } else {
       $result['success'] = $addProductDB;
    }
}

echo json_encode($result);


