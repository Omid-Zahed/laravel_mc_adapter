<?php
namespace Omidzahed\LaravelMcAdapter;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;
use Omidzahed\LaravelMcAdapter\contract\Path;
use Omidzahed\LaravelMcAdapter\Exceptions\DontFoundMcBinary;
use Omidzahed\LaravelMcAdapter\Exceptions\NotImplementedException;
use Omidzahed\LaravelMcAdapter\Exceptions\ProblemInAddAlias;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class McDriver  implements AdapterInterface
{

    protected $server_symptom="~";
    protected $mc_path;
    protected $bucket,$name;

    /**
     * @throws DontFoundMcBinary
     * @throws ProblemInAddAlias
     */
    public function __construct($mc_path,$bucket,$name="main")
    {
        $this->mc_path=$mc_path;
        $this->bucket=$bucket;
        $this->name=$name;
    }

    /**
     * @throws DontFoundMcBinary
     */
    private function testMcPath(): void
    {
        $pr= $this->getProcess("mc -v");
        $pr->run();
        if (empty(trim($pr->getOutput())))throw new DontFoundMcBinary("dont found mc in path" . $this->mc_path);
    }


    /**
     * @throws ProblemInAddAlias
     */
    protected function setAlias($name, $endpoint, $key, $secret, $api="S3v4"){
        $command="mc alias set $name $endpoint $key $secret --api $api";
        $process=$this->getProcess($command);
        $process->run();
        if (empty($process->getOutput()))throw new ProblemInAddAlias("can not creat alias $name");
    }

    protected function getProcess($command): Process
    {
        $p=new \Symfony\Component\Process\Process(explode(" ",$command),null,['PATH' =>$this->mc_path]);
        $p->setTimeout(\config("mcDriver.process_timout",60));
        return $p;
    }
    protected function ls($path=""){
        $command="mc ls $this->name/$this->bucket/$path --json";
        $process= $this->getProcess($command);
        $process->mustRun();
        $output=$process->getOutput();
        $data= preg_replace("/{/",",{",$output);
        $data= preg_replace("/^,{/","{",$data);
        $data="[$data]";
        return json_decode($data);
    }

    public function write($path, $contents, Config $config)
    {
        throw new NotImplementedException();
    }

    public function writeStream($path, $resource, Config $config)
    {
        throw new NotImplementedException();
    }

    public function update($path, $contents, Config $config)
    {
        throw new NotImplementedException();
    }

    public function updateStream($path, $resource, Config $config)
    {
        throw new NotImplementedException();
    }

    public function rename($path, $newpath)
    {
        throw new NotImplementedException();
    }

    /**
     * @param Path $from
     * @param Path $to
     * @return bool
     * @throws \Exception
     */
    public function move($from,$to){
        if (!($from instanceof Path) or !($to instanceof Path)){
            throw new \InvalidArgumentException('$from and $to must be instance of Path');
        }
        if ($from->location==Path::$REMOTE and $to->location==Path::$REMOTE)return $this->moveS3toS3($from->path,$to->path);
        if ($from->location==Path::$LOCAL  and $to->location==Path::$REMOTE)return $this->moveLocalToS3($from->path,$to->path);
        if ($from->location==Path::$REMOTE and $to->location==Path::$LOCAL)return $this->moveS3toLocal($from->path,$to->path);
        throw new \Exception("not found move method for this path");
    }
    protected function moveS3toS3($from,$to){
        list($from,$to)=$this->removeServerSymptom($from,$to);
        $command= "mc --debug mv ".$this->name."/".$this->bucket."/$from ".$this->name."/".$this->bucket."/$to --recursive";
        $process= $this->getProcess($command) ;
        $process->run();
         if($process->isSuccessful()){
            return true;
        }else{
            throw new \Exception($process->getErrorOutput()." => mc run output: ".$process->getOutput()); 
        }
    }
    protected function moveLocalToS3($from,$to){
        list($from,$to)=$this->removeServerSymptom($from,$to);

        $command= "mc --debug mv $from ".$this->name."/".$this->bucket."/$to";
        if (is_dir($from)){$command.=" --recursive";}
        $process= $this->getProcess($command) ;
        $process->run();
         if($process->isSuccessful()){
            return true;
        }else{
            throw new \Exception($process->getErrorOutput()." => mc run output: ".$process->getOutput()); 
        }

    }
    protected function moveS3toLocal($from,$to){
        list($from,$to)=$this->removeServerSymptom($from,$to);
        $command= "mc --debug mv ".$this->name."/".$this->bucket."/$from $to --recursive";
        $process= $this->getProcess($command) ;
        $process->run();
        if($process->isSuccessful()){
            return true;
        }else{
            throw new \Exception($process->getErrorOutput()." --- ".$process->getOutput());
        }

    }


    public function copy( $from, $to)
    {
        if (!($from instanceof Path) or !($to instanceof Path)){
            throw new \InvalidArgumentException('$from and $to must be instance of Path');
        }
        if ($from->location==Path::$LOCAL && $to->location==Path::$REMOTE)  return $this->copyLocalToS3($from->path,$to->path);
        if ($from->location==Path::$REMOTE && $to->location==Path::$REMOTE)  return $this->copyS3ToS3($from->path,$to->path);
        if ($from->location==Path::$REMOTE && $to->location==Path::$LOCAL)  return $this->copyS3ToLocal($from->path,$to->path);
        throw new \Exception ("not found copy method for this path");
    }
    protected function copyLocalTOS3($from,$to){

        $command= "mc --debug cp $from ".$this->name."/".$this->bucket."/$to";
        if (is_dir($from)){$command.=" --recursive";}
        $process= $this->getProcess($command) ;
        $process->run();
        if($process->isSuccessful()){
            return true;
        }else{
            throw new \Exception($process->getErrorOutput()." => mc run output: ".$process->getOutput()); 
        }
    }
    protected function copyS3toS3($from,$to){

        $command= "mc --debug cp ".$this->name."/".$this->bucket."/$from ".$this->name."/".$this->bucket."/$to --recursive" ;
        $process= $this->getProcess($command);
        $process->run();
         if($process->isSuccessful()){
            return true;
        }else{
            throw new \Exception($process->getErrorOutput()." => mc run output: ".$process->getOutput()); 
        }

    }
    protected function copyS3toLocal($from,$to){

        $command= "mc --debug cp ".$this->name."/".$this->bucket."/$from $to --recursive" ;
        $process= $this->getProcess($command);
        $process->run();
        if($process->isSuccessful()){
            return true;
        }else{
            throw new \Exception($process->getErrorOutput()." => mc run output: ".$process->getOutput()); 
        }
    }


    public function delete($path)
    {
        list($path)=$this->removeServerSymptom($path);
        $file_info=$this->has($path);
        if (!$file_info or trim($file_info["Type"])!="file")return false;
        $command= "mc rm ".$this->name."/".$this->bucket."/$path";
        $process= $this->getProcess($command) ;
        $process->run();
        if($process->isSuccessful()){
            return true;
        }else{
            throw new \Exception($process->getErrorOutput()." => mc run output: ".$process->getOutput()); 
        }
    }

    public function deleteDir($dirname)
    {
        list($dirname)=$this->removeServerSymptom($dirname);
        $file_info=$this->has($dirname);
        if (!$file_info or trim($file_info["Type"])!="folder")return false;
        $command= "mc rm ".$this->name."/".$this->bucket."/$dirname --recursive --force";
        $process= $this->getProcess($command) ;
        $process->run();
        if($process->isSuccessful()){
            return true;
        }else{
            throw new \Exception($process->getErrorOutput()." => mc run output: ".$process->getOutput()); 
        }
    }

    public function createDir($dirname, Config $config)
    {
        throw new NotImplementedException();
    }

    public function setVisibility($path, $visibility)
    {
        throw new NotImplementedException();
    }

    public function has($path): array|bool
    {
        list($path)=$this->removeServerSymptom($path);

        $patterns=[
            "Name"=>"/(?<=Name      : ).+(?=\n)/m",
            "Size"=>"/(?<=Size      : ).+(?=\n)/m",
            "Type"=>"/(?<=Type      : ).+(?=\n)/m"
        ];
        $path=preg_replace("/\/$/","",$path);
        $command="mc stat $this->name/$this->bucket/$path";
        $process= $this->getProcess($command);
        try  {$process->mustRun();}
        catch (ProcessFailedException $exception){return false;}
        $output=$process->getOutput();
        foreach ($patterns as $key=>$pattern){
            preg_match_all($pattern,$output,$res);
            $patterns[$key]=$res[0][0]??null;
        }
        $patterns["Name"]=pathinfo($patterns["Name"])["filename"];
        if (pathinfo($path)["filename"] !=  $patterns["Name"])return false;
        return $patterns;
    }

    public function read($path)
    {
        throw new NotImplementedException();
    }

    public function readStream($path)
    {
        throw new NotImplementedException();
    }

    public function listContents($directory = '', $recursive = false)
    {
        throw new NotImplementedException();
    }

    public function getMetadata($path)
    {
        throw new NotImplementedException();
    }

    public function getSize($path)
    {
        throw new NotImplementedException();
    }

    public function getMimetype($path)
    {
        throw new NotImplementedException();
    }

    public function getTimestamp($path)
    {
        throw new NotImplementedException();
    }

    public function getVisibility($path)
    {
        throw new NotImplementedException();
    }

    //upload directory and files
    public function putDir($local_path,$s3_path){

    }

    protected function  removeServerSymptom(...$path){
        $result=[];
        foreach ($path as $item){
            $result[]=preg_replace("/^".$this->server_symptom."/","",$item);
        }
        return $result;
    }

}
