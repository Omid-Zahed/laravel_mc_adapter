<?php
 namespace Omidzahed\LaravelMcAdapter\Command;
 use Illuminate\Console\Command;
 use Omidzahed\LaravelMcAdapter\Exceptions\ProblemInAddAlias;
 use Symfony\Component\Process\Process;

 class SetMcAliasFromConfigCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mc:set';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'set Alias to mc from config ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
{
    parent::__construct();
}

     /**
      * Execute the console command.
      *
      * @return int
      * @throws ProblemInAddAlias
      */
    public function handle(): int
    {
    $disks=config("filesystems")["disks"];
    foreach ($disks as $name=>$configs){
        if ($configs["driver"]=="mc"){
            $alias=$configs["alias"];
            $key=$configs["key"];
            $secret=$configs["secret"];
            $endpoint=$configs["endpoint"];
            $mc_path=$configs["mc_path"];
            $api=$configs["api"]??"S3v4";
            $command="mc alias set $alias $endpoint $key $secret --api $api";
            $process=$this->getProcess($command,$mc_path);
            $process->run();
            if (empty($process->getOutput()))throw new ProblemInAddAlias("can not creat alias $name");
            echo ("desk :$name\nalias: $alias \nendpoint: $endpoint \n------------\n");

        }
    }
    return 1;
    }



     protected function getProcess($command,$mc_path): Process
     {
         return new \Symfony\Component\Process\Process(explode(" ",$command),null,['PATH' =>$mc_path]);
     }
}
