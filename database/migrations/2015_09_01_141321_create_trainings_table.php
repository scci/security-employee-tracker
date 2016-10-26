<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTrainingsTable extends Migration {

	public function up()
	{
		Schema::create('trainings', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('name');
			$table->integer('renews_in')->unsigned()->nullable()->default('0');
			$table->text('description')->nullable();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('trainings');
	}
}