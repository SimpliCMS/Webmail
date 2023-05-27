@extends('webmail-admin::layouts.mail', ['folders' => $folders])

@section('content')

<div class="col-sm-12 col-md-4">
    <div class="card">
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
                   data-message-id="{{ $message->getUid() }}">
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

</div>
<div class="col-sm-12 col-md-6">
    <div class="card" id="message-content-placeholder" style="">
        <div class="card-header">Message</div>
        <div class="card-body">
            <p class="text-center" style="font-size: 30px;">Select a message to read</p>
            <p class="text-center text-primary" style="font-size: 150px;"><i class="fa-sharp fa-solid fa-envelopes-bulk"></i></p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function () {
        $('.message-link').click(function () {
            var messageId = $(this).data('message-id');
            var folder = '{{ $selectedFolder->name }}'; // Retrieve the current folder name
            var url = "{{ route('webmail.show', ['folder' => ':folder', 'messageId' => ':messageId']) }}";
            url = url.replace(':folder', folder); // Replace the placeholder with the actual folder name
            url = url.replace(':messageId', messageId); // Replace the placeholder with the actual message ID

            // Send an AJAX request to load the message content
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'html',
                success: function (response) {
                    $('#message-content-placeholder').html(response);
                },
                error: function (xhr, status, error) {
                    // Handle the error case
                    console.error(xhr.responseText);
                }
            });

            // Remove the 'active' class from all message links
            $('.message-link').removeClass('active');

            // Add the 'active' class to the clicked message link
            $(this).addClass('active');
        });
    });
</script>
@endpush
@endsection
