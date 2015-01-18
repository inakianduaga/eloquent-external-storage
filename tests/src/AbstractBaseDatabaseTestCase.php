<?php namespace InakiAnduaga\EloquentExternalStorage\Tests;

use InakiAnduaga\EloquentExternalStorage\Tests\AbstractBaseTestCase;
use InakiAnduaga\EloquentExternalStorage\Drivers\DriverInterface;
use InakiAnduaga\EloquentExternalStorage\Drivers\File as FileDriver;

/**
 * Adds database layer to base test case
 */
abstract class AbstractBaseDatabaseTestCase extends AbstractBaseTestCase {

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        // create an artisan object for calling migrations
        $artisan = $this->app->make('artisan');

        // call migrations specific to our tests, e.g. to create the db
        $artisan->call('migrate', array(
            '--database' => 'testbench',
            '--path'     => '../tests/src/Stubs/migrations',
        ));

        // call migrations that will be part of your package, assumes your migrations are in src/migrations
        // not neccessary if your package doesn't require any migrations to be run for
        // proper installation
        $artisan->call('migrate', [
            '--database' => 'testbench',
            '--path'     => 'migrations',
        ]);

        // call migrations for packages upon which our package depends, e.g. Cartalyst/Sentry
        // not necessary if your package doesn't depend on another package that requires
        // running migrations for proper installation
        /* uncomment as necessary
        $artisan->call('migrate', [
            '--database' => 'testbench',
            '--path'     => '../vendor/cartalyst/sentry/src/migrations',
        ]);
        */

        // call migrations that will be part of your package, assumes your migrations are in src/migrations
        // not neccessary if your package doesn't require any migrations to be run for
        // proper installation
        /* uncomment as neccesary
        $artisan->call('migrate', [
            '--database' => 'testbench',
            '--path'     => 'migrations',
        ]);
        */

//        require_once("src/Drivers/DriverInterface.php");
//        require_once("src/Drivers/File.php");

        $this->app->bind(DriverInterface::class, FileDriver::class);
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application    $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // reset base path to point to our package's src directory
        $app['path.base'] = __DIR__ . '/../../src';
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', array(
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ));
    }


    /**
     * Get package aliases.  In a normal app environment these would be added to
     * the 'aliases' array in the config/app.php file.  If your package exposes an
     * aliased facade, you should add the alias here, along with aliases for
     * facades upon which your package depends, e.g. Cartalyst/Sentry
     *
     * @return array
     */
    protected function getPackageAliases()
    {
        return array(
            'InakiAnduaga\EloquentExternalStorage\Drivers\DriverInterface' => FileDriver::class
        );
    }
}