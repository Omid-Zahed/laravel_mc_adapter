<?php

namespace Omidzahed\LaravelMcAdapter;
use League\Flysystem\Filesystem;

/**
 * @method McDriver getAdapter()
 */
class FilesystemMc extends Filesystem
{
    /**
     * @return
     */
    public function copy($path, $newpath){
      return  $this->getAdapter()->copy($path,$newpath);
    }


    public function rename($path, $newpath){
        return  $this->getAdapter()->move($path,$newpath);
    }



    public function has($path)
    {
        return $this->getAdapter()->has($path);
    }

    public function delete($path)
    {
     return  $this->getAdapter()->delete($path);
    }

    public function deleteDir($dirname)
    {
      return  $this->getAdapter()->deleteDir($dirname);
    }


}
