<?php

//Bootstraps the test environment

/*
|--------------------------------------------------------------------------
| Register The Composer Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for our application. We just need to utilize it! We'll require it
| into the script here so that we do not have to worry about the
| loading of any our classes "manually". Feels great to relax.
|
*/

require __DIR__.'/../../vendor/autoload.php';

//For some reasons the drivers folder is not added by composer automatically, so we require the files here manually
require_once("src/Drivers/DriverInterface.php");
require_once("src/Drivers/File.php");


/*
|--------------------------------------------------------------------------
| Loads test env variables
|--------------------------------------------------------------------------
|
| Except when the environment is flagged as CI, such as when building in Travis
*/

if(!getenv('CI')) {
    Dotenv::load(__DIR__);
}
