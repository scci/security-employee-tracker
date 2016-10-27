<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNotesTable extends Migration {

	public function up()
	{
		Schema::create('notes', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('author_id')->unsigned();
            $table->string('title');
            $table->text('comment')->nullible();
            $table->boolean('alert')->default(false);
            $table->boolean('private')->default(false);
            $table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('notes');
	}
}
