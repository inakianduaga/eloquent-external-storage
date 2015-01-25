<?php namespace InakiAnduaga\EloquentExternalStorage\Tests\Models;

use InakiAnduaga\EloquentExternalStorage\Tests\AbstractBaseDatabaseTestCase;
use InakiAnduaga\EloquentExternalStorage\Tests\Stubs\Models\TestModel;
use InakiAnduaga\EloquentExternalStorage\Drivers\File as FileDriver;
use Illuminate\Support\Facades\Config;

class ModelWithExternalStorageTest extends AbstractBaseDatabaseTestCase {

    private $fileStorageFolderRelativeToStoragePath = 'files';

    //-- Setup --//

    public function tearDown()
    {
        parent::tearDown();

        //Clean up file driver files after each test
        $this->cleanupFilesInFolder(storage_path() . DIRECTORY_SEPARATOR . $this->fileStorageFolderRelativeToStoragePath);
    }

    public function setUp()
    {
        parent::setUp();

        $this->refreshStorageDriver();

        $this->resetEvents();
    }

    //-----------//
    //-- Tests --//
    //-----------//

    public function testSetStorageDriverAndGetStorageDriverInstance()
    {
        $fileDriver = new FileDriver();

        TestModel::setStorageDriver($fileDriver);

        $storageInstance = TestModel::getStorageDriverInstance();

        $this->assertEquals(get_class($storageInstance), get_class($fileDriver));
    }

    public function testUpdateStorageDriver()
    {
        $configKey = 'foo/bar';

        //We first set a storage driver that will use default configuration, and check that config is indeed being used
        $fileDriver = new FileDriver();
        $configKey = $fileDriver->getConfigKey();
        TestModel::setStorageDriver($fileDriver);

        $this->assertEquals(TestModel::getStorageDriverInstance()->getConfigKey(), $configKey);

        //Update configuration & driver, which should update driver configuration automatically
        $this->mockStorageConfiguration($configKey, array('foo' => 'bar'));
        TestModel::setStorageDriver($fileDriver);

        $this->assertEquals(TestModel::getStorageDriverInstance()->getConfigKey(), $configKey);
    }

    public function testSetStorageDriverWithConfigPath()
    {
        $configKey = 'foo/bar';

        $fileDriver = new FileDriver();
        TestModel::setStorageDriver($fileDriver, $configKey);

        $this->assertEquals(TestModel::getStorageDriverInstance()->getConfigKey(), $configKey);
    }

    public function testCreateWithoutContent()
    {
        $model = $this->createModel();

        $this->assertEmpty($model->getContent());

        //TODO: Partial mock (Spy) on fileDriver and make sure the store method hasn't been called
    }

    public function testCreateWithContent()
    {
        //Random string as content
        $content = $this->generateRandomContent();

        $model = $this->createModel($content);

        //Check in memory content is ok
        $this->assertEquals($content, $model->getContent());

        //Fetch fresh model from db and retrieve "cold" content
        $storedContent = TestModel::get()->first()->getContent();

        //Check cold-stored content is ok
        $this->assertEquals($storedContent, $content);
    }

    /** @test */
    public function testModelUpdateWithContentChanges()
    {
        //Create model w/ content
        $model = $this->createModel('foo');

        //Update model's content
        $model->setContent('bar');

        //Inject spied storage driver (http://davedevelopment.co.uk/2014/10/09/mockery-spies.html)
        $storageDriverSpy = \Mockery::spy(FileDriver::class);
        $model->setStorageDriver($storageDriverSpy);

        //Model update & check storage driver store has been called
        $model->update(array('updated_at' => '2000-01-01 00:00:01'));
        $storageDriverSpy->shouldHaveReceived("store");
    }

    /** @test */
    public function testShouldDeleteContentWhenModelIsDeleted()
    {
        //Create model w/ content
        $model = $this->createModel('foo');
        $storagePath = $model->getPath();

        //Inject spied storage driver (http://davedevelopment.co.uk/2014/10/09/mockery-spies.html)
        $storageDriverSpy = \Mockery::spy(FileDriver::class);
        $model->setStorageDriver($storageDriverSpy);

        //Model update & check storage driver store has been called
        $model->delete();
        $storageDriverSpy->shouldHaveReceived("remove")->with($storagePath);
    }


    //---------------------//
    //-- Private Methods --//
    //---------------------//

    private function createModel($content = null)
    {
        $model = new TestModel();

        $model->setContent($content)->save();

        return $model;
    }


    /**
     * @param       $path
     * @param array $config
     */
    private function mockStorageConfiguration($path, array $config)
    {
        TestModel::setStorageDriverConfigurationPath($path);

        Config::set($path, $config);
    }

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

    private function generateRandomContent()
    {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 20);
    }

    /**
     * Sets a clean storage driver with default substorage path
     */
    private function refreshStorageDriver()
    {
        //Set new file driver as default storage driver
        TestModel::setStorageDriver(new FileDriver());

        //Fix storage folder path
        $this->mockStorageConfiguration('mocked.config.path', array(
            'storageSubfolder' => $this->fileStorageFolderRelativeToStoragePath
        ));
    }

    /**
     * Resets the eloquent creation/update/etc model bindings to work on consecutive tests
     *
     * http://stackoverflow.com/questions/17428050/laravel-4-model-events-dont-work-with-phpunit?rq=1
     */
    private function resetEvents()
    {
        TestModel::flushEventListeners();
        TestModel::boot();
    }

}