<?php

/*
|--------------------------------------------------------------------------
| Admin Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Admin web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" "auth" and "role" middleware groups to lock them to the Admin.
|
*/
Route::group(['prefix' => 'mail'], function () {
Route::get('/', function () {
    return redirect(url('admin/mail/mailbox/INBOX'));
})->name('webmail.index');
Route::get('/mailbox', function () {
    return redirect(url('admin/mail/mailbox/INBOX'));
})->name('webmail.mailbox.default');
Route::get('/mailbox/{folder?}', 'WebmailController@mailbox')->name('webmail.mailbox');
Route::get('/message/content', 'WebmailController@getMessageContent')->name('webmail.message.content');
Route::get('/mailbox/{folderName}/count', 'WebmailController@pollFolders')->name('webmail.pollFolders');
Route::get('/mailbox/{folder?}/{messageId}', 'WebmailController@show')->name('webmail.show');
Route::post('/webmail/add-folder', 'WebmailController@addFolder')->name('webmail.addFolder');
Route::post('/webmail/delete-folder/{targetFolder}', 'WebmailController@deleteFolder')->name('webmail.deleteFolder');
Route::get('/compose/{folder?}', 'WebmailController@compose')->name('webmail.compose');
Route::get('/reply/{folder?}/{messageId}', 'WebmailController@reply')->name('webmail.reply');
Route::get('/forward/{folder?}/{messageId}', 'WebmailController@forward')->name('webmail.forward');
Route::post('/send', 'WebmailController@send')->name('webmail.send');
Route::post('/reply/{originalMessage}', 'WebmailController@sendReply')->name('webmail.sendReply');
Route::post('/move/{folder?}/{messageId}/{targetFolder}', 'WebmailController@move')->name('webmail.move');
Route::post('/trash/{folder?}/{messageId}', 'WebmailController@trash')->name('webmail.trash');
Route::post('/delete/{folder?}/{messageId}', 'WebmailController@delete')->name('webmail.delete');


// View Address Book
Route::get('/contacts', 'AddressBookController@index')->name('webmail.address-book.index');
Route::get('/contacts/create/{email?}/{name?}', 'AddressBookController@create')->name('webmail.address-book.create');
Route::post('/contacts', 'AddressBookController@store')->name('webmail.address-book.store');
Route::get('/contacts/{entry}', 'AddressBookController@show')->name('webmail.address-book.show');
Route::get('/contacts/{entry}/edit', 'AddressBookController@edit')->name('webmail.address-book.edit');
Route::put('/contacts/{entry}', 'AddressBookController@update')->name('webmail.address-book.update');
Route::delete('/contacts/{entry}', 'AddressBookController@destroy')->name('webmail.address-book.destroy');

});