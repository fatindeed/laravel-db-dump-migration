# Laravel DB Dump Migration

[![PHP Version][ico-php-v]](#)
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](#)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-packagist]

<!-- This library is a _pure PHP_ implementation of the [AMQP 0-9-1 protocol](http://www.rabbitmq.com/tutorials/amqp-concepts.html).
It's been tested against [RabbitMQ](http://www.rabbitmq.com/). -->

## Guide

1.  Install with composer

    ```sh
    composer require --dev fatindeed/laravel-db-dump-migration
    ```

1.  Add command to your `app/Console/Kernel.php`

    ```php
    protected $commands = [
        \Fatindeed\LaravelDbDumpMigration\DbDumpCommand::class
    ];
    ```

2.  Run command

    ```php
    php artisan db:dump-migration my_table_name
    ```

    ```php
    php artisan db:dump-migration my_table_name --database=mysql
    ```

[ico-php-v]: https://img.shields.io/packagist/php-v/fatindeed/laravel-db-dump-migration.svg
[ico-version]: https://img.shields.io/packagist/v/fatindeed/laravel-db-dump-migration.svg
[ico-license]: https://img.shields.io/packagist/l/fatindeed/laravel-db-dump-migration.svg
[ico-travis]: https://img.shields.io/travis/fatindeed/laravel-db-dump-migration/master.svg
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/fatindeed/laravel-db-dump-migration.svg
[ico-code-quality]: https://img.shields.io/scrutinizer/g/fatindeed/laravel-db-dump-migration.svg
[ico-downloads]: https://img.shields.io/packagist/dm/fatindeed/laravel-db-dump-migration.svg

[link-packagist]: https://packagist.org/packages/fatindeed/laravel-db-dump-migration
[link-travis]: https://travis-ci.org/fatindeed/laravel-db-dump-migration
[link-scrutinizer]: https://scrutinizer-ci.com/g/fatindeed/laravel-db-dump-migration/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/fatindeed/laravel-db-dump-migration
[link-author]: https://github.com/fatindeed