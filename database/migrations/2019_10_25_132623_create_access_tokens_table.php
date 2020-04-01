<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccessTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('user_id')->unsigned();
            $table->boolean('sipr_issued')->default(false);
            $table->date('sipr_issue_date')->nullable();
            $table->date('sipr_expiration_date')->nullable();
            $table->date('sipr_return_date')->nullable();
            $table->boolean('cac_issued')->default(false);
            $table->date('cac_issue_date')->nullable();
            $table->date('cac_expiration_date')->nullable();
            $table->date('cac_return_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('access_tokens');
    }
}
