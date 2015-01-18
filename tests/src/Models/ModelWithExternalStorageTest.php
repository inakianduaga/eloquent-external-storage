<?php namespace InakiAnduaga\EloquentExternalStorage\Tests\Models;

use InakiAnduaga\EloquentExternalStorage\Tests\AbstractBaseDatabaseTestCase;
use InakiAnduaga\EloquentExternalStorage\Tests\Stubs\Models\TestModel;

class ModelWithExternalStorageTest extends AbstractBaseDatabaseTestCase {

    public function testDummy()
    {//
        $this->assertEquals(true, true);

        TestModel::create(array(
        ));
    }
}