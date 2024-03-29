<?php

namespace Omidzahed\LaravelMcAdapter\jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Laravel\SerializableClosure\SerializableClosure;
use Omidzahed\LaravelMcAdapter\contract\Path;


class McJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $callback;
    protected $callback_on_fail;
    protected $from;
    protected $to;
    protected $disk;
    protected $type;
    protected array $accept_function=["move","copy"];


    public function __construct($type,Path $from,Path $to, $disk,$callback,$callback_on_fail=null)
    {
        if (!in_array($type,$this->accept_function))throw new \Exception("accept function is ". join(",",$this->accept_function));
        $this->callback = new SerializableClosure($callback);
        //if is clouser
        if ($callback_on_fail) $this->callback_on_fail = new SerializableClosure($callback_on_fail);
        $this->from = $from;
        $this->to = $to;
        $this->disk = $disk;
        $this->type=$type;
    }


    public function handle()
    {
        if (Storage::disk($this->disk)->{$this->type}($this->from,$this->to)){
            $this->callback->getClosure()();
        }else {
            throw new \Exception("failed in ".$this->type." file from ".$this->from->path." to ".$this->to->path." on disk ".$this->disk);
        }
    }
    public function failed( $exception=null)
    {
        if($this->callback_on_fail) $this->callback_on_fail->getClosure()($exception);
        if($exception instanceof \Exception ) throw $exception;
        else throw new \Exception("failed in ".$this->type." file from ".$this->from->path." to ".$this->to->path." on disk ".$this->disk);


    }


}
