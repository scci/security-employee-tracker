<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrainingUserPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('training_id')->unsigned()->index();
            $table->foreign('training_id')->references('id')->on('trainings')->onDelete('cascade');
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('author_id');
            $table->date('due_date');
            $table->date('completed_date')->nullable()->default(null);
            $table->text('comment')->nullable();
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
        Schema::drop('training_user');
    }
}
