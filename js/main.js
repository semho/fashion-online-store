'use strict';

//константа для папки с картинками
const PATH_IMG = '/img/products/';

$(document).ready(function() {
    //клик по ссылкам категорий
    $(".filter__list-item").click(function () {
            //отправляем через ajax
            sendAjaxForm('shop__list', $(this).attr("href"), '/handler/action_ajax_form.php');
            //удаляем активный класс у всех категорий
            $('.filter__list-item').removeClass('active');
            //добавляем активный класс только у текущей
            $(this).addClass('active');
            return false
        }
    );
    //клик по кнопке фильтра
    $(".filter .button").click(function () {
            sendAjaxForm('shop__list', $('.filter__list-item.active').attr("href"), '/handler/action_ajax_form.php');
            return false
        }
    );
    //клик по пагинации
    $('.paginator').on('click', '.paginator__item', handlerPaginator);
    //клик по пунктам меню
    $('.main-menu').on('click', '.main-menu__item', handlerMainMenu);

    //при передаче параметра ссылки меню
    if (window.location.search.substr(1) == 'new' || window.location.search.substr(1) == 'sale') {
        //вызывываем обработчик меню
        handlerMainMenu(window.location.search.substr(1));
    }

    //клик по кнопке изменения статуса заказа на странице списка заказов
    $('.order-item__group--status').on('click', '.order-item__btn', handlerOrders);

    //клик по пагинации в административном разделе
    $('.paginator-admin').on('click', '.paginator-admin__item', handlerPaginatorAdmin);

     //клик по кнопке добавления товара
     $('.page-add').on('click', '.button', handlerAddProduct);

});
//деактивируем товар
function inactiveProduct() {
    const id = event.target.parentElement.querySelector('.product-item__field').textContent;

    $.ajax({
        url: '/handler/ajax_inactive_product.php',
        method: 'post', //метод передачи
        data: {id: id},
        dataType: 'json', //тип данных
        success: function(data) { // получаем ответ от сервера
            if (data.error) {
                showError(data.error);
            }
        },
        error: function(response) { // Данные не отправлены
            showError('Ошибка. Данные не отправлены.');
        }
    });

}

//функция добавляет товар в БД
function handlerAddProduct() {
    digitalChecked('#new');
    digitalChecked('#sale');
    digitalChecked('#active');

    const form = document.querySelector('.custom-form');
    const popupEnd = document.querySelector('.page-add__popup-end');
    const data = new FormData(form);
    const url = new URL(window.location.href);
    const idProd = url.searchParams.get('id');
    //если есть id товара, добавляем в data
    if (idProd != null) {
        data.append('id', idProd);
        //если картинка у товара уже есть и мы ее не меняем
        const checkingPreloadedImg = document.querySelector('.add-list-change');
        if (checkingPreloadedImg) {
            const parentImg = document.querySelector('.add-list__item--active');
            const img = parentImg.querySelector('img');
            const nameImg = img.src.replace(/^.*[\\/]/, '');
            //отправляем ее название
            data.append('preload', nameImg);
        }
    }


    $.ajax({
        type: "POST",
        enctype: 'multipart/form-data',
        url: '/handler/ajax_form_add_product.php',
        data: data,
        processData: false,
        contentType: false,
        cache: false,
        timeout: 600000,
        success: function (data) {
            console.log(data);
            const result = JSON.parse(data);
             //если есть данные с ошибкой, выводим их
            if (result.error) {
                showError(result.error);
            //иначе добавляем товар, скрываем форму добавление товара,
            } else if (result.success == true) {
                form.hidden = true;
                popupEnd.hidden = false;
            //иначе возвращаем ошибку соединения с БД
            } else {
                showError('Ошибка. Данные не отправлены.');
            }
        },
        error: function (response) {
            $(form).html('Ошибка. Данные не отправлены.');
        }
    });

    return false;
}

