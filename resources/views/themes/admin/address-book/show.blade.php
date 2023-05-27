@extends('webmail-admin::layouts.address-book')

@section('content')
<div class="col-md-8">
    <div class="card">
        <div class="card-header">
            <h3>Contact Details</h3>
        </div>
        <div class="card-body">
            <h5>Name: {{ $entry->name }}</h5>
            <p>Email: {{ $entry->email }}</p>
            @php
                $address = json_decode($entry->address);
                $country = \Konekt\Address\Models\CountryProxy::where('id', $address->country)->first();
                $state = \Konekt\Address\Models\ProvinceProxy::where('code', $address->state)->first();
            @endphp
            <p>Street: {{ $address->street }}</p>
            <p>City: {{ $address->city }}</p>
            <p>State/Province: {{ $state ? $state->name : '' }}</p>
            <p>Zip/Postal: {{ $address->zip }}</p>
            <p>Country: {{ $country ? $country->name : '' }}</p>
            <p>Notes: {{ $entry->notes }}</p>
            
            <a href="{{ route('webmail.address-book.edit', $entry) }}" class="btn btn-primary">Edit</a>
            <form action="{{ route('webmail.address-book.destroy', $entry) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection


