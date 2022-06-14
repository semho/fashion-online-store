<?php
//функция обрезает строку
function cutString($line, $length = 12, $appends = '...'): string
{
    if (mb_strlen($line) >= $length) {
        return mb_substr($line, 0, 12) . $appends;
    } else {
        return $line;
    }
}
//функция сортирует многомерный ассоциативный массив
function arraySort(array $array, $key = 'sort', $sort = SORT_ASC): array
{
    //по возрастанию
    if ($sort == SORT_ASC) {
        usort($array, function($a, $b) use ($key)
        {
            if ($a[$key] == $b[$key]) {
                return 0;
            }
            return ($a[$key] < $b[$key]) ? -1 : 1;
        });
    //по убыванию
    } elseif ($sort == SORT_DESC) {
        usort($array, function($a, $b) use ($key)
        {
            if ($a[$key] == $b[$key]) {
                return 0;
            }
            return ($a[$key] > $b[$key]) ? -1 : 1;
        });
    }
    return $array;
}
/** функция отображает меню сайта
 * @params $array - передаваемый массив с пунктами меню
 * @params $key - поле для сортировки
 * @params $sort - порядок сортировки
 * @params $style - передаем CSS класс для меню
 */
function showMenu(array $array, $key = 'sort', $sort = SORT_ASC, $style = '')
{
    $sortArray = arraySort($array, $key, $sort);
    include $_SERVER["DOCUMENT_ROOT"] . '/templates/menu.php';
}
function showSubMenu(array $array, $key = 'sort', $sort = SORT_ASC, $style = '')
{
    $sortArray = arraySort($array, $key, $sort);
    include $_SERVER["DOCUMENT_ROOT"] . '/templates/sub_menu.php';
}
//функция возвращает верное окончание числа моделей
function wordEnding($num): string
{
    if ($num % 100 >= 10 && $num % 100 <= 20) {
        return "моделей";
    } elseif ($num % 10 == 1) {
        return "модель";
    } elseif ($num % 10 >= 2 && $num % 10 <= 4 ) {
        return "модели";
    } else {
        return "моделей";
    }
}
//преобразуем число в строку
function digitalization($str): int
{
    if ($str == 'false') {
        return 0;
    } else {
        return 1;
    }
}

/**
 * запрос для получения всех товаров, возвращает ассоциативный массив
 * @param [type] $dbh - подключение к БД
 * @param int $page - номер страницы пагинции
 * @param int $countProductsPage - количество товаров на страние
 * @param array ...$optional - опционный массив с данными из фильтра
 * @return array
 */
