<?php namespace InakiAnduaga\EloquentExternalStorage\Drivers;

use InakiAnduaga\EloquentExternalStorage\Drivers\DriverInterface;
use InakiAnduaga\EloquentExternalStorage\Models\ModelWithExternalStorageInterface as Model;


/**
 * File-based storage
 */
class File implements DriverInterface {

    protected $directorySeparator = DIRECTORY_SEPARATOR;

    protected $configKey = 'inakianduaga/eloquent-external-storage::file';

    public function fetch($path) {
        return file_get_contents($this->getBaseStoragePath().DIRECTORY_SEPARATOR.$path);
    }

    public function store($content)
    {
        $relativePath = $this->generateStoragePath($content);
        $absolutePath = $this->getBaseStoragePath().DIRECTORY_SEPARATOR.$relativePath;

        file_put_contents($absolutePath, $content);

        return $relativePath;
    }

    public function remove($path)
    {
        $absolutePath = $this->getBaseStoragePath().$path;

        unlink($absolutePath);
    }

    /**
     * The base storage path for the stored files
     *
     * @return string
     */
    private function getBaseStoragePath()
    {
        return storage_path().DIRECTORY_SEPARATOR.$this->getConfigRelativeKey('storageSubfolder');
    }

} 