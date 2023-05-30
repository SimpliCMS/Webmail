@extends('webmail-admin::layouts.mail', ['folders' => $folders])

@section('content')

<div class="col-sm-12 col-md-4">
    <div id="mailbox-container">
        <div class="card" id="mailbox-container-content">
            <div class="card-header">{{ ucfirst(strtolower($selectedFolder->name)) }}</div>

            <div class="card-body">
                <div class="list-group" style="max-height: 755px; overflow-y: auto;">
                    @if($selectedFolder)
                    @php
                    // Sort the messages based on the date in descending order
                    $sortedMessages = $messages->sortByDesc(function ($message) {
                    return $message->getDate();
                    });
                    @endphp

                    @if($sortedMessages->count() > 0)
                    @foreach ($sortedMessages as $message)
                    <a href="javascript:void(0);"
                       class="list-group-item list-group-item-action message-link {{ $activeMessageId == $message->getUid() ? 'active' : '' }}"
                       data-message-id="{{ $message->getUid() }}" data-folder-name="{{ $selectedFolder->name }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex">
                                <i class="far fa-envelope {{ ($message->hasFlag('\\Seen') && $activeMessageId == $message->getUid()) ? 'text-white' : ($message->hasFlag('\\Seen') ? 'seen text-muted' : 'unseen text-primary') }} me-2" data-message-id="{{ $message->getUid() }}"></i>
                                <div class="avatar-container"><img src="{{ getLogo($message->getFrom()[0]->mail, $message->getFrom()[0]->personal) }}" alt="Logo/Avatar" class="avatar" style="margin-right: 10px;"></div>
                                <div>
                                    <div>{{ Str::limit($message->subject, 40) }}</div>
                                    <div>{{ $message->getFrom()[0]->mail }}</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-4">
                                <i class="far fa-clock me-2"></i>
                                @php
                                $messageDate = \Carbon\Carbon::parse($message->getDate());
                                $formattedDate = $messageDate->isToday() ? $messageDate->format('h:i A') : $messageDate->format('F j');
                                if ($messageDate->isCurrentYear()) {
                                $formattedDate .= ', ' . $messageDate->format('h:i A');
                                } else {
                                $formattedDate .= ', ' . $messageDate->format('Y h:i A');
                                }
                                @endphp
                                <span>{{ $formattedDate }}</span>
                                <i class="far fa-trash-alt ms-3"></i>
                            </div>
                        </div>
                    </a>
                    @endforeach
                    @else
                    <p class="ps-2">No messages found</p>
                    @endif

                    @else
                    <p class="ps-2">No folder selected</p>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
<div class="col-sm-12 col-md-6">
    <div id="message-content-placeholder">
        <div class="card" id="message-content">
            <div class="card-header">Message</div>
            <div class="card-body">
                <p class="text-center" style="font-size: 30px;">Select a message to read</p>
                <p class="text-center text-primary" style="font-size: 150px;"><i class="fa-solid fa-envelope-open-text"></i></p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ route('webmail.script.mailbox') }}"></script>
@endpush
@endsection
