<?php namespace InakiAnduaga\EloquentExternalStorage;

use InakiAnduaga\EloquentExternalStorage\Models\ModelWithExternalStorageInterface as Model;

/**
 * Storage operations API
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
     * @param  Model   $model
     * @param  string  $content  content to store
     * @return Model   with the updated storage path
     */
    public function store(Model $model, $content);

    /**
     * Deletes a given resource
     *
     * @param  string $path
     * @return bool
     */
    public function remove($path);

} 