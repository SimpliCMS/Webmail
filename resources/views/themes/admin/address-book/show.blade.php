@extends('webmail-admin::layouts.address-book')

@section('content')
<div class="col-md-8">
    <div class="card">
        <div class="card-header">
            <h3 class="text-center">Contact Details</h3>
        </div>
        <div class="card-body">
            <div class="text-center">
                <img src="{{ getLogo($entry->email, $entry->name) }}" alt="Logo/Avatar" class="rounded-circle" style="width: 200px;">
            </div>
            <h6>General Details</h6>
            <h5 class="mt-4">Name: {{ $entry->name }}</h5>
            <p><i class="fas fa-envelope"></i> Email: {{ $entry->email }}</p>
            @php
            $address = json_decode($entry->address);
            $country = \Konekt\Address\Models\CountryProxy::where('id', $address->country)->first();
            $state = \Konekt\Address\Models\ProvinceProxy::where('code', $address->state)->first();
            @endphp
            <hr>
            <h6>Address</h6>
            <p><i class="fas fa-map-marker-alt"></i> Street: {{ $address->street }}</p>
            <p><i class="fas fa-city"></i> City: {{ $address->city }}</p>
            <p><i class="fas fa-map-marked-alt"></i> State/Province: {{ $state ? $state->name : '' }}</p>
            <p><i class="fas fa-mail-bulk"></i> Zip/Postal: {{ $address->zip }}</p>
            <p><i class="fas fa-globe"></i> Country: {{ $country ? $country->name : '' }}</p>

            <hr>

            <h6>Notes</h6>
            <p><i class="fas fa-sticky-note"></i> Notes: {{ $entry->notes }}</p>

            <div class="text-center">
                <a href="{{ route('webmail.address-book.edit', $entry) }}" class="btn btn-primary">Edit</a>
                <form action="{{ route('webmail.address-book.destroy', $entry) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection


