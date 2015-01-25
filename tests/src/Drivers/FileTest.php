<?php namespace InakiAnduaga\EloquentExternalStorage\Tests\Drivers;

use InakiAnduaga\EloquentExternalStorage\Tests\AbstractBaseTestCase as BaseTestCase;
use InakiAnduaga\EloquentExternalStorage\Drivers\File as FileDriver;
use Illuminate\Support\Facades\Config;

class FileTest extends BaseTestCase {

    private $fileStorageFolderRelativeToStoragePath = 'files';

    /**
     * Content we add on setup
     * @var string
     */
    private $content = 'foo';

    /**
     * Stored path on setup
     * @var string
     */
    private $storedPath;

    //-----------//
    //-- Setup --//
    //-----------//

    /**
     * @var FileDriver
     */
    private $fileStorageDriver;

    public function setUp()
    {
        parent::setUp();

        $this->refreshDriver();

        $this->storedPath = $this->fileStorageDriver->store($this->content);
    }

    public function tearDown()
    {
        parent::tearDown();

        //Clean up file driver files after each test
        $this->cleanupFilesInFolder(storage_path() . DIRECTORY_SEPARATOR . $this->fileStorageFolderRelativeToStoragePath);
    }


    //-----------//
    //-- Tests --//
    //-----------//

    public function testStoresContent()
    {
        $this->assertTrue(is_file($this->storedPath));

        $this->assertEquals($this->content, file_get_contents($this->storedPath));
    }

    public function testFetchContent()
    {
        $this->assertEquals($this->content, $this->fileStorageDriver->fetch($this->storedPath));
    }

    public function testRemoveContent()
    {
        $this->assertTrue(is_file($this->storedPath));
        $this->fileStorageDriver->remove($this->storedPath);
        $this->assertFalse(is_file($this->storedPath));
    }


    //---------------------//
    //-- Private Methods --//
    //---------------------//


    /**
     * http://stackoverflow.com/questions/4594180/deleting-all-files-from-a-folder-using-php
     *
     * @param string $path
     */
    private function cleanupFilesInFolder($path)
    {
        $files = glob($path.'/*'); // get all file names

        foreach($files as $file){ // iterate files
            if(is_file($file))
                unlink($file); // delete file
        }
    }

    /**
     * Sets a clean storage driver with default substorage path
     */
    private function refreshDriver()
    {
        // Inject new file driver and mock config
        $this->fileStorageDriver = new FileDriver;
        $this->fileStorageDriver->setConfigKey('mocked.config.key');

        Config::set('mocked.config.key', array(
            'storageSubfolder' => $this->fileStorageFolderRelativeToStoragePath
        ));
    }

}