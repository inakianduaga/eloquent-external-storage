<?php namespace InakiAnduaga\EloquentExternalStorage\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;

use \Mockery as m;

/**
 * Sets up base test framework based on Orchestra tests
 */
abstract class AbstractBaseTestCase extends OrchestraTestCase {

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        // uncomment to enable route filters if your package defines routes with filters
        // $this->app['router']->enableFilters();
        // create an artisan object for calling migrations
//        $artisan = $this->app->make('artisan');

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

//        // call migrations specific to our tests, e.g. to seed the db
//        $artisan->call('migrate', array(
//            '--database' => 'testbench',
//            '--path'     => '../tests/migrations',
//        ));
    }

    public function tearDown()
    {
        parent::tearDown();

        m::close();
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
        $app['path.base'] = __DIR__ . '/../..';

        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', array(
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ));
    }

    /**
     * Get application paths. Use the preset from the Orchestral package but redefine the storage path
     *
     * @return array
     */
    protected function getApplicationPaths()
    {
        $basePath = __DIR__ . '/../..';

        $orchestralPaths = parent::getApplicationPaths();

        $orchestralPaths['storage'] = "{$basePath}/tests/storage";

        return $orchestralPaths;
    }

    /**
     * Get package providers.  At a minimum this is the package being tested, but also
     * would include packages upon which our package depends, e.g. Cartalyst/Sentry
     * In a normal app environment these would be added to the 'providers' array in
     * the config/app.php file.
     *
     * @return array
     */
    protected function getPackageProviders()
    {
        return array(
            //'Cartalyst\Sentry\SentryServiceProvider',
            //'YourProject\YourPackage\YourPackageServiceProvider',
        );
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
            //'Sentry'      => 'Cartalyst\Sentry\Facades\Laravel\Sentry',
            //'YourPackage' => 'YourProject\YourPackage\Facades\YourPackage',
        );
    }

    /**
     * Mockery helper function to register the mocks for a class into the application
     *
     * @param  string $class is the class we want to mock
     * @return \Mockery\MockInterface
     */
    protected function m($class)
    {
        $mock = m::mock($class);

        // Binding mock instance as class into the IoC container (for use with Laravel's auto-injection by type-hinting or anything that requests instances from the IoC container)
        $this->app->instance($class, $mock);

        return $mock;
    }

    /**
     * Helper method to mock a property for an eloquent model mock.
     *  - Eloquent has magic methods __set & __get so we can't just do $mock->property = value
     *  - This wouldn't be needed if we used repositories
     *
     * @param  Mockery $mock
     * @param  mixed $property
     * @param  mixed $value
     * @return Mockery
     */
    protected function mockEloquentProperty($mock, $property, $value)
    {
        return $mock
            ->shouldReceive('setAttribute')
            ->shouldReceive('getAttribute')
            ->with($property)
            ->andReturn($value);
    }


} 