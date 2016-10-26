<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttachmentsTable extends Migration {

	public function up()
	{
		Schema::create('attachments', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('filename');
			$table->string('mime');
			$table->integer('imageable_id')->unsigned();
            $table->string('imageable_type')->nullable();
            $table->boolean('encrypted')->default(false);
		});
	}

	public function down()
	{
		Schema::drop('attachments');
	}
}