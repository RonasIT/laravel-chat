# Laravel Chat Plugin

[![Coverage Status](https://coveralls.io/repos/github/RonasIT/laravel-chat/badge.svg?branch=master)](https://coveralls.io/github/RonasIT/laravel-chat?branch=master)

## Introduction

This plugin adds the ability for users to work with chat functionalities in a Laravel application.

## Installation

1. Install the package using the following command:

```sh
composer require ronasit/laravel-chat
```

2. Publish the package configuration:

``` sh
php artisan vendor:publish --provider=RonasIT\\Chat\\ChatServiceProvider
```

3. For Laravel <= 5.5 add `ronasit\Chat\ChatServiceProvider::class` to the `app.providers` list in config.
4. Set your project's User model to the `chat.classes.user_model` config.
5. All routes are registered by default, you can change the route registration by calling `Route::chat()` in your routes file (e.g. `routes/api.php`).
   - feel free to call `Route::chat()` helper inside any route wrappers like `group`, `prefix`, etc. to wrap package routes;
   - calling `Route::chat()` without args will add all package route inside the calling helper place;
   - calling `Route::chat()` with any args will add only routes with chosen actions;

## Integration with [LaravelSwagger](https://github.com/RonasIT/laravel-swagger)

This package includes an OpenAPI documentation file. To include it in your project's documentation, you need to register it in the `auto-doc.additional_paths` config:

`vendor/ronasit/laravel-chat/documentation.json`

## Contributing

Thank you for considering contributing to the Laravel Chat plugin! The contribution guide can be found in the [Contributing guide](CONTRIBUTING.md).

## License

Laravel Chat plugin is open-sourced software licensed under the [MIT license](LICENSE).
