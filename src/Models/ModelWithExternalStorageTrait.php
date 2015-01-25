<?php namespace InakiAnduaga\EloquentExternalStorage\Models;

use Illuminate\Foundation\Application as App;
use InakiAnduaga\EloquentExternalStorage\Drivers\DriverInterface as StorageDriver;
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
     * Under what db field we store the content path/md5 for this model
     * Can be overriden by the actual model implementation
     *
     * @var string
     */
    protected $databaseFields = array(
        'contentPath' => 'content_path',
        'contentMD5' => 'content_md5',
        'storage_driver' => 'storage_driver',
    );


    // ---------------
    // Properties
    // ---------------

    /**
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
    protected static function boot()
    {
        parent::boot();

        static::injectStorageDriver();

        /**
         * Creating Event
         *
         *  - Before creating the attachment, if the content is previously set, we will store it and update the path
         */
        static::creating(function (Model $model) {
            if ($model->hasInMemoryContent()) {

                //Set the stored content's path
                $storagePath = $model->getStorageDriverInstance()->store($model->getContent());
                $model->setPath($storagePath);

                //Just in case somebody set the md5 manually to a wrong value, we resync it
                $model->syncContentMD5();

                //Set the value of the storage driver class
                $model->syncStorageDriverField();
            }
        });

        /**
         * Updating event
         *
         *  - Before running a model update, if the content is previously set, we will run an update
         */
        static::updating(function (Model $model) {
            if ($model->hasInMemoryContent() && !$model->doesMD5MatchInMemoryContent()) {

                //Set the stored content's path
                $storagePath = $model->getStorageDriverInstance()->store($model->getContent());
                $model->setPath($storagePath);

                //Sync new md5 value before updating
                $model->syncContentMD5();

                //Set the value of the storage driver class
                $model->syncStorageDriverField();
            }
        });

        /**
         * Deleting Event
         *
         *  - Before deleting the attachment, we need to remove the contents from storage
         *  - TODO: Clean up content path, storage driver fields upon deletion
         */
        static::deleting(function (Model $model) {
            if ($model->hasInMemoryContent()) {
                $model->getStorageDriverInstance()->remove($model->getPath());
            }
        });
    }

    public static function setStorageDriverConfigurationPath($path)
    {
        static::$storageDriverConfigPath = $path;

        static::getStorageDriverInstance()->setConfigKey($path);
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

        $this->syncContentMD5();

        return $this;
    }


    public function getContent()
    {
        //If there is no in-memory content and there is no content path, the content must be null
        //Otherwise, get content from in-memory or cold storage
        if(!$this->hasInMemoryContent() && empty($this->getPath())) {
            return null;
        } else {
            return !empty($this->content) ? $this->content : app(StorageDriver::class)->fetch($this->getPath());
        }
    }


    // ---------------
    // Public API
    // ---------------

    public function hasInMemoryContent()
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
        return $this->{$this->databaseFields['contentPath']};
    }

    /**
     * Sets the model storage path
     *
     * @param  string $path
     * @return self
     */
    public function setPath($path)
    {
        return $this->{$this->databaseFields['contentPath']} = $path;
    }


    public function syncContentMD5()
    {
        if(!empty($this->content)) {
            $this->{$this->databaseFields['contentMD5']} = md5($this->content);
        } else {
            $this->{$this->databaseFields['contentMD5']} = null;
        }

        return $this;
    }


    public static function setStorageDriver(StorageDriver $driver, $storageDriverConfigurationPath = null)
    {
        static::$storageDriver = $driver;

        if(!empty($storageDriverConfigurationPath)) {
            static::setStorageDriverConfigurationPath($storageDriverConfigurationPath);
        }
    }


    public function syncStorageDriverField()
    {
        $this->{$this->databaseFields['storage_driver']} = static::getStorageDriverInstanceClass();
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

    /**
     * Determines whether the current (in memory, not stored) content matches the current md5 signature
     *
     * @return boolean
     */
    private function doesMD5MatchInMemoryContent()
    {
        return $this->{$this->databaseFields['contentMD5']} == md5($this->content) && !$this->isDirty($this->databaseFields['contentMD5']);
    }

    /**
     * The class name of the current storage driver
     * @return null|string
     */
    private static function getStorageDriverInstanceClass()
    {
        $driver = static::getStorageDriverInstance();

        return $driver !== null ? get_class($driver) : null;
    }
}