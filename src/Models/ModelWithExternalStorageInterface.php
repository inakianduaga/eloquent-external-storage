<?php namespace InakiAnduaga\EloquentExternalStorage\Models;

use InakiAnduaga\EloquentExternalStorage\DriverInterface as StorageDriver;

/**
 * Requirements for a model to transparently provide external storage capabilities
 */
interface ModelWithExternalStorageInterface {

    /**
     * Eloquent event registration for storing/removing binded content transparently upon model creation/deletion
     *
     * @return void
     */
    public static function boot();

    /**
     * Sets the storage driver used by this model (class-level)
     *
     * @param StorageDriver $driver
     * @param null          $storageDriverConfigurationPath
     *
     * @return self
     */
    public static function setStorageDriver(StorageDriver $driver, $storageDriverConfigurationPath = null);

    /**
     * Sets the storage driver configuration path and updates the
     *
     * @param string $path
     */
    public static function setStorageDriverConfigurationPath($path);

    /**
     * Returns the current used storage driver instance
     *
     * @return StorageDriver
     */
    public static function getStorageDriverInstance();

    /**
     * Sets the model external content, and also syncs the md5 field automatically
     *
     * @param string $string
     *
     * @return self
     */
    public function setContent($string);

    /**
     * Retrieves the model external content, either from in-memory or from storage
     *
     * @return string
     */
    public function getContent();

    /**
     * Whether the model has "in-memory" content (content that needs to be saved to storage)
     *
     * @return boolean
     */
    public function hasInMemoryContent();

    /**
     * Returns the storage path
     *
     * @return string|null
     */
    public function getPath();

    /**
     * Sets the model storage path
     *
     * @param  string $path
     * @return self
     */
    public function setPath($path);

    /**
     * Fills the content_md5 field with the current content's md5
     *
     * @return self
     */
    public function syncContentMD5();


    /**
     * Determines whether the current (in memory, not stored) content matches the current md5 signature
     *
     * @return boolean
     */
    public function doesMD5MatchInMemoryContent();


}