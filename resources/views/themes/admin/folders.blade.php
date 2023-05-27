@extends('layouts.master')

@section('content')
    <div class="card">
        <div class="card-header">Email Folders</div>

        <div class="card-body">
            <ul>
               {{ $trashFolderPath }}
            </ul>
        </div>
    </div>
@endsection
