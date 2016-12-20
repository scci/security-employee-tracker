<?php

use Illuminate\Database\Migrations\Migration;

class InsertSystemUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        $user = DB::table('users')->where('username', 'system')->get();
        if (! $user) {
            \SET\User::create([
              'username'   => 'system',
              'first_name' => 'system',
              'last_name'  => 'system',
              'emp_num'    => 0,
              'email'      => 'system@test.com',
          ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        $user = DB::table('users')->where('username', 'system');
        if (!is_null($user)) {
            DB::table('users')->where('username', 'system')->delete();
        }
    }
}
