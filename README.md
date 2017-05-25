# vps-tools

[![Build Status](https://img.shields.io/travis/pulsarvp/vps-tools.svg)](http://travis-ci.org/pulsarvp/vps-tools)
[![Dependency Status](https://www.versioneye.com/user/projects/592623c025feb90062a6aae8/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/592623c025feb90062a6aae8)

Набор инструментов для работы сервисов PulsarVP.

## Для запуска тестов
* Из корня `composer install`.
* Создать базу для тестов и пользователя к ней. В директории `tests/config` скопировать файл `db.default.php` → `db.php` и прописать туда настройки с тестовой базой и пользователем.
* Импортивать в созданную базу sql-файлы из папки `tests/migrations`.
* Создать папку `tests/data` и прописать ей полные права на запись: `mkdir data`, `chmod 0777 data`.

### Запуск тестов из папки tests
Всех тестов:
`../vendor/phpunit/phpunit/phpunit`

Набора тестов:
`../vendor/phpunit/phpunit/phpunit helpers`

Одного файла с тестами:
`../vendor/phpunit/phpunit/phpunit helpers/StringHelperTest.php`

Одного теста:
`../vendor/phpunit/phpunit/phpunit --filter testExplode helpers/StringHelperTest.php`

Здесь используется бинарник из vendor-а, можно чуть облегчить себе жизнь и поставить `phpunit` сразу как системный пакет, тогда запускать просто как `phpunit helpers/StringHelperTest.php`.
