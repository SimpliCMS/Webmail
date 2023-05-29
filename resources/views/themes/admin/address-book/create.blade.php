@extends('webmail-admin::layouts.address-book')

@section('content')
<div class="col-md-8">
    <div class="card">
        <div class="card-header">
            <h3>Create New Contact</h3>
        </div>
        <div class="card-body ps-3 pe-3 pb-3 pt-3">
            <form action="{{ route('webmail.address-book.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name') ?? $name }}">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email') ?? $email }}">
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="phone" id="phone" name="phone" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="organization" class="form-label">Organization</label>
                    <input type="text" id="organization" name="organization" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="street" class="form-label">Street</label>
                    <input type="text" class="form-control" id="street" name="street">
                </div>

                <div class="row mb-3">
                    <div class="col">
                        <label for="city" class="form-label">City</label>
                        <input type="text" class="form-control" id="city" name="city">
                    </div>
                    <div class="col">
                        <label for="state" class="form-label">State/Province</label>
                        <select name="state" id="state" class="form-control">
                            <option value="" selected>Select State/Province</option>
                            @foreach(\Konekt\Address\Models\ProvinceProxy::all() as $state)
                            <option value="{{ $state->code }}">{{ $state->name }}</option>
                            @endforeach
                        </select>

                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col">
                        <label for="zip" class="form-label">Zip/Postal</label>
                        <input type="text" class="form-control" id="zip" name="zip">
                    </div>
                    <div class="col">
                        <label for="country" class="form-label">Country</label>
                        <select name="country" id="country" class="form-control">
                            <option value="" selected>Select Country</option>
                            @foreach(\Konekt\Address\Models\CountryProxy::all() as $country)
                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control" name="notes" id="notes" rows="3"></textarea>
                </div>
                <!-- Add more fields as needed -->
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>
</div>
@endsection
