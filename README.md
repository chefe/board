# board
[![Build Status](https://travis-ci.org/chefe/menuplanner.svg?branch=master)](https://travis-ci.org/chefe/menuplanner)
[![StyleCI](https://github.styleci.io/repos/163540103/shield?branch=master&style=flat)](https://github.styleci.io/repos/163540103)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=chefe_board&metric=alert_status)](https://sonarcloud.io/dashboard?id=chefe_board)

A very minimal web based scrum board.
Aims to replace [the outdated scrum-board][1].

## Installation
1. Begin by cloning this repository to your machine, and installing all Composer & NPM dependencies.

```bash
git clone git@github.com:chefe/board.git
cd board && composer install && npm install
cp .env.example .env
php artisan key:generate
npm run dev
```

2. Next, configure your database credentials in in the `.env` file.
3. After that, you can migrate the database with the command `php artisan migrate`.
4. Start the websocket server with the command `php artisan websocket:serve`
4. Boot up a server with the command `php artisan serve` (or use a real server)
5. Finally you can visit the application in your browser.

## Credit
* https://getbootstrap.com/docs/
* https://laravel.com/
* https://vuejs.org/
* https://github.com/axios/axios
* https://docs.spatie.be/laravel-activitylog/
* https://docs.beyondco.de/laravel-websockets/

[1]: https://github.com/chefe/scrum-board
