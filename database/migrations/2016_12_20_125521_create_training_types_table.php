<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTrainingTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_types', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->text('name');  // Type label
            $table->text('description')->nullable(); // Optional
            $table->boolean('sidebar')->default(0); // Layout position
            $table->boolean('status')->default(1); // Active status
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('training_types');
    }
}
