GraphQL (GQL) query generator
=======================

Библиотека для работы с GraphQL запросами. Позволяет удобно составлять 
структуру запросов, рефракторить ее, или собирать постепенно в
зависимости от условий. Данная библиотека будет улучшаться,
данная версия работает с массивами. В дальнейшем, планируется доработать,
ее для работы с объектами.


Установка
======================
`composer require dastanaron/gql-query-generator-php "@dev"`

Используйте в своем php файле, если вы используете autoload.php из composer

```php
use dastanaron\GraphQL\GQLQueryGenerator;
```

Примеры работы
-----------------------

### Конструктор класса принимает три параметра:

`$base` - указатель базы запроса, с нее обычно начинается запрос GraphQL,

```graphql
{USER(/*filters*/) {/*select*/}}
```

Где User - это и есть указатель `$base`, а `filter` и `select` - это 
соответственно параметры для фильтрации и выборки.

`$filter` - это массив фильтров, где ключом является сама строка выборки,
а значением - его значение. Проще на примере:

```php
$filter = [
	'lang' => 'ru',
	'foo' => 'bar',
	'limit' => 10
];
```

Такой пример сформирует такой запрос:

```graphql
{Users(lang: "ru", foo: "bar", limit: 10){/*select*/}}
```
Обратите внимание, что если вы передаете число, система не оборачивает его в кавычки.
Это зависит от вашей **GraphQL** базы. Иногда нужно передавать и булевы 
значения и числа, а php их может преобразовывать. Данная библиотека этого
не делает. Какого типа данные будут переданы, такого типа данные и сформируются 
в запросе. Будьте внимательны. Если нужно передать число как строку, то передавайте его строкой,
т.е. завернутой в кавычки при передачи значения ключа массива

`$select` - это массив выборок. Сразу к примеру:

```php
$select = [
	'name',
	'age',
	'documents' =>
		[
			'passport',
			'driver_license',
			'other',
			'photo(preset: "55x55")'
		]
];
```

Данный пример сформирует такую строку запроса:

```graphql
{/*Base*/(/*filter*/){name age documents{passport driver_license other photo(preset: "55x55")}}}
```

Пример:
----------------------

```php
<?php
require_once (__DIR__. './vendor/autoload.php');

use dastanaron\GraphQL\GQLQueryGenerator;

$filter = [
    'lang'  => 'ru',
    'foo'   => 'bar',
    'limit' => 10,
];

$select = [
    'name',
    'age',
    'documents' =>
        [
            'passport' => [
                'number',
                'serial',
            ],
            'driver_license' => [
                'number',
            ],
            'other',
            'photo(preset: "55x55")',
        ],
];



$graphQl = new GQLQueryGenerator('user', $filter, $select);

echo $graphQl.PHP_EOL;


echo($graphQl); //Выведет сформированный запрос

//Если нужно вернуть запрос, есть метод:
$query = $graphQl->getQuery();

```

`echo` выведет: 

```graphql
{user(lang: "ru", foo: "bar", limit: 10){name age documents {passport {number serial } driver_license {number } other photo(preset: "55x55") }}}
```

Вы так же можете посмотреть примеры в тестах,
для запуска тестов нужно сделать следующее
```bash
composer install
php ./vendor/phpunit/phpunit/phpunit
```

Примерный ответ тестов:
```
 ./vendor/phpunit/phpunit/phpunit
PHPUnit 8.5.2 by Sebastian Bergmann and contributors.

..                                                                  2 / 2 (100%)

Time: 21 ms, Memory: 4.00 MB

OK (2 tests, 2 assertions)
```