//функция обрабатывает нажатие на страницы пагинации в административном разделе
function handlerPaginatorAdmin() {
    //удаляем активный класс у всех
    $('.paginator-admin__item').removeClass('active');
    //добавляем активный класс только текущей странице
    $(this).addClass('active');
    const numberPage = $(this).text();
    const list = document.querySelector('.page-products__list');


    $.ajax({
        url: '/handler/ajax_paginator_admin.php',
        method: 'post', //метод передачи
        data: {page: numberPage},
        dataType: 'json', //тип данных
        success: function(data) { // получаем ответ от сервера
            $(list).empty();
            data.forEach(el => {
                $('.page-products__list').append(createItemProduct(el.name, el.id, el.price, el.section, el.is_new)); //помещаем новые элементы возвращенные от сервера
            })
        },
        error: function(response) { // Данные не отправлены
            $(list).html('Ошибка. Данные не отправлены.');
        }
    });

    setLocation('?page=' + $(this).text());

    return false;
}
//функция создает новый элемент списка товаров в административном разделе
function createItemProduct(name, id, price, section, is_new) {
    const li = document.createElement('li');
    li.classList.add(...['product-item', 'page-products__item']);

    const b = document.createElement('b');
    b.classList.add('product-item__name')
    b.textContent = name;

    const idSpan = document.createElement('span');
    idSpan.classList.add('product-item__field');
    idSpan.textContent = id;

    const priceSpan = document.createElement('span');
    priceSpan.classList.add('product-item__field');
    priceSpan.textContent = price;

    const sectionSpan = document.createElement('span');
    sectionSpan.classList.add('product-item__field');
    sectionSpan.textContent = section;

    const newSpan = document.createElement('span');
    newSpan.classList.add('product-item__field');
    if (is_new == 1) {
        newSpan.textContent = 'Да';
    } else {
        newSpan.textContent = 'Нет';
    }

    const link = document.createElement('a');
    link.classList.add('product-item__edit');
    link.href = "/admin/products/add.php?id=" + id;
    link.ariaLabel = "Редактировать";

    const btn = document.createElement('button');
    btn.classList.add('product-item__delete');

    li.append(b, idSpan, priceSpan, sectionSpan, newSpan, link, btn);

    return li;
}
//обрабатываем нажатие на кнопку статуса заказа
function handlerOrders() {
    //получаем текущий статус
    const status = $(this).prev().text();
    let statusDC = '';
    //и меняем его на противоположный
    if (status == 'Выполнено') {
        statusDC = 0;
    } else {
        statusDC = 1;
    }
    const id = $(this).parent().parent().parent().find('.order-item__info--id').text();

    //с помощью ajax отправим в БД изменения
    $.ajax({
        url: '/handler/ajax_status_order.php',
        method: 'post', //метод передачи
        data: {
            status: statusDC,
            id: id,
        },
        dataType: 'json', //тип данных
    });
}
//функция обрабатывает нажатия на пункты меню
function handlerMainMenu(search) {

    //инициализируем адрес ссылки
    let href = null;
    //если в параметре что-то передаем
    if (search.length > 0) {
        href = search;
    //иначе берем адрес ссылки из меню
    } else {
        href = $(this).attr('href').substr(2);
    }

    //если в этот момент мы находимся в оформлении заказа, то переходим на главную, заказ скрываем
    if ($('.shop-page__order').is(':visible') || $('.shop-page__popup-end').is(':visible')) {
        document.querySelector('.shop-page__order').hidden = true;
        document.querySelector('.shop-page__popup-end').hidden = true;
        document.querySelector('.shop container').hidden = false;

        // document.location.href = "/?" + href;
        setLocation("/?" + href + '=true');
    }
    //удаляем все активные чекбоксы
    $('.custom-form__checkbox').prop('checked', false);

    //если константа это пункт меню новости или распродажа
    if (href == 'new' || href == 'sale') {
        //удаляем активный класс у всех пунктов меню
        $('.main-menu__item').removeClass('active');
        //добавляем активный класс только текущему
        $(this).addClass('active');
        //делаем активным соответсвующий чекбокс
        $('#' + href).prop("checked", true);

        //и получаем список товаров соответствующий выбраному чекбоксу
        sendAjaxForm('shop__list', $('.filter__list-item.active').attr("href"), '/handler/action_ajax_form.php', $(this), href);
        //делаем плавный скрол к товарам
        const content = $('.shop');
        const destination = $(content).offset().top;
        $('html, body').animate({ scrollTop: destination }, 600);

        return false;
    }
}
//функция обрабатывает нажатие на страницы пагинации
function handlerPaginator() {
    //удаляем активный класс у всех
    $('.paginator__item').removeClass('active');
    //добавляем активный класс только текущей странице
    $(this).addClass('active');

    setLocation('?page=' + $(this).text());

    sendAjaxForm('shop__list', $('.filter__list-item.active').attr("href"), '/handler/action_ajax_form.php', $(this));

    return false
}

function setLocation(curLoc) {
    // location.href = curLoc;
    // location.hash = curLoc;
    history.pushState(null, null, curLoc);
}


/** функция отправки через ajax на сервер
 * @params $list - класс селектора куда вернется ответ от сервера, вернем массив товаров
 * @params $sectionId - передаем id категории товара
 * @params $url - файл на сервере, где обрабатываются переданные данные
 * @params $pageActive - передаем активный элемент текущей страницы
 * @params $href - передаем url
 */
