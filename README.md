Simple shop cart
================

Корзина для интернет-магазина в качестве самостоятельного REST API сервиса

Установка
---------

####Требования:

* [Vagrant](https://www.vagrantup.com/downloads.html) - инструмент для создания полноценной среды разработки.
* [Virtualbox](https://www.virtualbox.org/wiki/Downloads) - инструмент для разворачивания виртуальных машин

####Получение кода:

#####Composer:

Выполнить команду `composer create-project --prefer-dist phantom-d/shop-cart`

#####GitHub:

Клонирование репозитория: `git clone https://github.com/phantom-d/shop-cart.git`

#####Разворачивание среды разработки

* Переходите в директорию проекта: `cd ./var/www/shop-cart`
* Запуск среды разработки: `vagrant up`

> Note: Для работы будет установлена операционная система CentOS 7.2 и программное обеспечение: NGINX, PHP 5.6, GIT, COMPOSER

> Note: Для доступа к сайту необходимо прописать в `hosts`: `192.168.33.10 www.shop-cart.local shop-cart.local`

####Поддерживаемые комманды:

* `GET http://www.shop-cart.local/api/products/` - получение списка товаров
* `GET http://www.shop-cart.local/api/cart/` - получение текущей корзины
* `POST http://www.shop-cart.local/api/cart/` - добавление товара в корзину, принимает параметры:

```php

$_POST = [
    'product_id' => 1, // Тип `integer`, больше 0
    'quantity'   => 1, // Тип `integer`, в диапазоне от 1 до 10
];

```

* `DELETE http://www.shop-cart.local/api/cart/1/` - удаление товара из корзины в количестве 1 шт.
* `OPTIONS http://www.shop-cart.local/api/cart/` - получение списка поддерживаемых HTTP методов для корзины
* `OPTIONS http://www.shop-cart.local/api/products/` - получение списка поддерживаемых HTTP методов для товаров

В рамках поставленной задачи:

* все данные возвращаются в формате `JSON`.
* срок жизни корзины установлен на 5 минут.

Тестирование
------------

Тестирование производится с помощью фреймворка `Codeception`

####Настройка

* Переходите в директорию проекта: `cd /var/www/shop-cart`
* Выполните обновление пакетов composer `composer update`
* Переходите в директорию тестирования: `cd /var/www/shop-cart/tests`
* Выполните запуск тестов `codeception run`