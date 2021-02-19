# ITS-UI

A package to scaffold UI for ITS laravel applications. Quickly run commands to set up ITS scaffolding on a base laravel application.

### Why?
This package helps in separating base laravel application in docker build step. Also included are best practices and refactorings.

### TODO:
- [ ] Remove TwitterBootstrap (Laravel 8 supports [AlpineJS](https://github.com/alpinejs/alpine))
- [ ] Use AlpineJS for Navigation dropdowns and Cookie settings
- [ ] Update Navbar and Footer using TailwindCSS

## Requirements
This package requires the following packages:
1. [Twitter Bootstrap](https://getbootstrap.com)
2. [VuetifyjS](https://vuetifyjs.com/en/)
3. [Material Design icons](https://vuetifyjs.com/en/features/icons/#material-icons)
4. Auth Scaffolding for API's either [Laravel Passport](https://laravel.com/docs/8.x/passport) or any other authencation method.

> Note: Remember to update the ``` middleware ``` for API's or web routes if different scheme of authentication is used.

## Installation

This is a private repository hence you can't install this package as a composer repository.

Follow these steps to install the package.

In your ``` composer.json ``` file add these lines:

```php [composer.json]
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/uisits/its-ui"
        }
    ],
```

Next, in the ``` require ``` array add the package dependency as follows:

```php [composer.json]
    "require": {
        "php": "^7.2.5|^8.0",
        "laravel/framework": "^7.29",
        "laravel/tinker": "^2.5",
        "its/ui": "8.*"
    },
```
Lastly, please run ``` composer update ``` to install the package.

For version requirement please refer the table below:

|    Laravel     |     package-version   |
|----------------|-----------------------|
|       7.*      |          7.*          |
|       8.*      |          8.*          |

## Usage

### Using Default ITS views
- Run: ``` php artisan its-ui:baseviews ```

### Publishing ITS assets (Images, icons etc.)
- Run: ``` php artisan its-ui:assets```

### Using Impersonate feature
> Note: By default Laravel installs the frontend assets at ``` resources/js/* ```.
> If this is not your default path you can override the path by addinng the path to ``` config/view.php ```

Example:
```
    return [
        'frontend' => resource_path('assets/js'),
        'paths' => [
            resource_path('views'),
        ],
        'compiled' => env(
            'VIEW_COMPILED_PATH',
            realpath(storage_path('framework/views'))
        ),
    ];
```

- Run ``` php artisan its-ui:impersonate ```
- Add the ``` Impersonate.php``` middleware to your ``` $routeMiddleware``` array in ``` app/Http/Kernel.php ``` file.
  ``` 'impersonate' => \App\Http\Middleware\Impersonate::class, ```
- This adds various files to your project as well as routes in ``` web.php ``` and ``` api.php ``` file.
- For usage just add the impersonate middleware to either your controller or add the middleware to the route.

### Adding Feedback feature to your app
- Run ``` php artisan its-ui:feedback ```
    - This copies files required to set up Feedback feature.
    - **Assumes all admins are authorized using the ``` is-admin ``` Gate.** If you want to change the name of the gate be sure to edit the necessary files.
