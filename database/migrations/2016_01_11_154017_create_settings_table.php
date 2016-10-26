<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use SET\Setting;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('name')->primary();
            $table->string('primary')->nullable();
            $table->string('secondary')->nullable();
            $table->timestamps();
        });

        //Address that receives our reports
        Setting::create(['name' => 'report_address', 'primary' => 'system', 'secondary' => 'system@system.com']);


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('settings');
    }
}
