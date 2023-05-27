@extends('webmail-admin::layouts.mail', ['folders' => $folders])

@section('content')

<div class="col-sm-12 col-md-4">
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

</div>
<div class="col-sm-12 col-md-6">
    <div class="card" id="message-content-placeholder">
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
        // Initial load of inbox mailbox
        //       loadMailbox('INBOX');

        // Handle sidebar mailbox clicks
        $('.sidebar').on('click', 'a', function (e) {
            e.preventDefault();
            var folder = $(this).attr('href').split('/').pop();
            loadMailbox(folder);
            $('.sidebar .list-group-item.active a').removeClass('text-white');

            // Remove active class from previously active list-group-item
            $('.sidebar .list-group-item.active').removeClass('active');

            // Add active class to the clicked list-group-item
            $(this).closest('.list-group-item').addClass('active');

            // Add text-white class to the clicked link
            $(this).addClass('text-white');
            $(this).closest('.list-group-item').find('.delete-link').addClass('text-white');
        });

        $('#mailbox-container').on('click', 'a', function (e) {
            e.preventDefault();
            var folder = $(this).data('folder-name');
            var messageId = $(this).data('message-id');
            loadMessage(folder, messageId);
            // Remove the 'active' class from all message links
            $('.message-link').removeClass('active');

            // Add the 'active' class to the clicked message link
            $(this).addClass('active');
        });
        // Function to load mailbox via AJAX
        function loadMailbox(folder) {
            $.ajax({
                url: '/admin/mail/mailbox/' + folder,
                type: 'GET',
                dataType: 'html',
                cache: false,
                success: function (response) {
                    $('#mailbox-container').html(response);
                    $('#message-content-placeholder').html('<div class="card" id="message-content-placeholder">\n\
                    <div class="card-header">Message</div>\n\
                    <div class="card-body">\n\
                    <p class="text-center" style="font-size: 30px;">Select a message to read</p>\n\
                    <p class="text-center text-primary" style="font-size: 150px;">\n\
                    <i class="fa-sharp fa-solid fa-envelopes-bulk"></i></p>\n\
                    </div>\n\
                    </div>');
                    // Update the browser URL without reloading the page
                    history.pushState(null, '', '/admin/mail/mailbox/' + folder);
                },
                error: function (xhr, status, error) {
                    console.error(error);
                }
            });
        }
        function loadMessage(folder, messageId) {
            var url = "{{ route('webmail.show', ['folder' => ':folder', 'messageId' => ':messageId']) }}";
            url = url.replace(':folder', folder); // Replace the placeholder with the actual folder name
            url = url.replace(':messageId', messageId); // Replace the placeholder with the actual message ID
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'html',
                cache: false,
                success: function (response) {
                    $('#message-content-placeholder').html(response);
                },
                error: function (xhr, status, error) {
                    // Handle the error case
                    console.error(xhr.responseText);
                }
            });
        }
    });
</script>
@endpush
@endsection
