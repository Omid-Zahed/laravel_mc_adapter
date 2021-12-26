<?php
 namespace Omidzahed\LaravelMcAdapter\Command;
 use Illuminate\Console\Command;
 use Omidzahed\LaravelMcAdapter\Exceptions\ProblemInAddAlias;
 use Symfony\Component\Process\Process;

 class McTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mc:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test mc path ';

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
            $mc_path=$configs["mc_path"];
            $command="mc -v";
            $process=$this->getProcess($command,$mc_path);
            $process->run();
            if (empty($process->getOutput()))throw new ProblemInAddAlias("can not creat alias $name");
            echo ("tested mc path for desk :$name \n");
        }
    }
    return 1;
    }



     protected function getProcess($command,$mc_path): Process
     {
         return new \Symfony\Component\Process\Process(explode(" ",$command),null,['PATH' =>$mc_path]);
     }
}
