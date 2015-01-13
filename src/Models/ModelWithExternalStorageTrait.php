<?php namespace InakiAnduaga\EloquentExternalStorage\Models;

use Illuminate\Foundation\Application as App;
use InakiAnduaga\EloquentExternalStorage\DriverInterface as StorageDriver;
use InakiAnduaga\EloquentExternalStorage\Models\ModelWithExternalStorageInterface as Model;

/**
 * Adds external storage capabilities to any eloquent model
 * Uses a storage driver to do the storing. (an actual implementation such as file or AwsS3 needs to be binded to the IoC).
 */
trait ModelWithExternalStorageTrait
{
    // ---------------
    // Configuration
    // ---------------

    /**
     * The storage driver configuration path
     *
     * @var string
     */
    protected static $storageDriverConfigPath;

    /**
     * Under what db field the path is stored for this model
     * Can be overriden by the actual model implementation
     *
     * @var string
     */
    protected $binaryPathDBField = 'binary_path';


    // ---------------
    // Properties
    // ---------------

    /**
     * The storageDriver service
     *
     * @var StorageDriver
     */
    protected static $storageDriver;

    /**
     * The model's external content
     * @var string
     */
    private $content;


    // ---------------
    // Events
    // ---------------

    /**
     * Eloquent event registration for storing/removing binded content transparently upon model creation/deletion
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::injectStorageDriver();

        $storageDriver = static::$storageDriver;

        /**
         * Creating Event
         *
         *  - Before creating the attachment, if the content is previously set, we will store it and update the path
         */
        App::make(static::class)->creating(function (Model $model) use ($storageDriver) {
            if ($model->hasContent()) {
                $storagePath = $storageDriver->store($model->getContent());
                $model->setPath($storagePath);
            }
        });

        /**
         * Updating event
         *
         *  - Before running a model update, if the content is previously set, we will run an update
         *  - TODO: IMPROVE THIS TO ACTUALLY SAVE CONTENT ONLY WHEN CONTENT HAS CHANGED
         */
        App::make(static::class)->creating(function (Model $model) use ($storageDriver) {
            if ($model->hasContent()) {
                $storagePath = $storageDriver->store($model->getContent());
                $model->setPath($storagePath);
            }
        });

        /**
         * Deleting Event
         *
         *  - Before deleting the attachment, we need to remove the contents from storage
         */
        App::make(static::class)->deleting(function (Model $model) use ($storageDriver) {
            if ($model->hasContent()) {
                $storageDriver->remove($model->getPath);
            }
        });
    }

    public static function setStorageDriverConfigurationPath($path)
    {
        static::$storageDriverConfigPath = $path;
    }

    public static function getStorageDriverInstance()
    {
        return static::$storageDriver;
    }

    // ---------------
    // Setter / Getters
    // ---------------

    public function setContent($string)
    {
        $this->content = $string;

        return $this;
    }


    public function getContent()
    {
        return !empty($this->content) ? $this->content : app(StorageDriver::class)->fetch($this->getPath());
    }


    // ---------------
    // Public API
    // ---------------

    public function hasContent()
    {
        return !is_null($this->content);
    }

    /**
     * Returns the storage path
     *
     * @return string|null
     */
    public function getPath()
    {
        return $this->{$this->binaryPathDBField};
    }

    /**
     * Sets the model storage path
     *
     * @param  string $path
     * @return self
     */
    public function setPath($path)
    {
        return $this->{$this->binaryPathDBField} = $path;
    }


    // ---------------------------
    // Private / Protected methods
    // ---------------------------

    /**
     * Inject storage driver and pass configuration
     */
    private static function injectStorageDriver()
    {
        static::$storageDriver = app(StorageDriver::class);
        static::$storageDriver->setConfigKey(static::$storageDriverConfigPath);
    }

}