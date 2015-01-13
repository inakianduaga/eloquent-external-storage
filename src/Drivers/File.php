<?php namespace InakiAnduaga\EloquentExternalStorage\Drivers;

use InakiAnduaga\EloquentExternalStorage\Drivers\DriverInterface;
use InakiAnduaga\EloquentExternalStorage\Models\ModelWithExternalStorageInterface as Model;
use EloquentExternalStorage\Services\ExtensionGuesser;

/**
 * File-based storage
 */
class File implements DriverInterface {

    /**
     * The base filepath where emails are stored
     * @var string
     */
    private $baseStoragePath;

    /**
     * @var ExtensionGuesser
     */
    private $extensionGuesser;

    function __construct(ExtensionGuesser $extensionGuesser) {

        $this->extensionGuesser = $extensionGuesser;

        $this->baseStoragePath = storage_path().DIRECTORY_SEPARATOR.'model'; //TODO: Make this configurable
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

    public function store(Model $model, $content)
    {
        $relativePath = $this->generateStoragePath($content);
        $absolutePath = $this->baseStoragePath.DIRECTORY_SEPARATOR.$relativePath;

        file_put_contents($absolutePath, $content);

        return $model->setPath($relativePath);
    }

    public function remove($path)
    {
        $absolutePath = $this->baseStoragePath.$path;

        unlink($absolutePath);
    }

} 