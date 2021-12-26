
#settings



```
//app.php
'providers' => [..., \Omidzahed\LaravelMcAdapter\McProvider::class]



//filesystems.php
'simple_disk' => [
             "alias"=>"main",
            'driver' => 'mc',
            "mc_path"=>'/usr/bin',
            'key' => "your key",
            'secret' => "your secret",
            'bucket' => "bucket name",
            'url' => "http://minio:9000",
            'endpoint' => "http://minio:9000",
            "auto_add_alias"=>false
        ]
```

#artisan command
```
 php artisan mc:set
```

#useage
```
    $minio=\Illuminate\Support\Facades\Storage::disk("minio");
    
    //s3 to local
      $minio->copy("~/download/aa/",__DIR__."/test")
      
    //s3 to s3 
    $minio->copy("~/download/aa/","~/main")
        
    // local to s3
    $minio->copy(__DIR__."/test","~/download/aa/")
    
    ```


