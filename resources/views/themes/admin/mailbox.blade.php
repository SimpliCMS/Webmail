@extends('webmail-admin::layouts.mail', ['folders' => $folders])

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">Inbox</div>

            <div class="card-body">
                <div class="list-group" style="max-height: 725px; overflow-y: auto;">
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
                       class="list-group-item list-group-item-action message-link"
                       data-message-id="{{ $message->message_id }}">
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
    <div class="col-md-7">
        <div class="card" id="message-content-placeholder">
            <div class="card-header">Message</div>
            <div class="card-body">
                <p>Select a message to read</p>
            </div>
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
        });
    });


</script>
@endpush
@endsection


