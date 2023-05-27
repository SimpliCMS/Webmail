<div class="card" id="mailbox-container">
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
                <div class="d-flex justify-content-between">
                    <div>{{ $message->subject }}</div>
                </div>
                <div>{{ $message->getFrom()[0]->mail }}</div>
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
