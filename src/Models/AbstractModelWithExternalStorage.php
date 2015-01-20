<?php namespace InakiAnduaga\EloquentExternalStorage\Models;
//@codeCoverageIgnoreStart

use Illuminate\Database\Eloquent\Model as Eloquent;

abstract class AbstractModelWithExternalStorage extends Eloquent  implements ModelWithExternalStorageInterface {

    use ModelWithExternalStorageTrait;

} 