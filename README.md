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

## Local Set Up
- Create a fresh laravel application as we do for all projects
- Create a folder ``` packages ```` at ``` /var/www/laravel/packages```
- Create a folder ``` uisits``` which is the vendor name for our package in the packages directory at ``` /var/www/laravel/packages/uisits```
- cd into this vendor name folder (``` cd /var/www/laravel/packages/uisits```) and run ``` git clone git@github.com:uisits/ui.git ```
- This copies all the files of the package from github.
- To work on the **7.x** run ``` git checkout 7.x``` (Here 7.x is the name of the branch on github)
- Similarly to work on the **8.x** run ``` git checkout .8x``` (Here 8.x is the name of the branch on github)
- To use this package locally we need to add this package to our laravel app. We can do this by adding the following to out ``` composer.json``` file at ``` /var/www/laravel/composer.json```.
    ```php
    "repositories": {
        "local": {
            "type": "path",
            "url": "./packages/uisits/ui"
        }
    },
    ```
    Now your entire composer.json file looks like this:
    ```php
    {
        "name": "laravel/laravel",
        "type": "project",
        "description": "The Laravel Framework.",
        "keywords": [
            "framework",
            "laravel"
        ],
        "license": "MIT",
        "repositories": {
            "local": {
                "type": "path",
                "url": "./packages/uisits/ui"
            }
        },
        "require": {
            "adldap2/adldap2-laravel": "^6.0",
            "fideloper/proxy": "^4.4",
            "fruitcake/laravel-cors": "^2.0",
            "laravel/framework": "^7.0",
            "laravel/passport": "^8.4",
            "laravel/tinker": "^2.0",
            "php": "^7.2.5|^8.0",
            "uabookstores/laravel-oidc": "3.1.1",
            "uisits/ui": "^7.0",
            "yajra/laravel-oci8": "^7.0"
        },
        "require-dev": {
            "barryvdh/laravel-debugbar": "^3.3",
            "facade/ignition": "^2.0",
            "fzaninotto/faker": "^1.4",
            "laravel/dusk": "^5.5",
            "mockery/mockery": "^1.0",
            "nunomaduro/collision": "^4.1",
            "phpunit/phpunit": "^8.5"
        },
        "config": {
            "optimize-autoloader": true,
            "preferred-install": "dist",
            "sort-packages": true
        },
        "extra": {
            "laravel": {
                "dont-discover": [
                    "laravel/dusk"
                ]
            }
        },
        "autoload": {
            "psr-4": {
                "App\\": "app/"
            },
            "classmap": [
                "database/seeds",
                "database/factories"
            ]
        },
        "autoload-dev": {
            "psr-4": {
                "Tests\\": "tests/"
            }
        },
        "minimum-stability": "dev",
        "prefer-stable": true,
        "scripts": {
            "post-autoload-dump": [
                "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
                "@php artisan package:discover --ansi"
            ],
            "post-root-package-install": [
                "@php -r \"file_exists('.env') || copy('.env.example', '.env');\""
            ],
            "post-create-project-cmd": [
                "@php artisan key:generate --ansi"
            ]
        }
    }
    ```
- All that is now required is to run ``` composer install uisits/ui ```. This should now install the package from the local directory.

> Note:
> Sometimes you might need to run ``` composer dump-autoload```.

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
