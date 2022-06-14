<li class="order-item page-order__item">
    <div class="order-item__wrapper">
        <div class="order-item__group order-item__group--id">
            <span class="order-item__title">Номер заказа</span>
            <span class="order-item__info order-item__info--id"><?=htmlspecialchars($order['id'])?></span>
        </div>
        <div class="order-item__group">
            <span class="order-item__title">Сумма заказа</span>
            <?php echo htmlspecialchars($order['price']) + htmlspecialchars($order['add_price'])?>
        </div>
        <button class="order-item__toggle"></button>
    </div>
    <div class="order-item__wrapper">
        <div class="order-item__group order-item__group--margin">
        <span class="order-item__title">Заказчик</span>
        <span class="order-item__info"><?=htmlspecialchars($order['full_name'])?></span>
        </div>
        <div class="order-item__group">
        <span class="order-item__title">Номер телефона</span>
        <span class="order-item__info"><?=htmlspecialchars($order['phone'])?></span>
        </div>
        <div class="order-item__group">
        <span class="order-item__title">Способ доставки</span>
        <span class="order-item__info"><?=($order['delivery'] == 'dev-yes') ? "Курьером" : "Самовывоз"; ?></span>
        </div>
        <div class="order-item__group">
        <span class="order-item__title">Способ оплаты</span>
        <span class="order-item__info"><?=($order['pay'] == 'cash') ? "Наличными" : "Картой"; ?></span>
        </div>
        <div class="order-item__group order-item__group--status">
        <span class="order-item__title">Статус заказа</span>
        <span class="order-item__info <?=($order['status'] == '0') ? 'order-item__info--no' : 'order-item__info--yes'?>"><?=($order['status'] == '0') ? "Не выполнено" : "Выполнено"; ?></span>
        <button class="order-item__btn">Изменить</button>
        </div>
    </div>
    <?php if (!empty($order['full_address'])): ?>
        <div class="order-item__wrapper">
            <div class="order-item__group">
            <span class="order-item__title">Адрес доставки</span>
            <span class="order-item__info"><?=htmlspecialchars($order['full_address'])?></span>
            </div>
        </div>
    <?php endif ?>
    <?php if (!empty($order['comment'])): ?>
        <div class="order-item__wrapper">
            <div class="order-item__group">
            <span class="order-item__title">Комментарий к заказу</span>
            <span class="order-item__info"><?=htmlspecialchars($order['comment'])?></span>
            </div>
        </div>
    <?php endif ?>
</li>
