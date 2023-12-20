<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Description

This API is a base template developed using Laravel, designed to provide a robust starting point for modern web applications. It has been meticulously built with Laravel 10.10, leveraging the framework's efficient and robust functionalities.

Technologies and Packages Used

- PHP 8.1: We use the latest stable version of PHP, ensuring performance improvements and security enhancements.

- Laravel Framework 10.10: This PHP framework provides a clear and powerful structure for building quality web applications.

- Laravel Sanctum 3.2: For API authentication and protection, Sanctum offers a simple and lightweight way to manage authentication tokens.

- Spatie Laravel-Permission 5.11: This package is used for efficiently handling roles and permissions within the application.

- L5-Swagger 8.5: Integrates API documentation generation with Swagger, facilitating the creation and maintenance of clear and accurate documentation.

- GuzzleHttp/Guzzle 7.2: A PHP HTTP client that simplifies sending HTTP requests and integrating with other APIs.

- Laravel Tinker 2.8: A command-line tool that allows interaction with the entire Laravel project from the console.

- League Flysystem AWS S3 V3 3.16: Provides a simple interface for working with AWS S3 storage.

- PhpOffice PhpSpreadsheet 1.29: This package allows reading and writing various spreadsheet formats via PHP, offering great flexibility for data management.

## Project Focus

This project is designed as a base template, including a series of essential endpoints for authentication, user profile management, and basic functionalities of a web application. It focuses on offering a clean architecture and well-structured code, serving as a solid foundation for expansion and customization according to the specific needs of the project.

## Steps to run the project

1. Clone the project
2. Run `composer install`
3. Run `php artisan key:generate`
4. Run `php artisan migrate`
5. Run `php artisan serve`
6. Open the project in the browser

## Steps to run the tests

1. create a database called `testing`
2. create a `.env.testing` file and copy the content of `.env.example` file into it
3. change the `DB_DATABASE` value to `testing`
4. Run `php artisan migrate`
5. Run `php artisan test`
