<?php

namespace Omidzahed\LaravelMcAdapter;

use Closure;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Omidzahed\LaravelMcAdapter\Command\McTestCommand;
use Omidzahed\LaravelMcAdapter\Command\SetMcAliasFromConfigCommand;

class McProvider extends ServiceProvider
{
    public function boot()
    {
        Storage::extend('mc',function($app, $config){
            $client = new McDriver(
                $config["mc_path"],
                $config["bucket"],
                $config["alias"],
            );
            return new FilesystemMc($client,$config);
        });
    }

    public function register()
    {
        $this->registerCommands();
        $this->publishes([
            __DIR__.'/../config/mcDriver.php' => \config_path('mcDriver.php'),
        ]);
        $this->mergeConfigFrom(__DIR__.'/../config/mcDriver.php', 'mcDriver');
    }

    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([SetMcAliasFromConfigCommand::class,McTestCommand::class]);
        }



    }

}
