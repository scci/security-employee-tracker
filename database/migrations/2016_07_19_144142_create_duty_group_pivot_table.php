<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDutyGroupPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('duty_group', function (Blueprint $table) {
            $table->integer('duty_id')->unsigned()->index();
            $table->foreign('duty_id')->references('id')->on('duties')->onDelete('cascade');
            $table->integer('group_id')->unsigned()->index();
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->date('last_worked')->nullable();
            $table->primary(['duty_id', 'group_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('duty_group');
    }
}
