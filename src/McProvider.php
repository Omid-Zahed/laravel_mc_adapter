<?php

namespace Omidzahed\LaravelMcAdapter;

use Closure;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Omidzahed\LaravelMcAdapter\Command\SetMcAliasFromConfigCommand;

class McProvider extends ServiceProvider
{
    public function boot()
    {
        Storage::extend('mc',function($app, $config){
            $client = new McDriver(
                $config["mc_path"],
                $config["key"],
                $config["secret"],
                $config["bucket"],
                $config["endpoint"],
                $config["alias"],
                $config["auto_add_alias"]??false
            );
            return new FilesystemMc($client,$config);
        });
    }

    public function register()
    {
        $this->registerCommands();
    }

    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([SetMcAliasFromConfigCommand::class]);
        }


    }

}
