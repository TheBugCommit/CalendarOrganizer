<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Requeriments 

- npm 16.15.0
- composer v2
- php7.4

## Prepare Enviroment

Once you clone this project exec:

- composer update
- npm install
- npm run dev
- php artisan key:generate
- php artisan jwt:secret
- php artisan cache:clear
- php artisan config:clear
- php artisan route:cache

Then copy the .env.example file to the root project and rename it to .env

Open the file and exchange the following keys for yours:

DB_CONNECTION=mysql <br/>
DB_HOST=<br/>
DB_DATABASE=<br/>
DB_USERNAME=<br/>
DB_PASSWORD=<br/>
<br/>
MAIL_DRIVER=<br/>
MAIL_HOST=<br/>
MAIL_PORT=<br/>
MAIL_USERNAME=<br/>
MAIL_PASSWORD=<br/>
MAIL_ENCRYPTION=<br/>
MAIL_FROM_ADDRESS=<br/>
<br/>
And that's it, use "php artisan serve" to start a server.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
