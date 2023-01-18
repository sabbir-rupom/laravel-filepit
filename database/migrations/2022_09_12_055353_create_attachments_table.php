<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->string('added_by')->default('admin');
            $table->integer('user_id')->unsigned()->default(0);
            $table->string('type')->default('image')->comment('values: image, pdf, doc, video etc.');
            $table->string('caption')->nullable();
            $table->string('alt_text')->nullable();
            $table->string('file_path')->comment('file storage path');
            $table->string('file_name')->comment('original file name of uploaded file');
            $table->string('file_mime')->nullable();
            $table->integer('file_size')->default(0)->comment('Size in kilobyte');
            $table->string('thumb_image')->nullable()->comment('if file is image save thumnail format of the transformed image path');
            $table->string('external_link')->nullable()->comment('File from external source');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attachments');
    }
}