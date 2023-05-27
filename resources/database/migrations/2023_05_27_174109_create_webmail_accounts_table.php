<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebmailAccountsTable extends Migration
{
    public function up()
    {
        Schema::create('webmail_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->intOrBigIntBasedOnRelated('user_id', Schema::connection(null), 'users.id');
            $table->timestamps();
            $table->foreign('user_id')
                    ->references('id')
                    ->on('users');
        });
    }

    public function down()
    {
        //
    }
}

