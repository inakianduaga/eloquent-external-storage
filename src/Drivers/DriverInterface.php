<?php namespace InakiAnduaga\EloquentExternalStorage\Drivers;
//@codeCoverageIgnoreStart

/**
 * Storage driver API
 */
interface DriverInterface {

    /**
     * Generates the storage path for the given model
     *
     * @param  string  $content The content for the externally-binded property
     * @return string
     */
    public function generateStoragePath($content);

    /**
     * Retrieves the contents for a given storage path
     *
     * @param  string $path
     * @return string
     */
    public function fetch($path);

    /**
     * Stores external content for the given model
     *
     * @param  string  $content  content to store
     * @return array with the stored path, binary md5
     */
    public function store($content);

    /**
     * Deletes a given resource
     *
     * @param  string $path
     * @return bool
     */
    public function remove($path);


    /**
     * Sets the key used for retrieving the driver configuration
     *
     * @param string $key
     *
     * @return self
     */
    public function setConfigKey($key);

    /**
     * Retrieves the driver's instance config key
     *
     * @return string
     */
    public function getConfigKey();

} 