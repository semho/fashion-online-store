<?php
if (empty($_POST['id']) || !isset($_POST['id'])) {
    $result['error'] = 'Не найден id товара.';
}

if (empty($result['error'])) {
    $deleteId = (int)($_POST['id']);

    require $_SERVER["DOCUMENT_ROOT"] . '/src/core.php';

    $result['success'] = $inactiveProd;
}

echo json_encode($result);
