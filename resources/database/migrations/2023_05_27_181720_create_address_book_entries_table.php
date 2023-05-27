<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressBookEntriesTable extends Migration {

    public function up() {
        Schema::create('address_books_entries', function (Blueprint $table) {
            $table->id();
            $table->intOrBigIntBasedOnRelated('address_book_id', Schema::connection(null), 'address_books.id');
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('organization')->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('address_book_id')
                    ->references('id')
                    ->on('address_books');
        });
    }

    public function down() {
        Schema::dropIfExists('address_books_entries');
    }

}
