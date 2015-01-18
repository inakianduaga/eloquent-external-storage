<?php namespace InakiAnduaga\EloquentExternalStorage\Tests\Services;

use Illuminate\Support\Facades\App;
use InakiAnduaga\EloquentExternalStorage\Tests\AbstractBaseTestCase as BaseTestCase;
use InakiAnduaga\EloquentExternalStorage\Services\ExtensionGuesser;

class ExtensionGuesserTest extends BaseTestCase {

    /**
     * Stub files for different extensions
     *
     * @var array
     */
    private $stubPaths = array(
        'txt' => 'tests/src/Stubs/stub.txt',
        'pdf' => 'tests/src/Stubs/stub.pdf',
        'gif' => 'tests/src/Stubs/stub.gif',
        'jpeg' => 'tests/src/Stubs/stub.jpg',
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

        $this->extensionGuesser = App::make(ExtensionGuesser::class);
    }

    public function testGuess()
    {
        foreach($this->stubPaths as $expectedExtension => $stubPath) {
            $guessedExtension = $this->extensionGuesser->guess(file_get_contents(base_path($stubPath)));
            $this->assertEquals($expectedExtension, $guessedExtension);
        }
    }
}