<?php

namespace Omidzahed\LaravelMcAdapter\contract;

class Path
{
    public static $REMOTE="remote";
    public static $LOCAL="local";

    public $path;
    public $location;
    public function __construct($location,$path)
    {
        $this->location=$location;
        $this->path=$path;
    }

}