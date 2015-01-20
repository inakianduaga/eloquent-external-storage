<?php namespace InakiAnduaga\EloquentExternalStorage\Tests\Services;

use InakiAnduaga\EloquentExternalStorage\Tests\AbstractBaseTestCase as BaseTestCase;
use InakiAnduaga\EloquentExternalStorage\Services\ExtensionGuesser;

class ExtensionGuesserTest extends BaseTestCase {

    /**
     * Stub files for different extensions
     *
     * @var array
     */
    private $stubPaths = array(
        'txt' => 'tests/src/Stubs/extensions/stub.txt',
        'pdf' => 'tests/src/Stubs/extensions/stub.pdf',
        'gif' => 'tests/src/Stubs/extensions/stub.gif',
        'jpeg' => 'tests/src/Stubs/extensions/stub.jpg',
    );

    /**
     * @var ExtensionGuesser
     */
    private $extensionGuesser;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->extensionGuesser = $this->app->make(ExtensionGuesser::class);
    }

    public function testGuess()
    {
        foreach($this->stubPaths as $expectedExtension => $stubPath) {
            $guessedExtension = $this->extensionGuesser->guess(file_get_contents(base_path($stubPath)));
            $this->assertEquals($expectedExtension, $guessedExtension);
        }
    }
}