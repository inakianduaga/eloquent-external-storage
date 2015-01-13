<?php namespace InakiAnduaga\EloquentExternalStorage\Drivers;

use InakiAnduaga\EloquentExternalStorage\DriverInterface;
use InakiAnduaga\EloquentExternalStorage\Services\ExtensionGuesser;
use Carbon\Carbon;

abstract class AbstractDriver implements DriverInterface {

    /**
     * @var ExtensionGuesser
     */
    protected $extensionGuesser;

    /**
     * Stores the driver's configuration key
     *
     * @var string
     */
    protected $configKey;

    /**
     * Storage's filesystem directory separator
     * @var string
     */
    protected $directorySeparator = '/';

    /**
     * @param ExtensionGuesser $extensionGuesser
     */
    function __construct(ExtensionGuesser $extensionGuesser) {
        $this->extensionGuesser = $extensionGuesser;
    }

    public function generateStoragePath($content)
    {
        $name = md5($content);
        $extension = $this->extensionGuesser($content);
        $subfolder = Carbon::now()->format('Y-m');
        $path = $subfolder. $this->directorySeparator .$name.'.'.$extension;

        return $path;
    }

    public function setConfigKey($key)
    {
        $this->configKey = $key;

        return $this;
    }

    /**
     * Returns a config key relative to the main driver configuration
     *
     * @param $key
     *
     * @return mixed
     */
    protected function getConfigRelativeKey($key)
    {
        return Config::get($this->configKey.'.'.$key);
    }
}