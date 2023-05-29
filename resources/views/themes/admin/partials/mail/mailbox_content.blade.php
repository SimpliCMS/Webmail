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
                        <img src="{{ getBimiLogo($message->getFrom()[0]->mail, $message->getFrom()[0]->personal) }}" alt="Logo/Avatar" class="rounded-circle" style="width: 40px; margin-right: 10px;">
                        <div>
                            <div>{{ $message->subject }}</div>
                            <div>{{ $message->getFrom()[0]->mail }}</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-4">
                        <i class="far fa-clock me-2"></i>
                        @php
                        $messageDate = \Carbon\Carbon::parse($message->getDate());
                        $formattedDate = $messageDate->isToday() ? $messageDate->format('h:i A') : $messageDate->format('F j, Y h:i A');
                        @endphp
                        <span>{{ $formattedDate }}</span>
                        <i class="far fa-trash-alt ms-3"></i>
                    </div>
                </div>
            </a>
            @endforeach
            @else
            <p>No messages found</p>
            @endif

            @else
            <p>No folder selected</p>
            @endif
        </div>
    </div>
</div>

