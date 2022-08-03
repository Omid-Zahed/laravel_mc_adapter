<?php

namespace Omidzahed\LaravelMcAdapter;
use Closure;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Omidzahed\LaravelMcAdapter\Command\McTestCommand;
use Omidzahed\LaravelMcAdapter\Command\SetMcAliasFromConfigCommand;
class helperMc
{

    public static function  makeMockMc($functions)
    {
        Storage::extend('mc',function($app, $config)use($functions){
            $client = new McDriverMock($functions);
            return new FilesystemMc($client,$config);
        });
    }

}