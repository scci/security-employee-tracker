<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration
{

    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('username')->unique();
            $table->smallInteger('emp_num')->nullable();
            $table->string('first_name');
            $table->string('nickname')->nullable();
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('jpas_name')->nullable();
            $table->string('status')->default('active');
            $table->string('clearance')->nullable();
            $table->date('elig_date')->nullable();
            $table->string('inv')->nullable();
            $table->date('inv_close')->nullable();
            $table->date('destroyed_date')->nullable();
            $table->string('role')->nullable();
            $table->integer('supervisor_id')->nullable();
            $table->string('access_level')->nullable();
            $table->rememberToken('rememberToken')->nullable();
            $table->datetime('last_logon')->nullable();
            $table->string('ip')->nullable();
            $table->string('password')->nullable();
        });
        \SET\User::create([
            'username' => 'system',
            'first_name' => 'system',
            'last_name' => 'system',
            'emp_num' => 0,
            'email' => 'system@test.com'
        ]);
    }

    public function down()
    {
        Schema::drop('users');
    }
}