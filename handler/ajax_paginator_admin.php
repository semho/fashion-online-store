<?php
//текучая страница
$pageAdmin = (int)strip_tags($_POST['page']);

require $_SERVER["DOCUMENT_ROOT"] . '/src/core.php';

//возвращаем товары в виде json
echo json_encode($listProductsAjax);
