<?php

namespace Modules\Webmail\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Webmail\Models\AddressBookEntry;

class AddressBook extends Model {

    protected $table = 'address_books';
    protected $fillable = [
        'account_id',
    ];

    public function account() {
        return $this->belongsTo(Account::class);
    }

    public function entries() {
        return $this->hasMany(AddressBookEntry::class);
    }

}
