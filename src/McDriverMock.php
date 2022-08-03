<?php

namespace Omidzahed\LaravelMcAdapter;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;

class McDriverMock implements AdapterInterface
{

    protected $functions_queue;
    public function __construct($functions)
    {
       $this->functions=$functions;
    }

    protected function  call_function($func_name,$args){
        try {
            $function=$this->functions[$func_name][array_key_first($this->functions[$func_name])];
            array_shift($this->functions[$func_name]);
             return $function(...$args);
        }catch (\Exception $e){
          return false;
        }

    }

    public function write($path, $contents, Config $config)
    {
    return   $this->call_function("write",[$path,$contents,$config]);
    }

    public function writeStream($path, $resource, Config $config)
    {
       return  $this->call_function("writeStream",[$path,$resource,$config]);
    }

    public function update($path, $contents, Config $config)
    {
        return $this->call_function("update",[$path,$contents,$config]);
    }

    public function updateStream($path, $resource, Config $config)
    {
        return $this->call_function("updateStream",[$path,$resource,$config]);
    }

    public function rename($path, $newpath)
    {
        return $this->call_function("rename",[$path,$newpath]);
    }

    public function copy($path, $newpath)
    {
       return $this->call_function("copy",[$path,$newpath]);
    }

    public function delete($path)
    {
        return $this->call_function("delete",[$path]);
    }

    public function deleteDir($dirname)
    {
        return $this->call_function("deleteDir",[$dirname]);
    }

    public function createDir($dirname, Config $config)
    {
        return $this->call_function("createDir",[$dirname,$config]);
    }

    public function setVisibility($path, $visibility)
    {
        return $this->call_function("setVisibility",[$path,$visibility]);
    }

    public function has($path)
    {
     return $this->call_function("has",$path);
    }

    public function read($path)
    {
        return $this->call_function("read",$path);
    }

    public function readStream($path)
    {
        return $this->call_function("readStream",$path);
    }

    public function listContents($directory = '', $recursive = false)
    {
        return $this->call_function("listContents",[$directory,$recursive]);
    }

    public function getMetadata($path)
    {
        return $this->call_function("getMetadata",$path);
    }

    public function getSize($path)
    {
        return $this->call_function("getSize",$path);
    }

    public function getMimetype($path)
    {
        return $this->call_function("getMimetype",$path);
    }

    public function getTimestamp($path)
    {
        return $this->call_function("getTimestamp",$path);
    }

    public function getVisibility($path)
    {
        return $this->call_function("getVisibility",$path);
    }
}