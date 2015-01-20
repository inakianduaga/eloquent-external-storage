<?php //@codeCoverageIgnoreStart

namespace InakiAnduaga\EloquentExternalStorage\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

abstract class AbstractModelWithExternalStorage extends Eloquent  implements ModelWithExternalStorageInterface {

    use ModelWithExternalStorageTrait;

}