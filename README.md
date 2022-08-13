<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>



## Expense Manager

### Server Specifications on Dev
- PHP 8.1^
- node v17.7.1
- npm 8.5.2
- Composer version 2.3.10 2022-07-13 15:48:23
- mysql  Ver 15.1 Distrib 10.5.15-MariaDB, for debian-linux-gnu (x86_64) using  EditLine wrapper


### Installation

- clone repository
- run `composer install`
- run `npm install`
- check for `.env` if not rename `.env.example`
- setup db config in `.env`
- run `php artisan key:generate`
- run `php artisan migrate`
- rub `php artisab db:seed`

### Running Application

- `php artisan serve` create local server
- `npm run dev` (optional) runs webpack compiler 

#### Default USERS (after running `db:seed`)

| Role            |       Username       |   password   |
|:----------------|:--------------------:|-------------:|
| Administrator   |   admin@system.com   |   password   |
| User            |   user@system.com    |   password   |

#### Adding Users
- When administrator adds users the default password is `password`
