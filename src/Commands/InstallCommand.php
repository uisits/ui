<?php

namespace Uisits\Ui\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Process\Process;
use Uisits\Ui\Helpers\Helper;

class InstallCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'its-ui:install
                            {stack : The development stack that should be installed (vue2,inertia)}
                            {--force : Overwrite existing views by default }
                            {--composer=global : Absolute path to the Composer binary which should be used to install packages}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install external packages for UisIts app';

    /**
     * Execute the console command.
     *
     * @throws \InvalidArgumentException
     */
    public function handle()
    {
        if (!in_array($this->argument('stack'), ['inertia', 'vue2', 'vue3'])) {
            $this->components->error('Invalid stack. Supported stacks are [inertia], [vue2] and [vue3].');
            return 1;
        }

        // Install AdLdap and Oci package
        $this->info('Preparing to Install Composer Packages');
        if (!$this->requireComposerPackages('adldap2/adldap2-laravel:^6.1', 'yajra/laravel-oci8:^9.0')) {
            return false;
        }

        if ($this->argument('stack') === 'vue2') {
            if (!$this->installvue2Stack()) {
                return 1;
            }
        } elseif ($this->argument('stack') === 'vue3') {
            if (!$this->installvue3Stack()) {
                return 1;
            }
        } elseif ($this->argument('stack') === 'inertia') {
            if (!$this->installInertiaStack()) {
                return 1;
            }
        }

        $this->runComposerCommand(["composer", "update"]);

        $this->info("Done!");
        $this->comment('Please execute the "npm install && npm run dev" command to install dependencies.');
        $this->comment('Please execute the "composer update" command to install dependencies.');
    }

    /**
     * Scaffold stubs required for Vue2
     *
     * @return bool
     */
    public function installVue2Stack(): bool
    {
        // NPM Packages...
        $this->updateNodePackages(function ($packages) {
            return [
                    '@tailwindcss/forms' => '^0.3.4',
                    '@tailwindcss/typography' => '^0.4.1',
                    'autoprefixer' => '^10.1.0',
                    'postcss' => '^8.2.1',
                    'postcss-import' => '^12.0.1',
                    'sass' => '^1.32.4',
                    'vue-loader' => '^15.9.6',
                    'lodash' => '^4.17.19',
                    'sass-loader' => '^10.1.1',
                    'deepmerge' => '^2.2.1',
                    'tailwindcss' => '^2.0.2',
                    'laravel-mix' => '^4.0.7',
                    'vue' => '^2.6.12',
                    'resolve-url-loader' => '^3.1.0',
                    'vue-template-compiler' => '^2.6.12',
                    'vuetify' => '^2.4.2'
                ] + $packages;
        });

        (new Filesystem)->ensureDirectoryExists(resource_path('js/Components'));
        (new Filesystem)->ensureDirectoryExists(resource_path('js/Pages'));

        $this->info("Copying required files");
        copy(
            __DIR__ . '/../../stubs/vue2/webpack.mix.js',
            base_path('webpack.mix.js')
        );
        copy(
            __DIR__ . '/../../stubs/vue2/tailwind.config.js',
            base_path('tailwind.config.js')
        );
        copy(
            __DIR__ . '/../../stubs/vue2/resources/js/app.js',
            resource_path('js/app.js')
        );

        copy(
            __DIR__ . '/../../stubs/vue2/resources/css/app.css',
            resource_path('css/app.css')
        );

        $this->runCommands(['npm install', 'npm run build']);

        $this->line('');
        $this->components->info('Inertia scaffolding installed successfully.');

        return true;
    }

    /**
     * Scaffold stubs required for Vue3
     *
     * @return bool
     */
    public function installVue3Stack(): bool
    {
        // NPM Packages...
        $this->updateNodePackages(function ($packages) {
            return [
                    '@tailwindcss/forms' => '^0.5.2',
                    '@tailwindcss/typography' => '^0.5.2',
                    'laravel-mix' => '^6.0',
                    'autoprefixer' => '^10.4.7',
                    'postcss' => '^8.4.14',
                    'tailwindcss' => '^3.1.0',
                    'vue' => '^3.2.31',
                ] + $packages;
        });
        copy(
            __DIR__ . '/../../stubs/vue3/webpack.mix.js',
            base_path('webpack.mix.js')
        );
        copy(
            __DIR__ . '/../../stubs/vue3/tailwind.config.js',
            base_path('tailwind.config.js')
        );
        copy(
            __DIR__ . '/../../stubs/vue3/app.js',
            resource_path('webpack.mix.js')
        );
        return true;
    }

    /**
     * Install the Inertia stack into the application.
     *
     * @return bool
     */
    protected function installInertiaStack(): bool
    {
        // Install Inertia...
        if (!$this->requireComposerPackages('inertiajs/inertia-laravel:^0.6.8', 'tightenco/ziggy:^1.0')) {
            return false;
        }

        // Install NPM packages...
        $this->updateNodePackages(function ($packages) {
            return [
                    '@inertiajs/vue3' => '^1.0.0',
                    '@tailwindcss/forms' => '^0.5.2',
                    '@tailwindcss/typography' => '^0.5.2',
                    'laravel-mix' => '^6.0',
                    'autoprefixer' => '^10.4.7',
                    'postcss' => '^8.4.14',
                    'tailwindcss' => '^3.1.0',
                    'vue' => '^3.2.31',
                ] + $packages;
        });

        // Tailwind Configuration...
        copy(__DIR__ . '/../../stubs/inertia/tailwind.config.js', base_path('tailwind.config.js'));
        copy(__DIR__ . '/../../stubs/inertia/postcss.config.js', base_path('postcss.config.js'));
        copy(__DIR__ . '/../../stubs/inertia/vite.config.js', base_path('vite.config.js'));

        // jsconfig.json...
        copy(__DIR__ . '/../../stubs/inertia/jsconfig.json', base_path('jsconfig.json'));

        Helper::installMiddlewareAfter('SubstituteBindings::class', '\App\Http\Middleware\HandleInertiaRequests::class');
        Helper::installMiddlewareAfter('\App\Http\Middleware\HandleInertiaRequests::class', '\Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class');

        $this->line('');
        $this->components->info('Inertia scaffolding installed successfully.');

        return true;
    }

    /**
     * Run a composer command on the shell.
     *
     * @param array $command
     * @return bool
     */
    protected function runComposerCommand(array $command): bool
    {
        return !(new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            });
    }

    /**
     * Update the "package.json" file.
     *
     * @param  callable  $callback
     * @param bool $dev
     * @return void
     */
    protected static function updateNodePackages(callable $callback, bool $dev = true): void
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
     * Installs the given Composer Packages into the application.
     *
     * @param  mixed  $packages
     * @return bool
     */
    protected function requireComposerPackages(mixed $packages): bool
    {
        $composer = $this->option('composer');

        if ($composer !== 'global') {
            $command = [$this->phpBinary(), $composer, 'require'];
        }

        $command = array_merge(
            $command ?? ['composer', 'require'],
            is_array($packages) ? $packages : func_get_args()
        );

        return ! (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            });
    }

    /**
     * Run the given commands.
     *
     * @param  array  $commands
     * @return void
     */
    protected function runCommands($commands): void
    {
        $process = Process::fromShellCommandline(implode(' && ', $commands), null, null, null, null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            try {
                $process->setTty(true);
            } catch (RuntimeException $e) {
                $this->output->writeln('  <bg=yellow;fg=black> WARN </> '.$e->getMessage().PHP_EOL);
            }
        }

        $process->run(function ($type, $line) {
            $this->output->write('    '.$line);
        });
    }

}
