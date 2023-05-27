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
Route::get('/mailbox/{folder?}', 'WebmailController@mailbox')->name('webmail.mailbox');
Route::get('/message/content', 'WebmailController@getMessageContent')->name('webmail.message.content');
Route::get('/mailbox/{folder?}/{messageId}', 'WebmailController@show')->name('webmail.show');
Route::post('/webmail/add-folder', 'WebmailController@addFolder')->name('webmail.addFolder');
Route::get('/compose/{folder?}', 'WebmailController@compose')->name('webmail.compose');
Route::get('/reply/{folder?}/{messageId}', 'WebmailController@reply')->name('webmail.reply');
Route::get('/forward/{folder?}/{messageId}', 'WebmailController@forward')->name('webmail.forward');
Route::post('/send', 'WebmailController@send')->name('webmail.send');
Route::post('/reply/{originalMessage}', 'WebmailController@sendReply')->name('webmail.sendReply');
Route::post('/move/{folder?}/{messageId}/{targetFolder}', 'WebmailController@move')->name('webmail.move');
Route::post('/trash/{folder?}/{messageId}', 'WebmailController@trash')->name('webmail.trash');
Route::post('/delete/{folder?}/{messageId}', 'WebmailController@delete')->name('webmail.delete');
});