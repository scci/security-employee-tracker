<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateForeignKeys extends Migration {

	public function up()
	{
		Schema::table('notes', function(Blueprint $table) {
			$table->foreign('user_id')->references('id')->on('users')
						->onUpdate('cascade')->onDelete('cascade');
		});
	}

	public function down()
	{
		Schema::table('notes', function(Blueprint $table) {
			$table->dropForeign('notes_user_id_foreign');
		});
	}
}