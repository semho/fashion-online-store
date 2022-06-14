<?php
try {
    //подключаемся к БД
    $dbh = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    //получаем все товары без фильтра
    if (isset($_GET['page']) && isset($_GET['idCategory'])) {
        // $allProducts = getProducts($dbh, $_GET['page'], COUNT_PRODUCTS_PAGE);

        $allProducts = getProducts(
            $dbh,
            strip_tags($_GET['page']),
            COUNT_PRODUCTS_PAGE,
            [
            "id"=> strip_tags($_GET['idCategory']),
            "min_price" => strip_tags($_GET['min']),
            "max_price" => strip_tags($_GET['max']),
            "new" => digitalization(strip_tags($_GET['new'])),
            "sale" => digitalization(strip_tags($_GET['sale'])),
            "sort" => strip_tags($_GET['sort']),
            "direction" => strip_tags($_GET['direction'])
            ]
        );
        $lastElement = array_pop($allProducts);
    } else {
        $allProducts = getProducts($dbh, 1, COUNT_PRODUCTS_PAGE);
    }

    $prices = getPrice($dbh);

    //получаем все секции
    $allSections = getSections($dbh);

    //передается ли параметр в фильтре?
    if (isset($id)) {
        //если да, получаем все товары с указанными параметрами
        $allProductsFilter = getProducts(
            $dbh,
            $page,
            COUNT_PRODUCTS_PAGE,
            [
            "id"=> $id,
            "min_price" => $minPrice,
            "max_price" => $maxPrice,
            "new" => $new,
            "sale" => $sale,
            "sort" => $sort,
            "direction" => $direction
            ]
        );
    }
    $countProducts = getCountProducts($dbh);

    //если есть запрос на добавление нового товара
    if (isset($sendNewOrder)) {
        //если достака курьером и цена меньше необходимого уровня
        if ($delivery == 'dev-yes' && $currentProdPrice < MIN_PRICE_PRODUCT) {
            $addPrice = COST_DELIVERY;
        }
        //добавляем новый заказ в базу данных
        $newOrder = addOrder($dbh, [
            'name' => $surname . " " . $name . " " . $thirdName,
            'phone' => $phone,
            'email' => $email,
            'delivery' => $delivery,
            'pay' => $pay,
            'address' => $full_address,
            'comment' => $comment,
            'add_price' => $addPrice,
            'product_id' => $product_id,
        ]);
    }

    //если пользователь вводит логин, ищем его пароль в БД
    if ($login) {
        $passwordHash = getPassword($dbh, $login);
        //верифицируем пароль из формы с хэшом БД
        if (isset($_POST['password']) && password_verify(htmlspecialchars($_POST['password']), $passwordHash)) {
            $success = true;
            setcookie('login', $login, time()+60*60*24*30, '/');
            $_SESSION['auth'] = true;
        }
        //получаем id пользователя по логину
        $userIdByLogin = getUserId($dbh, $login);
        //получаем группу администратора текущего пользователя
        $groupAdmin = getGroup($dbh, "Администратор", $userIdByLogin);
    }
    //список необработынных заказов
    $listUnprocessedOrders = getListOrders($dbh, 0);
    //список обработынных заказов
    $listProcessedOrders = getListOrders($dbh, 1);

    //если есть запрос на изменение статуса заказа
    if (isset($sendNewStatus)) {
        $resultNewStatus = changeStatus($dbh, $newStatus, $orderId);
    }

    //получим список товаров в административном разделе
    $listProducts = getListProducts($dbh, 1, COUNT_PRODUCTS_ADMIN);
    //а так же количество записей
    $countProductsAdmin = getCountProductsAdmin($dbh);
    if (isset($pageAdmin)) {
        //получаем список товаров в административном разделе для ajax
        $listProductsAjax = getListProducts($dbh, $pageAdmin, COUNT_PRODUCTS_ADMIN);
    }
    //изменение товара в БД
    if (isset($idProdIsset) && $idProdIsset == true) {
        $changeProductDB = changeProduct($dbh, $dataAddProd);
    }
    //добавление нового товара в БД
    if (isset($addProd) && $idProdIsset == false) {
        $addProductDB = addProduct($dbh, $dataAddProd);
    }

    //получаем товар по его id
    if (isset($_GET['id'])) {
        $product = getProduct($dbh, htmlspecialchars($_GET['id']));
    }


    if (isset($deleteId)) {
        //деактивируем товар по его id
        // $inactiveProd = inactiveProduct($dbh, $deleteId);
        //удаляем товар
        $inactiveProd = deleteProduct($dbh, $deleteId);
    }

    //закрываем соединения с БД
    $dbh = null;
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
