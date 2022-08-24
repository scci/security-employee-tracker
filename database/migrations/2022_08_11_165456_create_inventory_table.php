<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->increments('id');
            $table->string('material_control_number', 100)->unique();
            $table->integer('type_id')->unsigned()->nullable();
            $table->text('description')->nullable();
            $table->integer('classification_id')->unsigned();
            $table->string('manufacturer', 100)->nullable();
            $table->string('model_number', 100)->nullable();
            $table->string('serial_number', 100)->nullable();
            $table->string('asset_tag_number', 100)->nullable();
            $table->date('date_created');
            $table->date('date_into_inventory');
            $table->text('received_from');
            $table->integer('received_method_id')->unsigned();
            $table->text('tracking_number')->nullable();
            $table->integer('room_id')->unsigned()->nullable();
            $table->integer('safe_id')->unsigned()->nullable();
            $table->integer('drawer_id')->unsigned()->nullable();
            $table->string('bag_number', 100)->nullable();
            $table->string('machine_designation', 100)->nullable();
            $table->string('disposition', 100)->nullable();
            $table->date('disposition_date')->nullable();
            $table->string('last_inventory_date_and_initials', 100)->nullable();
            $table->text('notes')->nullable();
            $table->string('copy_number', 50)->nullable();
            $table->integer('number_of_copies')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('inventory');
    }
}