function getProducts($dbh, $page, $countProductsPage, ...$optional): array
{
    //номер начальной позиции для выборки товаров
    $start = ($page - 1) * $countProductsPage;

    //если есть опционный параметр и он не пустой
    if (!empty($optional[0])) {
        //если id секции не номер, значит товары показываем из всех секций
        if (!is_numeric($optional[0]['id'])) {
            unset($optional[0]['id']);
            $sql = "FROM products WHERE price BETWEEN :min_price AND :max_price AND products.is_active = 1";
            //запрос для вывода товаров по фильтру
            $sqlFields = "SELECT id, name, price, img ${sql}";
            $result = getProductsFilter(
                $dbh,
                $sqlFields,
                $optional[0],
                "LIMIT ${start}, ${countProductsPage}"
            );
            //запрос для вывода количества товаров по фильтру
            $sqlCounts = "SELECT COUNT(*) as countSection ${sql}";
            $resultCount = getProductsFilter(
                $dbh,
                $sqlCounts,
                $optional[0]
            );
            //добавим в массив с количеством товаров, количество товаров которое хотим видеть на странице
            $resultCount[0]["countPage"] = $countProductsPage;
            //и объединим в общий вывод
            $result[] = $resultCount[0];

        //иначе показываем товары выбранной секции
        } else {
            $sql = "FROM products
            INNER JOIN product_section ON product_section.product_id = products.id
            INNER JOIN sections ON product_section.section_id = sections.id
            WHERE products.price BETWEEN :min_price AND :max_price AND sections.id = :id AND products.is_active = 1";
            $sqlFields = "SELECT products.id, products.name, products.price, products.img ${sql}";
            $result = getProductsFilter(
                $dbh,
                $sqlFields,
                $optional[0],
                "LIMIT ${start}, ${countProductsPage}"
            );
            $sqlCounts = "SELECT COUNT(*) as countSection ${sql}";
            $resultCount = getProductsFilter(
                $dbh,
                $sqlCounts,
                $optional[0]
            );
            //добавим в массив с количеством товаров, количество товаров которое хотим видеть на странице
            $resultCount[0]["countPage"] = $countProductsPage;
            //и объединим в общий вывод
            $result[] = $resultCount[0];

        }
    //иначе делаем прямой запрос без параметров
    } else {
        $stmt = $dbh->query("SELECT id, name, price, img FROM products WHERE products.is_active = 1 LIMIT ${start}, ${countProductsPage}");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    //возвращаем массив с товарами
    return $result;
}
//запрос для вывода всех секций
function getSections($dbh): array
{
    $stmt = $dbh->query("SELECT name, id FROM sections");
    //возвращаем массив с товарами
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
//запрос для вывода id секций по id товара
function getSectionsProductId($dbh, $id): array
{
    $stmt = $dbh->prepare("SELECT section_id FROM product_section WHERE product_id = :id");
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $array = [];
    foreach ($result as $value) {
        $array[] = (int)$value['section_id'];
    }
    //возвращаем массив с id секций
    return $array;
}
//удаление привязанных секций по id товара
function deleteSectionsById($dbh, $id)
{
    $stmt = $dbh->prepare("DELETE FROM product_section WHERE product_id = :id");
    $stmt->execute(['id' => $id]);
}
//привязка новых секций к товару
function addSectionsById($dbh, $id, $cat)
{
    $sql = "INSERT INTO product_section (product_id, section_id) VALUES (:product_id, :section_id)";
    $stmt = $dbh->prepare($sql);
    return $stmt->execute(['product_id' => $id, 'section_id' => $cat]);
}

/**
 * возвращает массив товаров с параметрами фильтра
 *
 * @param [type] $dbh - подключение к БД
 * @param string $sql - тело запроса
 * @param array  $arr - массив с значениями фильтра
 * @param string $sql_limit - опционный запрос на ограничение вывода товаров
 * @return array
 */
function getProductsFilter($dbh, $sql, $arr, ...$sql_limit): array
{
    //новинки и скидки
    if ($arr['new'] == 1 && $arr['sale'] == 1) {
        if (empty($sql_limit)) {
            $stmt = $dbh->prepare("${sql} AND is_new = :new AND is_sale = :sale ORDER BY products." . $arr['sort'] . " " . $arr['direction']);
        } else {
            $stmt = $dbh->prepare("${sql} AND is_new = :new AND is_sale = :sale ORDER BY products." . $arr['sort'] . " " . $arr['direction'] . " " . $sql_limit[0]);
        }

        $execute = ['min_price' => $arr['min_price'], 'max_price' => $arr['max_price'], 'new' => $arr['new'], 'sale' => $arr['sale']];
    //только новинки
    } elseif ($arr['new'] == 1 && $arr['sale'] == 0) {
        if (empty($sql_limit)) {
            $stmt = $dbh->prepare("${sql} AND is_new = :new ORDER BY products." . $arr['sort'] . " " . $arr['direction']);
        } else {
            $stmt = $dbh->prepare("${sql} AND is_new = :new ORDER BY products." . $arr['sort'] . " " . $arr['direction'] . " " . $sql_limit[0]);
        }

        $execute = ['min_price' => $arr['min_price'], 'max_price' => $arr['max_price'], 'new' => $arr['new']];
    //только скидки
    } elseif ($arr['new'] == 0 && $arr['sale'] == 1) {
        if (empty($sql_limit)) {
            $stmt = $dbh->prepare("${sql} AND is_sale = :sale ORDER BY products." . $arr['sort'] . " " . $arr['direction']);
        } else {
            $stmt = $dbh->prepare("${sql} AND is_sale = :sale ORDER BY products." . $arr['sort'] . " " . $arr['direction'] . " " . $sql_limit[0]);
        }

        $execute = ['min_price' => $arr['min_price'], 'max_price' => $arr['max_price'], 'sale' => $arr['sale']];
    //все остальное
    } else {
        if (empty($sql_limit)) {
            $stmt = $dbh->prepare("${sql} ORDER BY products." . $arr['sort'] . " " . $arr['direction']);
        } else {
            $stmt = $dbh->prepare("${sql} ORDER BY products." . $arr['sort'] . " " . $arr['direction'] . " " . $sql_limit[0]);
        }

        $execute = ['min_price' => $arr['min_price'], 'max_price' => $arr['max_price']];
    }
    //если передали id секции, добавляем ее в подготовленный запрос
    if (isset($arr['id'])) {
        $execute['id'] = $arr['id'];
    }
    $stmt->execute($execute);
    //и возвращаем ассоциативный массив
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
//количество товара
function getCountProducts($dbh)
{
    $stmt = $dbh->query("SELECT COUNT(*) FROM products WHERE products.is_active = 1");
    return $stmt->fetch(PDO::FETCH_COLUMN);
}

//получаем минимальную и максимальную цену товаров
function getPrice($dbh)
{
    $stmt = $dbh->query("SELECT MIN(price) AS min_price, MAX(price) AS max_price FROM products");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * возвращает количество страниц с товарами
 * @param int $countProducts - количество товаров
 * @param int $countProductsPage - количество товаров на странице
 */
function numberPages($countProducts, $countProductsPage): int
{
    //если продуктов меньше количества товаров на странице, выходим из функции
    if ($countProducts < $countProductsPage) {
        return FALSE;
    }
    //номер страницы
    $numberPages = (int)($countProducts / $countProductsPage);
    //если есть остаток, увеличиваем на единицу страницу
    if (($countProducts % $countProductsPage) != 0) {
        $numberPages++;
    }

    return $numberPages;
}
/**
 * добавляет новый заказ в базу данных
 * @param [type] $dbh - подключение к БД
 * @param array $data - массив с полями данных
 */
function addOrder($dbh, $data)
{
    $sql = "INSERT INTO orders (full_name, phone, email, delivery, pay, full_address, comment, add_price, product_id)
    VALUES (:name, :phone, :email, :delivery, :pay, :address, :comment, :add_price, :product_id)";
    $stmt = $dbh->prepare($sql);
    return $stmt->execute($data);
}

//проверяем авторизован пользователь или нет, возвращает булевое значение
function isAuth(): bool
{
    if (isset($_SESSION['auth'])) {
        return true;
    } else {
        return false;
    }
}
/**
 * возвращает пароль в виде строки с хэшом пароля, либо пустую строку, если пользователя нет в БД
 * @param [type] $dbh - подключение к БД
 * @param string $login - логин(email) пользователя
 */
function getPassword($dbh, $login): string
{
    $stmt = $dbh->prepare("SELECT password FROM users WHERE email = :email");
    $stmt->execute(['email' => $login]);
    //возвращаем пароль в виде хэша
    return $stmt->fetchColumn();
}

//подготовленный запрос для получения id пользователя по логину, возвращает id
function getUserId($dbh, $login): string
{
    $stmt = $dbh->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $login]);
    //отправляем информацию о пользователе из БД
    return $stmt->fetchColumn();
}

//возвращает группу пользователя по id
function getGroup($dbh, $group, $idUser): string
{
    $stmt = $dbh->prepare("SELECT groups.name FROM groups INNER JOIN users ON users.group_id = groups.id WHERE groups.name = :group_name AND users.id = :id_user");
    $stmt->execute(['group_name' => $group, 'id_user' => $idUser]);

    return $stmt->fetchColumn();
}
//Проверка на администратора
function isAdmin($string): bool
{
    if ($string == "Администратор") {
        return true;
    } else {
        return false;
    }
}
//возвразает массив со списком заказов
function getListOrders($dbh, $status): array
{
    $stmt = $dbh->prepare("SELECT orders.*, products.price FROM orders INNER JOIN products ON orders.product_id = products.id WHERE orders.status = :status ORDER BY orders.id DESC");
    $stmt->execute(['status' => $status]);
    return $stmt->fetchALL(PDO::FETCH_ASSOC);
}
//функция меняет статуc заказа
function changeStatus($dbh, $status, $statusId)
{
    $stmt = $dbh->prepare("UPDATE orders SET status = :status WHERE id = :id");
    $stmt->execute(['status' => $status, 'id' => $statusId]);
    return true;
}
//возвращает массив товаров с категорией
function getListProducts($dbh, $page, $countProductsPage): array
{
    //номер начальной позиции для выборки товаров
    $start = ($page - 1) * $countProductsPage;

    $stmt = $dbh->query("SELECT products.name, products.id, products.price, sections.name as section, products.is_new FROM products
    INNER JOIN product_section ON product_section.product_id = products.id
    INNER JOIN sections ON product_section.section_id = sections.id
    ORDER BY products.id ASC
    LIMIT ${start}, ${countProductsPage}");

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
//количество товаров в административном разделе
function getCountProductsAdmin($dbh)
{
    $stmt = $dbh->query("SELECT COUNT(*) FROM products
    INNER JOIN product_section ON product_section.product_id = products.id
    INNER JOIN sections ON product_section.section_id = sections.id");
    return $stmt->fetch(PDO::FETCH_COLUMN);
}
/**
 * добавляем товар в БД
 * @param [type] $dbh - подключение к БД
 * @param array $data - массив с данными нового товара
 */
function addProduct($dbh, $data): bool
{
    //вытаскиваем категории из массива данных
    $category = array_pop($data);
    //добавляем товар в таблицу товаров
    $stmtProduct = $dbh->prepare("INSERT INTO products (name, price, is_active, img, is_new, is_sale) VALUES (:fname, :fprice, :fis_active, :fimg, :fis_new, :fis_sale)");
    $stmtProduct->execute($data);
    //после получаем id последнего товара
    $last_id = $dbh->lastInsertId();

    //перебираем категории
    foreach ($category as $cat) {
        //и id товара добавляем в текущую секцию в таблицу секций с товарами
        $stmtSection = $dbh->prepare("INSERT INTO product_section (product_id, section_id) VALUES (:fproduct_id, :fsection_id)");
        $stmtSection->execute(
            [
                'fproduct_id' => $last_id,
                'fsection_id' => $cat,
            ]
        );
    }

    return true;
}
/**
 * изменяет товар в БД
 * @param [type] $dbh - подключение к БД
 * @param array $data - массив с новыми данными товара
 */
function changeProduct($dbh, $data)
{
    //получаем id товара из массива
    if (isset($data['fid'])) {
        $id = $data['fid'];
    }
    //вытаскиваем категории из массива данных
    $category = array_pop($data);
    //изменяем товар в таблице товаров
    $stmtProduct = $dbh->prepare("UPDATE products SET name = :fname, price = :fprice, is_active = :fis_active, img = :fimg, is_new = :fis_new, is_sale = :fis_sale WHERE id = :fid");
    $stmtProduct->execute($data);

    //получаем id секций к которым принадлежал товар
    $idSections = getSectionsProductId($dbh, $id);
    //переменная для обозначения изменения секции товара
    $changeSections = false;
    //сравниваем массив с секциями из БД с массивом секций из фронтенда
    $diff = array_diff($idSections, $category);
    //если есть разница изменяем перенную
    if (count($diff) > 0) {
        $changeSections = true;
    }
    //если категории поменяли, то удаляем старые и добавляем новые
    if ($changeSections) {
        deleteSectionsById($dbh, $id);
        foreach ($category as $cat) {
            addSectionsById($dbh, $id, $cat);
        }
    }

    return true;
}


//возвращаем конкретный товар по его id в виде массива
function getProduct($dbh, $id): array
{
    //получаем товар
    $stmtProduct = $dbh->prepare("SELECT name, price, is_active, img, is_new, is_sale FROM products WHERE id = :id");
    $stmtProduct->execute(['id' => $id]);

    $product = $stmtProduct->fetch(PDO::FETCH_ASSOC);
    //получаем его категории
    $stmtSections = $dbh->prepare("SELECT section_id FROM product_section WHERE product_id = :id");
    $stmtSections->execute(['id' => $id]);

    $sections = $stmtSections->fetchAll(PDO::FETCH_ASSOC);
    //помещаем в общий массив
    $product['sections'] = $sections;

    return $product;
}

//деактивируем товар
function inactiveProduct($dbh, $id)
{
    $stmt = $dbh->prepare("UPDATE products SET is_active = 0 WHERE id = :id");
    $stmt->execute(['id' => $id]);

    return true;
}
//удаление товара
function deleteProduct($dbh, $id)
{
    $stmt = $dbh->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute(['id' => $id]);

    return true;
}