function sendAjaxForm(list, sectionId, url, pageActive = 1, href = null) {

    if (sectionId == undefined) {
        sectionId = '/';
    }
    const id = sectionId.substr(sectionId.length - 1); //получаем id
    const minPrice = $('.min-price').text().replace(/\D/g, ""); // получаем только цифры
    const maxPrice = $('.max-price').text().replace(/\D/g, ""); // получаем только цифры
    //флаг для понимая нахождения строки и переменная для ссылки
    let isString = false;
    let attr = '';
    if (pageActive.length > 0) {
        //если не число
        if (isNaN(pageActive.text().replace(/\s/g, ""))) {
            isString = true;
            attr = pageActive.attr('href');
            pageActive = 1;
        //иначе активная страница
        } else {
            pageActive = pageActive.text().replace(/\s/g, "");
        }
    }
    //если передаем путь через параметр
    if (href != null) {
        pageActive = 1;
        attr = '?' + href;
        isString = true;
    }
    //массив с данными для строки url
    const arrayUrl = [
        id,
        $('#new').is(":checked"),
        $('#sale').is(":checked"),
        minPrice,
        maxPrice,
        document.querySelector('select[name=category]').value,
        document.querySelector('select[name=prices]').value
    ];
    let sortValue = '';
    let directionValue = '';
    if (document.querySelector('select[name=category]').value == 'Сортировка') {
        sortValue = 'id';
    } else {
        sortValue = document.querySelector('select[name=category]').value;
    }

    if (document.querySelector('select[name=prices]').value == 'Порядок') {
        directionValue = 'ASC';
    } else {
        directionValue = document.querySelector('select[name=prices]').value;
    }

    $.ajax({
        url: url,
        method: 'post', //метод передачи
        data: { //формируем объект данных в ручную, т.к. метод serialize() обрабатывает только данные формы
            categoryId: id,
            new: isChecked("#new"),
            sale: isChecked("#sale"),
            min: minPrice,
            max: maxPrice,
            page: pageActive,
            sort: sortValue,
            direction: directionValue,
        },
        dataType: 'json', //тип данных
        success: function(data){ // получаем ответ от сервера
            const count = data.pop(); //вытаскиваем последний объект из массива с количеством элементов в данной секции и количеством элементов на странице
            const numberPaginator = numberPages(count.countSection, count.countPage); //возвращает количество страниц для пагинации или false;

            //контейнер моделей
            const wrapModels = document.querySelector('.shop__sorting-res');
            createModels(wrapModels, count.countSection);

            //контейнер пагинации
            const wrapPaginator = document.querySelector('.paginator');
            if (wrapPaginator != null) {
                //убираем все дочерние элементы
                wrapPaginator.innerHTML = '';
            }
            //если есть страницы
            if (numberPaginator) {
                //создаем дочерние элементы
                for (let index = 1; index <= numberPaginator; index++) {
                    wrapPaginator.append(createElementPaginator(index));
                }
                //текущая активная страница пагинации
                const currentPage = document.querySelector(`.paginator__item[href="/?page=${pageActive}"]`);
                //делаем текущую страницу активной
                if (currentPage) {
                    currentPage.classList.add('active');
                    //если строка в параметрах url вместо активного номера страницы пагинации
                    if (isString) {
                        setLocation(attr + '=true');
                    } else {
                        arrayUrl.unshift(currentPage.textContent);
                        formationUrl(arrayUrl);
                    }
                }
            } else {
                //если строка в параметрах url вместо активного номера страницы пагинации
                if (isString) {
                    setLocation(attr + '=true');
                } else {
                    arrayUrl.unshift(1);
                    formationUrl(arrayUrl);
                }
            }
            //контейнер для карточек
            const box = document.querySelector('.' + list);
            box.innerHTML = ''; //каждый раз его очищаем от прежних дочерних элементов
            data.forEach(element => {
                box.append(createCard(element.id, element.name, element.img, element.price)); //помещаем новые элементы возвращенные от сервера
            })
        },
        error: function(response) { // Данные не отправлены
            $('.' + list).html('Ошибка. Данные не отправлены.');
        }
    });
}

function isChecked(selector) {
    if ( $(selector).is(":checked")) {
        return 1;
    }

    return 0;
}

function digitalChecked(selector) {
    if ($(selector).is(":checked")) {
        $(selector).val("1");
    } else {
        $(selector).val("0");
    }
}

function formationUrl(array) {
    if (array[6] == 'Сортировка') {
        array.splice(6, 1, 'id')
    }

    if (array[7] == 'Порядок') {
        array.splice(7, 1, 'asc')
    }

    setLocation(
        '?page=' + array[0] +
        '&idCategory=' + array[1] +
        '&new=' + array[2] +
        '&sale=' + array[3] +
        '&min=' + array[4] +
        '&max=' + array[5] +
        '&sort=' + array[6] +
        '&direction=' + array[7]
    );
}

