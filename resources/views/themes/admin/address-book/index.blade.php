@extends('webmail-admin::layouts.address-book')

@section('content')
<div class="col-md-8">
    <div class="card">
        <div class="card-header">
            <h3>Address Book</h3>
        </div>
        <div class="card-body">
            @if ($addressBook->entries->isEmpty())
            <p>No entries found.</p>
            @else
            <ul class="list-group">
                @foreach ($addressBook->entries as $entry)
                <li class="list-group-item">
                    <a href="{{ route('webmail.address-book.show', $entry) }}">{{ $entry->name }}</a>
                </li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>
</div>
@endsection


