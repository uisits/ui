<?php

namespace Uisits\Ui\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InstallCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'its-ui:install
                            {--force : Overwrite existing views by default }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install external packages for UisIts app';

    /**
     * Execute the console command.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function handle()
    {
        // NPM Packages...
        $this->updateNodePackages(function ($packages) {
            return [
                    'autoprefixer' => '^10.1.0',
                    'postcss' => '^8.2.1',
                    'postcss-import' => '^12.0.1',
                    'sass' => '^1.32.4',
                    'vue-loader' => '^15.9.6',
                    'lodash' => '^4.17.19',
                    'sass-loader' => '^10.1.1',
                    'deepmerge' => '^2.2.1',
                    'tailwindcss' => '^2.0.2',
                    'vue' => '^2.6.12',
                    'resolve-url-loader' => '^3.1.0',
                    'vue-template-compiler' => '^2.6.12',
                    'vuetify' => '^2.4.2'
                ] + $packages;
        });

        // Composer Packages...
        $this->updateComposerPackages(function ($packages) {
            return [
                    "uabookstores/laravel-shibboleth" => "^3.3",
                    "adldap2/adldap2-laravel" => "^6.0",
                    "yajra/laravel-oci8" => "^8.0",
                    "laravel/passport" => "^10.0.1"
                ]
                + $packages;
        }, false);

        // Copy configs files to config directory
        (new Filesystem)->copyDirectory(
            __DIR__.'/../../stubs/config',
            config_path()
        );

        // Copy Controllers
        (new Filesystem)->ensureDirectoryExists(app_path('Http/Controllers'));
        (new Filesystem)->copyDirectory(
            __DIR__.'/../../stubs/Http/Controllers',
            app_path('Http/Controllers')
        );

        // Copy Api Controllers
        (new Filesystem)->ensureDirectoryExists(app_path('Http/Controllers/Api'));
        (new Filesystem)->copyDirectory(
            __DIR__.'/../../stubs/Http/Controllers/Api',
            app_path('Http/Controllers/Api')
        );

        // Copy Models
        (new Filesystem)->ensureDirectoryExists(app_path('Models'));
        (new Filesystem)->copyDirectory(
            __DIR__.'/../../stubs/Models',
            app_path('Models')
        );

        // Copy Middlewares
        (new Filesystem)->ensureDirectoryExists(app_path('Http/Middleware'));
        (new Filesystem)->copyDirectory(
            __DIR__.'/../../stubs/Http/Middleware',
            app_path('Http/Middleware')
        );

        // Copy Providers
        (new Filesystem)->ensureDirectoryExists(app_path('Providers'));
        (new Filesystem)->copyDirectory(
            __DIR__.'/../../stubs/Providers',
            app_path('Providers')
        );

        // Copy FormRequests
        (new Filesystem)->ensureDirectoryExists(app_path('Http/Requests'));
        (new Filesystem)->copyDirectory(
            __DIR__.'/../../stubs/Http/Requests',
            app_path('Http/Requests')
        );

        // Copy Api Resources
        (new Filesystem)->ensureDirectoryExists(app_path('Http/Resources'));
        (new Filesystem)->copyDirectory(
            __DIR__.'/../../stubs/Http/Resources',
            app_path('Http/Resources')
        );

        // Copy Traits
        (new Filesystem)->ensureDirectoryExists(app_path('Http/Traits'));
        (new Filesystem)->copyDirectory(
            __DIR__.'/../../stubs/Http/Traits',
            app_path('Http/Traits')
        );

        // Copy View Components (Not Vue Components)
        (new Filesystem)->ensureDirectoryExists(app_path('View/Components'));
        (new Filesystem)->copyDirectory(
            __DIR__.'/../../stubs/View/Components',
            app_path('View/Components')
        );

        // Copy Assets
        (new Filesystem)->ensureDirectoryExists(public_path());
        (new Filesystem)->copyDirectory(
            __DIR__.'/../../stubs/assets',
            public_path()
        );

        // Copy Views, JS, CSS and vue components
        (new Filesystem)->ensureDirectoryExists(resource_path());
        (new Filesystem)->copyDirectory(
            __DIR__.'/../../stubs/resources',
            resource_path()
        );

        // Copy Helper file
        (new Filesystem)->ensureDirectoryExists(app_path('Helpers'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/Helpers', app_path('Helpers'));

        // Copy routes
        copy(__DIR__.'/../../stubs/routes/web.php', base_path('routes/web.php'));
        copy(__DIR__.'/../../stubs/routes/api.php', base_path('routes/api.php'));

        // Copy Tailwind.config and webpack.mix.js
        copy(__DIR__.'/../../stubs/tailwind.config.js', base_path('tailwind.config.js'));
        copy(__DIR__.'/../../stubs/webpack.mix.js', base_path('webpack.mix.js'));

        // Copy Kernel.php file
        copy(__DIR__.'/../../stubs/Http/Trouble.php', app_path('Http/Kernel.php'));

        $this->info("Done!");
        $this->comment('Please execute the "npm install && npm run dev" command to install dependencies.');
        $this->comment('Please execute the "composer update" command to install dependencies.');
    }

    /**
     * Update the "package.json" file.
     *
     * @param  callable  $callback
     * @param  bool  $dev
     * @return void
     */
    protected static function updateNodePackages(callable $callback, $dev = true)
    {
        if (! file_exists(base_path('package.json'))) {
            return;
        }

        $configurationKey = $dev ? 'devDependencies' : 'dependencies';

        $packages = json_decode(file_get_contents(base_path('package.json')), true);

        $packages[$configurationKey] = $callback(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
            $configurationKey
        );

        ksort($packages[$configurationKey]);

        file_put_contents(
            base_path('package.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL
        );
    }

    /**
     * Update the "composer.json" file.
     *
     * @param  callable  $callback
     * @param  bool  $dev
     * @return void
     */
    protected static function updateComposerPackages(callable $callback, $dev = true)
    {
        if (! file_exists(base_path('composer.json'))) {
            return;
        }

        $configurationKey = $dev ? 'require-dev' : 'require';

        $packages = json_decode(file_get_contents(base_path('composer.json')), true);

        $packages[$configurationKey] = $callback(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
            $configurationKey
        );

        ksort($packages[$configurationKey]);

        file_put_contents(
            base_path('composer.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL
        );
    }

}
