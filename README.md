# ITS-UI

A package to scaffold UI for ITS laravel applications. Quickly run commands to set up ITS scaffolding on a base laravel application.

### Why?
This package helps in separating base laravel application in docker build step. Also included are best practices and refactorings.

## Requirements

> Note: Remember to update the ``` middleware ``` for API's or web routes if different scheme of authentication is used.

## Installation and Usage

- Run ``` composer require uisits/ui ```
- Run ``` php artisan its-ui:install ```

> ### **Note:**
> This command copies all the stubs to your base application.
> > You are free to update the stubs once they are published.
> > You can also install other required packages as you would previously.

## Testing
- Create a docker container.
- remove the current laravel application at ``` /var/www/laravel```
  by running ``` cd .. && rm -rf laravel```
- Install a fresh Laravel application at ``` /var/www/``` by running
  ``` composer create-project --prefer-dist laravel/laravel:^7.0 laravel```
- Or For Laravel 8 ``` composer create-project --prefer-dist laravel/laravel blog```.
- Now Install the package by following the Installation steps for the package.
    - Run ```composer require uisits/ui ```
    - Run ``` php artisan its-ui:install ```

For version requirement please refer the table below:

|    Laravel     |     package-version   |
|----------------|-----------------------|
|       7.*      |          7.*          |
|       8.*      |          8.*          |

## Issues and Feature Requests
- Please create a new issue and mention any bugs or feature requests.

> Maintained by [UIS ITS](https://github.com/uisits).
