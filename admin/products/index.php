<?php
require $_SERVER["DOCUMENT_ROOT"] . '/templates/header.php';
//если пользователь не администратор, перенаправляем на главную страницу
if (!isAuth() || !isAdmin($groupAdmin)) {
    header("Location: /");
    die();
}
?>
<main class="page-products">
    <h1 class="h h--1">Товары</h1>
    <a class="page-products__button button" href="/admin/products/add.php">Добавить товар</a>
    <div class="page-products__header">
        <span class="page-products__header-field">Название товара</span>
        <span class="page-products__header-field">ID</span>
        <span class="page-products__header-field">Цена</span>
        <span class="page-products__header-field">Категория</span>
        <span class="page-products__header-field">Новинка</span>
    </div>
    <ul class="page-products__list">
        <?php foreach ($listProducts as $item) : ?>
            <li class="product-item page-products__item">
                <b class="product-item__name"><?=$item['name']?></b>
                <span class="product-item__field"><?=$item['id']?></span>
                <span class="product-item__field"><?=$item['price']?> руб.</span>
                <span class="product-item__field"><?=$item['section']?></span>
                <span class="product-item__field"><?=($item['is_new'] == 1) ? "Да" : "Нет"; ?></span>
                <a href="/admin/products/add.php?id=<?=$item['id']?>" class="product-item__edit" aria-label="Редактировать"></a>
                <button class="product-item__delete"></button>
            </li>
        <?php endforeach ?>
    </ul>
    <?php
        $paginator = numberPages($countProductsAdmin, COUNT_PRODUCTS_ADMIN);
        $page = 1;
        if ($paginator > 1):
    ?>
        <ul class="shop__paginator paginator-admin">
            <?php while ($page <= $paginator) : ?>
                <li>
                    <a class="paginator-admin__item<?=$page == 1 ? ' active' : ''?>" href="/admin/products/?page=<?=$page?>"><?=$page?></a>
                </li>
            <?php
                $page++;
                endwhile
            ?>
        </ul>
    <?php endif ?>
</main>
<?php
require $_SERVER["DOCUMENT_ROOT"] . '/templates/footer.php';
?>
