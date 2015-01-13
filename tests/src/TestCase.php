<?php namespace InakiAnduaga\EloquentExternalStorage\Tests;

use \Mockery as m;

class TestCase extends \Illuminate\Foundation\Testing\TestCase {

    /**
     * Creates the application.
     *
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        $unitTesting = true;
        $testEnvironment = 'testing';
        return require __DIR__.'/../start.php'; //creates a new application
    }


    /**
     * Clean mocks after test
     * @return void
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Mockery helper function to register the mocks for a class into the application
     *
     * @param  string $class is the class we want to mock
     * @return \Mockery\MockInterface
     */
    protected function mock($class)
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