<?php

namespace Modules\Webmail\Http\Controllers\Admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Webmail\Models\Account;
use Modules\Webmail\Models\AddressBook;
use Modules\Webmail\Models\AddressBookEntry;
use Modules\Core\Http\Controllers\Controller;

class AddressBookController extends Controller {

    public function index() {
        $account = Account::where('user_id', auth()->id())->firstOrFail();
        $addressBook = $account->addressBook()->first();

        return view('webmail-admin::address-book.index', compact('addressBook'));
    }

    public function create($email = null, $name = null) {
        return view('webmail-admin::address-book.create', compact('email', 'name'));
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
                // Add more validation rules as needed
        ]);

        $account = Account::where('user_id', auth()->id())->firstOrFail();
        $addressBook = $account->addressBook()->first();

        $address = [
            'street' => $request->input('street'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'country' => $request->input('country'),
            'zip' => $request->input('zip'),
        ];
        $entry = $addressBook->entries()->create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'organization' => $request->input('organization'),
            'address' => json_encode($address),
            'notes' => $request->input('notes'),
                // Add more fields as needed
        ]);

        return redirect()->route('webmail.address-book.show', $entry)->with('success', 'Address book entry created successfully.');
    }

    public function show(AddressBookEntry $entry) {
        return view('webmail-admin::address-book.show', compact('entry'));
    }

    public function edit(AddressBookEntry $entry) {
        return view('webmail-admin::address-book.edit', compact('entry'));
    }

    public function update(Request $request, AddressBookEntry $entry) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
                // Add more validation rules as needed
        ]);

        $address = [
            'street' => $request->input('street'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'country' => $request->input('country'),
            'zip' => $request->input('zip'),
        ];

        $entry->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'organization' => $request->input('organization'),
            'address' => json_encode($address),
            'notes' => $request->input('notes'),
                // Update more fields as needed
        ]);

        return redirect()->route('webmail.address-book.show', $entry)->with('success', 'Address book entry updated successfully.');
    }

    public function destroy(AddressBookEntry $entry) {
        $entry->delete();

        return redirect()->route('webmail.address-book.index')->with('success', 'Address book entry deleted successfully.');
    }

}
