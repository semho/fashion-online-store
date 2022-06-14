<?php
require $_SERVER["DOCUMENT_ROOT"] . '/templates/header.php';
//если пользователь не авторизован
if (!isAuth()) {
    header("Location: /");
    die();
}
?>
<main class="page-order">
  <h1 class="h h--1">Список заказов</h1>
  <ul class="page-order__list">
    <?php foreach ($listUnprocessedOrders as $order):?>
        <?php include $_SERVER["DOCUMENT_ROOT"] . '/include/item_order.php'; ?>
    <?php endforeach ?>
    <?php foreach ($listProcessedOrders as $order):?>
        <?php include $_SERVER["DOCUMENT_ROOT"] . '/include/item_order.php'; ?>
    <?php endforeach ?>
  </ul>
</main>
<?php
require $_SERVER["DOCUMENT_ROOT"] . '/templates/footer.php';
?>
