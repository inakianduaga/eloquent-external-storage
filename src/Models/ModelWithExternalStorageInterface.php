<?php namespace InakiAnduaga\EloquentExternalStorage\Models;

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
     * Sets the model external content
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
    public function hasContent();

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

}