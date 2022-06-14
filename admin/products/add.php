<?php
require $_SERVER["DOCUMENT_ROOT"] . '/templates/header.php';
//если пользователь не администратор, перенаправляем на главную страницу
if (!isAuth() || !isAdmin($groupAdmin)) {
    header("Location: /");
    die();
}
//помещаем id секций в отдельный массив
if (isset($product)) {
    $select = [];
    foreach ($product['sections'] as $prod) {
        $select[] = $prod['section_id'];
    }
    //преобразовываем данные чекбоксов к булевому типу
    $new = (bool) $product['is_new'] ?? false;
    $sale = (bool) $product['is_sale'] ?? false;
    $active = (bool) $product['is_active'] ?? false;
}

?>
<main class="page-add">
  <h1 class="h h--1">Добавление товара</h1>
  <form class="custom-form" action="#" method="post">
    <fieldset class="page-add__group custom-form__group">
      <legend class="page-add__small-title custom-form__title">Данные о товаре</legend>
      <label for="product-name" class="custom-form__input-wrapper page-add__first-wrapper">
        <input type="text" class="custom-form__input" name="product-name" id="product-name" value="<?=(isset($product['name'])) ? $product['name'] : ''; ?>">
        <?php if (!isset($product)) :?>
            <p class="custom-form__input-label">
            Название товара
            </p>
        <?php endif ?>
      </label>
      <label for="product-price" class="custom-form__input-wrapper">
        <input type="text" class="custom-form__input" name="product-price" id="product-price" value="<?=(isset($product['price'])) ? $product['price'] : ''; ?>">
        <?php if (!isset($product)) :?>
            <p class="custom-form__input-label">
            Цена товара
            </p>
        <?php endif ?>
      </label>
    </fieldset>
    <fieldset class="page-add__group custom-form__group">
      <legend class="page-add__small-title custom-form__title">Фотография товара</legend>
      <ul class="add-list <?=(isset($product)) ? 'add-list-change' : ''; ?>">
        <li class="add-list__item add-list__item--add" >
          <input type="file" name="product-photo" id="product-photo" hidden="">
          <label for="product-photo">Добавить фотографию</label>
        </li>
        <?php if (isset($product)) :?>
            <li class="add-list__item add-list__item--active">
                <img src="/img/products/<?=$product['img']?>" alt="">
            </li>
        <?php endif ?>
      </ul>
    </fieldset>
    <fieldset class="page-add__group custom-form__group">
      <legend class="page-add__small-title custom-form__title">Раздел</legend>
      <div class="page-add__select">
        <select name="category[]" class="custom-form__select" multiple="multiple">
          <option hidden="">Название раздела</option>
          <option <?php if (isset($select)) { if (in_array('1', $select)) { ?> selected <?php } } ?> value="female" >Женщины</option>
          <option <?php if (isset($select)) { if (in_array('2', $select)) { ?> selected <?php } } ?> value="male" >Мужчины</option>
          <option <?php if (isset($select)) { if (in_array('3', $select)) { ?> selected <?php } } ?> value="children" >Дети</option>
          <option <?php if (isset($select)) { if (in_array('4', $select)) { ?> selected <?php } } ?> value="access" >Аксессуары</option>
        </select>
      </div>
      <input type="checkbox" name="new" id="new" class="custom-form__checkbox"  <?php if (isset($new) && $new == true) {?> checked <?php } ?>  >
      <label for="new" class="custom-form__checkbox-label">Новинка</label>
      <input type="checkbox" name="sale" id="sale" class="custom-form__checkbox" <?php if (isset($sale) && $sale == true) {?> checked <?php } ?> >
      <label for="sale" class="custom-form__checkbox-label">Распродажа</label>
      <input type="checkbox" name="active" id="active" class="custom-form__checkbox" <?php if (isset($active) && $active == true) {?> checked <?php } ?> >
      <label for="active" class="custom-form__checkbox-label">Активность</label>
    </fieldset>
    <button class="button" type="submit">Добавить товар</button>
  </form>
  <section class="shop-page__popup-end page-add__popup-end" hidden="">
    <div class="shop-page__wrapper shop-page__wrapper--popup-end">
        <h2 class="h h--1 h--icon shop-page__end-title">Товар успешно добавлен</h2>
        <a href="/admin/products/">К списку товаров</a>
    </div>
  </section>
</main>
<?php
require $_SERVER["DOCUMENT_ROOT"] . '/templates/footer.php';
?>
