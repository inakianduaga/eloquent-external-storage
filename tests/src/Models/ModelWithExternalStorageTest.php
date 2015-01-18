<?php namespace InakiAnduaga\EloquentExternalStorage\Tests\Models;

use InakiAnduaga\EloquentExternalStorage\Tests\AbstractBaseDatabaseTestCase;
use InakiAnduaga\EloquentExternalStorage\Tests\Stubs\Models\TestModel;
use InakiAnduaga\EloquentExternalStorage\Drivers\File as FileDriver;

class ModelWithExternalStorageTest extends AbstractBaseDatabaseTestCase {

    //-- Tests --//

    public function testCreateWithoutContent()
    {
        $model = $this->createModel();

        $this->assertEmpty($model->getContent());

        //TODO: Partial mock (Spy) on fileDriver and make sure the store method hasn't been called
    }

    public function testCreateWithContent()
    {
        $content = '12345';

        $model = $this->createModel($content);

        //Check in memory content is ok
        $this->assertEquals($content, $model->getContent());

        //Fetch model from db and retrieve "cold" content
        $storedContent = TestModel::get()->first()->getContent();

        $this->assertEquals($storedContent, $content);
    }

    public function testSetStorageDriverAndGetStorageDriverInstance()
    {
        $fileDriver = new FileDriver();

        TestModel::setStorageDriver($fileDriver);

        $storageInstance = TestModel::getStorageDriverInstance();

        $this->assertEquals(get_class($storageInstance), get_class($fileDriver));
    }


    //-- Private Methods --//

    private function createModel($content = null)
    {
        $model = new TestModel();

        $model->setContent($content)->save();

        return $model;
    }

}