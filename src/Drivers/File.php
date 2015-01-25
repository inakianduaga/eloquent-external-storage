<?php namespace InakiAnduaga\EloquentExternalStorage\Drivers;

use InakiAnduaga\EloquentExternalStorage\Drivers\AbstractDriver;

/**
 * File-based storage driver
 */
class File extends AbstractDriver {

    protected $directorySeparator = DIRECTORY_SEPARATOR;

    protected $configKey = 'inakianduaga/eloquent-external-storage::file';

    protected $subfolderByDate = false;

    public function fetch($path) {
        return file_get_contents($path);
    }

    public function store($content)
    {
        $relativePath = $this->generateStoragePath($content);
        $absolutePath = $this->getBaseStoragePath(). $this->directorySeparator . $relativePath;

        file_put_contents($absolutePath, $content);

        return $absolutePath;
    }

    public function remove($path)
    {
        unlink($path);
    }

    /**
     * The base storage path for the stored files
     *
     * @return string
     */
    private function getBaseStoragePath()
    {
        return storage_path().$this->directorySeparator.$this->getConfigRelativeKey('storageSubfolder');
    }

} 