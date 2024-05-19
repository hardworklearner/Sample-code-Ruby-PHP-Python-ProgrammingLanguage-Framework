# HealthRestfulWebsite

A web application for health management with Restful API implemented.

## Requirement

-   Ubuntu, Windows, Docker Environment
-   Laravel 10
-   PHP 8.2.15
-   Composer

## Install

-   Clone repository

```sh
git@github.com:zmerrychristmas/HealthRestfullWebsite.git
```

-   Change Env File

```bash
mv .env.example .env
```

-   Configure Database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

-   Generate Key

```bash
php artisan key:generate
```

-   Check Permission folder

```bash
# Set directory permissions to 775
sudo find ./storage -type d -exec chmod 775 {} \;
sudo find ./bootstrap/cache -type d -exec chmod 775 {} \;

# Set file permissions to 664
sudo find ./storage -type f -exec chmod 664 {} \;
sudo find ./bootstrap/cache -type f -exec chmod 664 {} \;

# Add your user to the www-data group
sudo usermod -a -G www-data $USER

# Apply group ownership recursively
sudo chgrp -R www-data ./storage
sudo chgrp -R www-data ./bootstrap/cache

# Ensure directories are group writable
sudo chmod -R ug+rwx ./storage
sudo chmod -R ug+rwx ./bootstrap/cache
```

-   Migration database

```bash
php artisan migrate
```

-   Run Mockup data with database Seeder

```bash
php artisan db:seed
```

-   Run server at localhost to check

```bash
php artisan serve
```

Web server will create a sample server at http://127.0.0.1:8000

## Document

## Specification and FAQ

Please file in **docs** folder: file: Health App\_ Specifications & FAQ.xlsx. Online version [Health Restful Website Specifications and FAQ](https://docs.google.com/spreadsheets/d/1MS0LrnM3yPwONmAInXvZjKA3MHNP1dZgAye-NjU5DZU/edit?usp=sharing)

## API Doc

Please file in **docs** folder: file: php_attempt.postman_collection.json

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Author

-   Senior Software Developer, Solution Architecture, Web developer
-   Blog page: [zmerrychristmas.github.io](https://zmerrychristmas.github.io)
