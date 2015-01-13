# eloquent-external-storage

Adds external storage to an eloquent model. 

- Storage supports different drivers, currently `file` and `Amazon AWS S3` are implemented
- Different models can have different storage methods, configurations
- Storage drivers can be updated on the fly.

## Installation

### Add package as a composer dependency

In your `composer.json` file, include

```json
"require": {
        "inakianduaga/eloquent-external-storage" : "dev-master",
    },
```

and then run

`composer update --no-scripts inakianduaga/laravel-html-builder-extensions`

to install the package


## Configuration
 
Each model (class) that needs external storage must have a configuration set, controlled by model properties:
  
```php

class ActualModel extends InakiAnduaga\EloquentExternalStorage\Models\AbstractModelWithExternalStorage {

   /**
    * This is the path to the driver configuration that will be used for this model class, independently of other classes
    */
   protected static $storageDriverConfigPath;
    
    /**
     * Under what db field we store the content path/md5 for this model
     */
    protected $databaseFields = array(
        'contentPath' => 'content_path',
        'contentMD5' => 'content_md5',
    );
}
```

### Choosing / changing a storage driver dynamically 

If you want to switch storage drivers dynamically for a given model, you can do so by using the model's `setStorageDriver(StorageDriver $driver, $storageDriverConfigurationPath = null)` method. 
This will use the given driver with the chosen configuration path (or leave the current config path if `null`)

### Drivers configuration

The package provides placeholder configurations for the different included drivers. In the laravel installation root folder, run

`php artisan config:publish inakianduaga/eloquent-external-storage`

You can then modify the placeholder values in the files 
- Aws S3 driver: `app/config/packages/inakianduaga/eloquent-external-storage/awsS3.php` 
- File driver `app/config/packages/inakianduaga/eloquent-external-storage/file.php`

Note that you should set the models `$storageDriverConfigPath` property to point to `inakianduaga/eloquent-external-storage::awsS3` for the example above, when using S3.


## Usage:

- Simply extend `InakiAnduaga\EloquentExternalStorage\Models\AbstractModelWithExternalStorage` in your eloquent model (or use the trait `InakiAnduaga\EloquentExternalStorage\Models\ModelWithExternalStorageTrait` if you can't use class extension. 
   - In the extended model, set driver/database config properties (see above). 

- To attach/retrieve external content associated to a model
   - Use `setContent` method on the model to set the content (will be actually saved on update/save/creation)
   - Use `getContent` to retrieve the actual contents
