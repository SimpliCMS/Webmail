$(document).ready(function() {
    // Initial load of inbox mailbox
    loadMailbox('INBOX');

    // Handle sidebar mailbox clicks
    $('.sidebar').on('click', 'a', function(e) {
        e.preventDefault();
        var folder = $(this).attr('href').split('/').pop();
        loadMailbox(folder);
    });

    // Function to load mailbox via AJAX
    function loadMailbox(folder) {
        $.ajax({
            url: '/admin/mail/mailbox/' + folder,
            type: 'GET',
            success: function(response) {
                $('.mailbox').html(response);
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }
});