/**
 * Создаем карточку товара
 * @param string name - передаем название товара
 * @param string src  - путь к картинке
 * @param string prc  - цена товара
 * @returns
 */
function createCard(id, name, src, prc) {
    const article = document.createElement('article');
    article.classList.add(...['shop__item', 'product']);

    const boxImg = document.createElement('div');
    boxImg.classList.add('product__image');

    const img = document.createElement('img');
    img.src = PATH_IMG + src;
    img.alt = name;
    boxImg.append(img);

    const title = document.createElement('p');
    title.classList.add('product__name');
    title.textContent = name;

    const price = document.createElement('span');
    price.classList.add('product__price');
    price.textContent = prc + ' руб.';

    const hidden = document.createElement('span');
    hidden.hidden = true
    hidden.classList.add('product__id');
    hidden.textContent = id;

    article.append(boxImg, title, price, hidden);

    return article;
}

//возвращает количество страниц для пагинации или false
function numberPages(countProducts, countProductsPage) {
    //если продуктов меньше количества товаров на странице, выходим из функции
    if (countProducts < countProductsPage) {
        return false;
    }
    //номер страницы
    let numberPages = (countProducts / countProductsPage);
    //если есть остаток, увеличиваем на единицу страницу
    if ((countProducts % countProductsPage) != 0) {
        numberPages++;
    }
    return Math.floor(numberPages);
}
//создает один элемент для пагинации
function createElementPaginator(name) {
    const li = document.createElement('li');
    const a = document.createElement('a');
    a.classList.add('paginator__item');
    a.textContent = name;
    a.href = '/?page=' + name;
    li.append(a);

    return li;
}
//отображает количество моделей в данной секции
function createModels(wrap, number) {
    wrap.innerHTML = '';
    wrap.innerHTML = "Найдено <span class='res-sort'>" + number + "</span> " + wordEnding(number);
}
//функция возвращает верное окончание числа моделей
function wordEnding(num) {
    if (num % 100 >= 10 && num % 100 <= 20) {
        return "моделей";
    } else if (num % 10 == 1) {
        return "модель";
    } else if (num % 10 >= 2 && num % 10 <= 4 ) {
        return "модели";
    } else {
        return "моделей";
    }
}
//функция возвращает ответ от сервера. Если все ок, заказ оформлен
function sendToServer(form, currentProdId, currentProdPrice) {
    $.ajax({
        url: '/handler/ajax_form_order.php',
        method: 'post', //метод передачи
        data: $(form).serialize() + '&currentProdId=' + currentProdId + '&currentProdPrice=' + currentProdPrice, //сериализуем форму
        dataType: 'json', //тип данных
        success: function(data){ // получаем ответ от сервера
            //если есть данные с ошибкой, выводим их
            if (data.error) {
                showError(data.error);
            //иначе оформляем заказ
            } else if (data.success == true) {
                orderComplete();
            //иначе возвращаем ошибку соединения с БД
            } else {
                showError('Ошибка. Данные не отправлены.');
            }
        },
        error: function(response) { // Данные не отправлены
            $(form).html('Ошибка. Данные не отправлены.');
        }
    });

}
//функция показывает ошибку, а затем удаляет ее
function showError(error) {
    let message = '';
    if (error == 'personal') {
        message = 'Заполните личные данные!';
    } else if (error == 'delivery') {
        message = 'Заполните информацию о доставке для курьера!'
    } else {
        message = error;
    }
    const elem = createError(message)
    document.body.append(elem);
    setTimeout(function () {
        elem.remove();
    }, 2000)
}
//функция создает блок с новой ошибкой
function createError(string) {
    if (document.querySelector('popup-wrap')) {
        document.querySelector('popup-wrap').remove;
    }
    const wrap = document.createElement('div');
    wrap.classList.add('popup-wrap');
    const block = document.createElement('div');
    block.classList.add('popup-error');
    const h3 = document.createElement('h3');
    h3.textContent = string;
    block.append(h3);
    wrap.append(block);

    return wrap;
}
//функция закрывает окно с оформлением заказа. Скрипт взят из scripts.js
function orderComplete() {

    const shopOrder = document.querySelector('.shop-page__order');
    const popupEnd = document.querySelector('.shop-page__popup-end');

    toggleHidden(shopOrder, popupEnd);

    popupEnd.classList.add('fade');
    setTimeout(() => popupEnd.classList.remove('fade'), 1000);

    window.scroll(0, 0);
    const buttonEnd = popupEnd.querySelector('.button');

    buttonEnd.addEventListener('click', () => {
        popupEnd.classList.add('fade-reverse');
        setTimeout(() => {
            popupEnd.classList.remove('fade-reverse');
            toggleHidden(popupEnd, document.querySelector('.intro'), document.querySelector('.shop'));

            document.location.href = "/";
        }, 1000);
    });
}

