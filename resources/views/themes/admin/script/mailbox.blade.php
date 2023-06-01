$(document).ready(function () {
    fetchFolderCounts();
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
    $(document).on('click', '#trash-button', function () {
        var $form = $('#trash-form');
        var nextMessageId = $('.message-link.active').next().data('message-id');
        var folder = $form.find('[name="folder"]').val();
        var messageId = $form.find('[name="messageId"]').val();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: $form.data('action'),
            type: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                folder: folder,
                messageId: messageId,
            },
            success: function (response) {
                // Handle the success response
                // You can display a success message or perform any additional actions
                console.log('Email moved to Trash!');
                $('.message-link.active').remove();
                if (nextMessageId) {
                    $('.message-link[data-message-id="' + nextMessageId + '"]').addClass('active');
                    loadMessage(folder, nextMessageId);
                    loadMailboxPoll(folder, nextMessageId);
                } else {
                    loadMailboxPoll(folder);
                    $('#message-content-placeholder').html('<div class="card" id="message-content">\n\
    <div class="card-header">Message</div>\n\
    <div class="card-body">\n\
        <p class="text-center" style="font-size: 30px;">Select a message to read</p>\n\
        <p class="text-center text-primary" style="font-size: 150px;">\n\
            <i class="fa-solid fa-envelope-open-text"></i></p>\n\
    </div>\n\
</div>');
                }
                fetchFolderCounts();
                // Optionally, you can redirect to the previous page
                // window.location.href = document.referrer;
            },
            error: function (xhr) {
                // Handle the error response
                // You can display an error message or perform any additional actions
                console.error('Error moving email to Trash!');
            }
        });
    });
    $(document).on('click', '#delete-button', function () {
    var $form = $('#delete-form');
            var nextMessageId = $('.message-link.active').next().data('message-id');
            var folder = $form.find('[name="folder"]').val();
            var messageId = $form.find('[name="messageId"]').val();
            $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            });
            $.ajax({
            url: $form.data('action'),
                    type: 'POST',
                    data: {
                    "_token": "{{ csrf_token() }}",
                            folder: folder,
                            messageId: messageId,
                    },
                    success: function (response) {
                    // Handle the success response
                    // You can display a success message or perform any additional actions
                    console.log('Email Deleted!');
                            $('.message-link.active').remove();
                            if (nextMessageId) {
                    $('.message-link[data-message-id="' + nextMessageId + '"]').addClass('active');
                            loadMessage(folder, nextMessageId);
                            loadMailboxPoll(folder, nextMessageId);
                    } else {
                    loadMailboxPoll(folder);
                            $('#message-content-placeholder').html('<div class="card" id="message-content">\n\
    <div class="card-header">Message</div>\n\
    <div class="card-body">\n\
        <p class="text-center" style="font-size: 30px;">Select a message to read</p>\n\
        <p class="text-center text-primary" style="font-size: 150px;">\n\
            <i class="fa-solid fa-envelope-open-text"></i></p>\n\
    </div>\n\
</div>');
                    }
                    fetchFolderCounts();
                            // Optionally, you can redirect to the previous page
                            // window.location.href = document.referrer;
                    },
                    error: function (xhr) {
                    // Handle the error response
                    // You can display an error message or perform any additional actions
                    console.error('Error deleting email!');
                    }
            });
            });
@foreach($folders as $folder)
    $(document).on('click', '#{{ strtolower($folder->name) }}-button', function () {
        var $form = $('#{{ strtolower($folder->name) }}-form');
        var nextMessageId = $('.message-link.active').next().data('message-id');
        var folder = $form.find('[name="folder"]').val();
        var targetFolder = $form.find('[name="targetFolder"]').val();
        var messageId = $form.find('[name="messageId"]').val();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: $form.data('action'),
            type: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                folder: folder,
                targetFolder: targetFolder,
                messageId: messageId,
            },
            success: function (response) {
                // Handle the success response
                // You can display a success message or perform any additional actions
                console.log('Email moved to ' + targetFolder + '!');
                $('.message-link.active').remove();
                if (nextMessageId) {
                    $('.message-link[data-message-id="' + nextMessageId + '"]').addClass('active');
                    loadMessage(folder, nextMessageId);
                    loadMailboxPoll(folder, nextMessageId);
                } else {
                    loadMailboxPoll(folder);
                    $('#message-content-placeholder').html('<div class="card" id="message-content">\n\
    <div class="card-header">Message</div>\n\
    <div class="card-body">\n\
        <p class="text-center" style="font-size: 30px;">Select a message to read</p>\n\
        <p class="text-center text-primary" style="font-size: 150px;">\n\
            <i class="fa-solid fa-envelope-open-text"></i></p>\n\
    </div>\n\
</div>');
                }
                fetchFolderCounts();
                // Optionally, you can redirect to the previous page
                // window.location.href = document.referrer;
            },
            error: function (xhr) {
                // Handle the error response
                // You can display an error message or perform any additional actions
                console.error('Error moving email to ' + targetFolder + '!');
            }
        });
    });
    @endforeach
// Function to load mailbox via AJAX
            function loadMailbox(folder) {
            $.ajax({
                url: "{{ route('webmail.mailbox', '') }}" + '/' + folder,
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
                    history.pushState(null, '', "{{ route('webmail.mailbox', '') }}" + '/' + folder);
                },
                error: function (xhr, status, error) {
                    console.error(error);
                }
            });
            }

    function loadMailboxPoll(folder, activeMessageId) {
        var activeMessageId = activeMessageId || $('.message-link.active').data('message-id'); // Get the active message ID
        $.ajax({
        url: "{{ route('webmail.mailbox', '') }}" + '/' + folder,
                type: 'GET',
                dataType: 'html',
                cache: false,
                success: function (response) {
                $('#mailbox-container').html(response);
// Update the browser URL without reloading the page
                        history.pushState(null, '', "{{ route('webmail.mailbox', '') }}" + '/' + folder);
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
        var url = "{{ route('webmail.mailbox', '') }}" + '/' + folder + '/' + messageId;
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

// Fetch folder counts
    function fetchFolderCounts() {
        $('.folder-count').each(function () {
            var folderCountElement = $(this);
            var folderName = folderCountElement.data('folder');
            var currentCount = parseInt(folderCountElement.text());
            $.ajax({
                url: "{{ route('webmail.pollFolders', '') }}" + '/' + folderName,
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    var newCount = response.count;
                    // Only update the count if it's a valid number and not zero
                    if (!isNaN(newCount) && newCount !== 0) {
                        folderCountElement.text(newCount);
                    }
                },
                error: function () {
                    // Keep the previous count if there's an error
                    folderCountElement.text(currentCount);
                }
            });
        });
    }

// Update folder counts every 5 seconds
    setInterval(fetchFolderCounts, 20000);
    $('.add-folder-link').click(function (e) {
        e.preventDefault();
        $(this).hide();
        $('.add-folder-form').removeClass('d-none');
    });
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

// Call the polling function initially
    pollMailbox();
// Set the interval to poll every 5 seconds (adjust the interval as needed)
    setInterval(pollMailbox, 20000);
});