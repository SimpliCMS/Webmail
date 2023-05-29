@extends('webmail-admin::layouts.address-book')

@section('content')
<div class="col-md-8">
    <div class="card">
        <div class="card-header">
            <h3>Contacts</h3>
        </div>
        <div class="card-body">
            @if ($addressBook->entries->isEmpty())
            <p>No entries found.</p>
            @else
            <ul class="list-group">
                @foreach ($addressBook->entries as $entry)
                <li class="list-group-item d-flex align-items-center">
                    <img src="{{ getBimiLogo($entry->email, $entry->name) }}" alt="Avatar" class="rounded-circle me-3" style="width: 50px;">
                    <div>
                        <h5>{{ $entry->name }}</h5>
                        <p>{{ $entry->email }}</p>
                    </div>
                    <a href="{{ route('webmail.address-book.show', $entry) }}" class="ms-auto">
                        <i class="fas fa-eye"></i>
                    </a>
                </li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>
</div>
@endsection
