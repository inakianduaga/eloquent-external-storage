<?php namespace InakiAnduaga\EloquentExternalStorage\Drivers;

use InakiAnduaga\EloquentExternalStorage\Drivers\DriverInterface;
use InakiAnduaga\EloquentExternalStorage\Services\ExtensionGuesser;
use Illuminate\Config\Repository as ConfigService;
use Carbon\Carbon;

abstract class AbstractDriver implements DriverInterface {

    /**
     * @var ExtensionGuesser
     */
    protected $extensionGuesser;

    /**
     * @var ConfigService
     */
    protected $configService;

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
     * "Inject dependencies". We retrieve them manually from the IoC container instead of auto-injecting
     * because that way we don't need to pass the classes to the constructor on child classes
     */
    function __construct() {
        $this->extensionGuesser = app(ExtensionGuesser::class);
        $this->configService = app(ConfigService::class);
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
        return $this->configService->get($this->configKey.'.'.$key);
    }
}