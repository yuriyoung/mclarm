# Introduction
REST API base on laravel framework.

# Features
- jwt token auth
- socialite auth
- api request
- api json resource response

# Requirements
- Laravel 6 framework
- jwt-auth
- laravel/socialite

# Installation
1. Clone this repo and run `composer install`
    ```shell script
    git clone https://github.com/yuriyoung/mclarm.git

    composer install
    ```
2. Copy `.env` file
    ```shell script
    cp .env.example .env
    ```
3. Edit your database credential in `.env` file.
4. Add providers `CLIENT_ID`, `CLIENT_SECRET` and `REDIRECT`
    ```dotenv
    GITHUB_CLIENT_ID=your-app-id
    GITHUB_CLIENT_SECRET=your-app-secret
    GITHUB_REDIRECT=yuur-app-redirect
    ```
   
    > Make sure you set correct redirect url in dev console of providers.
    > It should be like `"{env(APP_URL)}/oauth/github/callback"` format.
5. Now you can run `php artisan migrate` and `serve` your app

# License
The mclarm software is licensed under the [MIT License](https://github.com/yuriyoung/mclarm/blob/master/LICENSE). 

## CopyRight
Copyright © Yuri Young yuri.young@qq.com

<img src="https://github.com/yuriyoung/resources/blob/master/weixin-pay.jpg" width="200" alt="Weixin-QR-code" />
