<?php namespace InakiAnduaga\EloquentExternalStorage\Tests\Stubs\Models;

use InakiAnduaga\EloquentExternalStorage\Models\AbstractModelWithExternalStorage as BaseModel;

/**
 * Test model that implements the external storage
 */
class TestModel extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'external_storage_model_table';

    protected $guarded = array('id');
} 