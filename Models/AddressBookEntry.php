<?php

namespace Modules\Webmail\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Modules\Webmail\Models\AddressBook;

class AddressBookEntry extends Model {

    protected $table = 'address_books_entries';
    protected $fillable = [
        'addressbook_id',
        'name',
        'email',
        'phone',
        'organization',
        'address',
        'notes',
    ];

    public function addressBook() {
        return $this->belongsTo(AddressBook::class);
    }

}
