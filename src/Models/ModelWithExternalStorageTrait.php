<?php namespace InakiAnduaga\EloquentExternalStorage\Models;

use Illuminate\Foundation\Application as App;
use InakiAnduaga\EloquentExternalStorage\DriverInterface as StorageDriver;
use InakiAnduaga\EloquentExternalStorage\Models\ModelWithExternalStorageInterface as Model;

/**
 * Adds external storage capabilities to any eloquent model
 * Uses a storage driver to do the storing. (an actual implementation such as file or AwsS3 needs to be binded to the IoC).
 */
trait ModelWithExternalStorageTrait {

    /**
     * Under what db field the path is stored for this model
     * Can be overriden by the actual model implementation
     */
    protected $binaryPathDBField = 'binary_path';

    /**
     * The model's external content
     * @var string
     */
    private $content;

    /**
     * Eloquent event registration for storing/removing binded content transparently upon model creation/deletion
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        /**
         * Creating Event
         *
         *  - Before creating the attachment, if the content is previously set, we will store it and update the path
         */
        App::make(static::class)->creating(function(Model $model)
        {
            if($model->hasContent()) {
                app(StorageDriver::class)->store($model, $model->getContent());
            }
        });

        /**
         * Deleting Event
         *
         *  - Before deleting the attachment, we need to remove the contents from storage
         */
        App::make(static::class)->deleting(function(Model $model)
        {
            if($model->hasContent()) {
                app(StorageDriver::class)->remove($model->getPath);
            }
        });
    }

    public function setContent($string)
    {
        $this->content = $string;

        return $this;
    }


    public function getContent()
    {
        return !empty($this->content) ? $this->content : app(StorageDriver::class)->fetch($this->getPath());
    }

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

}