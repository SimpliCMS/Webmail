<?php

namespace Modules\Webmail\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Webmail\Models\AddressBook;
use Modules\Webmail\Models\Setting;

class Account extends Model {

    protected $table = 'webmail_accounts';
    protected $fillable = [
        'email',
        'user_id'
    ];

    // Define relationships
//    public function settings() {
//        return $this->hasOne(Setting::class);
//    }
//
    public function addressBook() {
        return $this->hasMany(AddressBook::class);
    }

}
