<?php namespace InakiAnduaga\EloquentExternalStorage\Drivers;

use InakiAnduaga\EloquentExternalStorage\Drivers\DriverInterface;
use InakiAnduaga\EloquentExternalStorage\Models\ModelWithExternalStorageInterface as Model;


/**
 * File-based storage
 */
class File implements DriverInterface {

    protected $directorySeparator = DIRECTORY_SEPARATOR;

    protected $configKey = 'inakianduaga/eloquent-external-storage::file';

    /**
     * The base filepath where emails are stored
     * @var string
     */
    private $baseStoragePath;

    function __construct() {

        $this->baseStoragePath = storage_path().DIRECTORY_SEPARATOR.$this->getConfigRelativeKey('storageSubfolder');
    }

    public function generateStoragePath($content)
    {
        $name = md5($content);
        $extension = $this->extensionGuesser($content);
        $subfolder = Carbon::now()->format('Y-m');
        $path = $subfolder.'_'.$name.'.'.$extension;

        return $path;
    }

    public function fetch($path) {
        return file_get_contents($this->baseStoragePath.DIRECTORY_SEPARATOR.$path);
    }

    public function store($content)
    {
        $relativePath = $this->generateStoragePath($content);
        $absolutePath = $this->baseStoragePath.DIRECTORY_SEPARATOR.$relativePath;

        file_put_contents($absolutePath, $content);

        return $relativePath;
    }

    public function remove($path)
    {
        $absolutePath = $this->baseStoragePath.$path;

        unlink($absolutePath);
    }

} 