eloquent-external-storage
=========================

[![Build Status][travis-image]][travis-url] [![Coverage Status][coveralls-image]][coveralls-url] [![Code Climate][code-climate-image]][code-climate-url] [![HHVM Status][hhvm-status-image]][hhvm-status-url] [![Dependency Status][dependency-status-image]][dependency-status-url]

> Adds external storage capabilities to an eloquent model.

## Features

- Save/retrieve model-related content to an external storage using `setContent()`, `getContent()` methods on the eloquent model.
  Now you can move all that binary data out of the database without having to move a finger.
- Storage supports different drivers, currently `file` and `Amazon AWS S3` are implemented
- Different models can implement different storage drivers, separate configurations
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

### Database configuration

The storage driver needs three additional fields, where it stores the content path, an md5 checksum of the contents,
and the driver class used to store the contents.

- A migration example is provided under `src/migration`.
- The database field names can be custom-named, simply modify the model's $databaseFields property

```php

class ActualModel extends InakiAnduaga\EloquentExternalStorage\Models\AbstractModelWithExternalStorage {

    /**
     * Under what db field we store the content path/md5/storage_driver_class for this model
     */
    protected $databaseFields = array(
        'contentPath' => 'content_path',
        'contentMD5' => 'content_md5',
        'storage_driver' => 'storage_driver',
    );
    
}
```

## Configuration
 
Each model (class) that needs external storage must have a configuration set, controlled by model properties:
  
```php

class ActualModel extends InakiAnduaga\EloquentExternalStorage\Models\AbstractModelWithExternalStorage {

   /**
    * This is the path to the driver configuration that will be used for this model class, independently of other classes
    */
   protected static $storageDriverConfigPath;    
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


## Development & Testing

### Requirements

- Install `node` & `npm`, afterwards run `npm install` to install development tools
- Install `sqlite` driver for running tests

   - **Ubuntu**: `sudo apt-get install sqlite3 libsqlite3-dev` and afterwards `sudo apt-get install php5-sqlite`

### Tools
There are several commands related to testing

- `gulp test` : runs unit tests once
- `gulp tdd`  : runs unit tests continuously once changes are detected

Both options have the ability to generate a code coverage report in different formats. When this is selected, a local server will be launched to visualize the coverage report

### Testing AWS S3 Driver

- **Locally:** Valid AWS S3 credentials can be provided via `tests/.env` file, see `tests/.env.example` as an example. If no credentials are set, the AWS S3 tests will be skipped
- **CI**: Travis CI integration is performed by passing encrypted credentials in travis.yml (valid only for inakianduaga/eloquent-external-storage repo).
- [Amazon S3 testing in Travis CI](http://milesj.me/blog/read/amazon-s3-testing-travis-ci)

### Tests Coverage report

- `gulp test --generateCoverage=coverageHtml` to generate html code coverage report (under `./coverage` folder)

## Documentation generation

- The package code documentation can be generated by running `gulp docs`, which generates the docs in the `./docs` folder


[travis-url]: https://travis-ci.org/inakianduaga/eloquent-external-storage
[travis-image]: https://travis-ci.org/inakianduaga/eloquent-external-storage.svg?branch=master

[coveralls-url]: https://coveralls.io/r/inakianduaga/eloquent-external-storage
[coveralls-image]: https://coveralls.io/repos/inakianduaga/eloquent-external-storage/badge.svg?branch=master

[code-climate-url]: https://codeclimate.com/github/inakianduaga/eloquent-external-storage
[code-climate-image]: https://codeclimate.com/github/inakianduaga/eloquent-external-storage/badges/gpa.svg

[dependency-status-url]: https://gemnasium.com/inakianduaga/eloquent-external-storage
[dependency-status-image]: https://gemnasium.com/inakianduaga/eloquent-external-storage.svg

[hhvm-status-url]: http://hhvm.h4cc.de/package/inakianduaga/eloquent-external-storage
[hhvm-status-image]: http://hhvm.h4cc.de/badge/inakianduaga/eloquent-external-storage.svg

