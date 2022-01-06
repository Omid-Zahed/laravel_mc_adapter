# install
```php
composer require omidzahed/laravel_mc_adapter @dev
```
# settings
```php
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

# artisan command
```
 php artisan mc:set
 php artisna mc:test
```

# useage
```php
    $minio=\Illuminate\Support\Facades\Storage::disk("minio");
    $path_local=new Path(Path::$LOCAL,__DIR__."/dd.txt");
    $path_remote=new Path(Path::$REMOTE,"/omid/dd.txt");

    //s3 to local
    $minio->move($path_remote,$path_local);

      
    //s3 to s3 
    $minio->move($path_remote,$path_path_remote);

    // local to s3
    $minio->move($path_local,$path_remote);
    
    ```


