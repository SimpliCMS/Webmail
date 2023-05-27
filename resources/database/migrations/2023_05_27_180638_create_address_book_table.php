<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressBookTable extends Migration {

    public function up() {
        Schema::create('address_books', function (Blueprint $table) {
            $table->id();
            $table->intOrBigIntBasedOnRelated('account_id', Schema::connection(null), 'webmail_accounts.id');
            $table->timestamps();
            $table->foreign('account_id')
                    ->references('id')
                    ->on('webmail_accounts');
        });
    }

    public function down() {
        //
    }

}
