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
                                <img src="{{ getBimiLogo($message->getFrom()[0]->mail, $message->getFrom()[0]->personal) }}" alt="Logo/Avatar" class="rounded-circle" style="width: 40px; margin-right: 10px;">
                                <div>
                                    <div>{{ $message->subject }}</div>
                                    <div>{{ $message->getFrom()[0]->mail }}</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-4">
                                <i class="far fa-clock me-2"></i>
                                <!-- Replace with message time -->
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
<script>
    $(document).ready(function () {

        // Handle sidebar mailbox clicks
        $('.sidebar').on('click', 'a', function (e) {
            e.preventDefault();
            var folder = $(this).attr('href').split('/').pop();
            loadMailbox(folder);
            $('.sidebar .list-group-item.active a').removeClass('text-white');
            $('.sidebar .list-group-item.active button').removeClass('text-white');

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

            // Remove the 'text-primary' and 'text-white' classes from all unseen messages
            $('.unseen').removeClass('text-primary text-white');

            // Add the appropriate class to the clicked message's unseen element based on its status
            if ($(this).find('.unseen').hasClass('text-muted')) {
                // If the clicked message is seen, add 'text-muted'
                $(this).find('.unseen').addClass('text-muted');
            } else {
                // If the clicked message is unseen, add 'text-primary' or 'text-white' based on the active state
                if ($(this).hasClass('active')) {
                    $(this).find('.unseen').addClass('text-white');
                } else {
                    $(this).find('.unseen').addClass('text-primary');
                }
            }
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
                    $('#message-content-placeholder').html('<div class="card" id="message-content">\n\
          <div class="card-header">Message</div>\n\
          <div class="card-body">\n\
          <p class="text-center" style="font-size: 30px;">Select a message to read</p>\n\
          <p class="text-center text-primary" style="font-size: 150px;">\n\
          <i class="fa-solid fa-envelope-open-text"></i></p>\n\
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

        function loadMailboxPoll(folder) {
            var activeMessageId = $('.message-link.active').data('message-id'); // Get the active message ID
            $.ajax({
                url: '/admin/mail/mailbox/' + folder,
                type: 'GET',
                dataType: 'html',
                cache: false,
                success: function (response) {
                    $('#mailbox-container').html(response);
                    // Update the browser URL without reloading the page
                    history.pushState(null, '', '/admin/mail/mailbox/' + folder);

                    // Set the active class for the previously active message item
                    $('.message-link[data-message-id="' + activeMessageId + '"]').addClass('active');
                    $('.unseen[data-message-id="' + activeMessageId + '"]').removeClass('text-primary');
                    $('.unseen[data-message-id="' + activeMessageId + '"]').addClass('text-white');
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

// Function to retrieve the current folder from the URL
        function getCurrentFolderFromURL() {
            var url = window.location.pathname; // Get the current URL path
            var folder = url.split('/').pop(); // Extract the folder name from the URL
            return folder;
        }

// Polling function to periodically load the current mailbox
        function pollMailbox() {
            var currentFolder = getCurrentFolderFromURL();
            console.log('Polling mailbox for folder:', currentFolder);
            loadMailboxPoll(currentFolder);
        }

        $('.trash-form').submit(function (e) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            e.preventDefault(); // Prevent the default form submission

            var form = $(this);

            $.ajax({
                url: form.attr('action'),
                type: 'POST', // Use POST method since it's a form submission
                dataType: 'json',
                data: form.serialize(), // Serialize the form data
                success: function (response) {
                    // Handle the success response here
                    console.log('Trash action completed successfully');
                    loadMailbox(folderName);
                },
                error: function () {
                    // Handle the error response here
                    console.log('Error occurred while trashing');
                }
            });
        });
// Call the polling function initially
        pollMailbox();

// Set the interval to poll every 5 seconds (adjust the interval as needed)
        setInterval(pollMailbox, 20000);

    });
</script>
@endpush
@endsection
