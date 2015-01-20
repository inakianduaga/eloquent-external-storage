<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Example migration of fields used by the external storage model
 */
class UpdateExternalStorageModelTableExample extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('external_storage_model_table', function(Blueprint $table)
        {
            $table->string('content_path')->nullable();
            $table->string('content_md5', 32)->nullable();
            $table->string('storage_driver')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('external_storage_model_table', function(Blueprint $table)
        {
            $table->dropColumn('binary_path', 'binary_md5');
        });
    }

}
