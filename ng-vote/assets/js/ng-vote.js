jQuery.noConflict();

jQuery(document).ready(function ($) {

    $('#ng-vote .btn').click(function (e) {

        // Prevent them from actually visiting the URL when clicking.
        e.preventDefault();

        // Get some useful variables rollin'.
        var isDisabled = $(this).attr('disabled');
        var blogURL = $(this).attr('href');

        if (typeof isDisabled !== typeof undefined && isDisabled !== false) {
            return false;
        }

        // Add a little confirm box saying, "Are you sure you want to vote?"
        // If they select 'cancel' then this whole thing bails and nothing after this gets executed/
        if (!confirm('Are you sure you wish to vote for ' + blogURL + '? You only get one vote and this cannot be undone!')) {
            return false;
        }

        $(this).attr('disabled', true);

        // Add a little 'waiting' thingie to the cursor.
        $(document.body).css({'cursor': 'wait'});

        // Start ajaxin'!
        $.ajax({
            type: 'POST',
            url: NG_VOTE.ajaxurl,
            data: {
                action: 'ng_cast_vote', // Action hooked into WordPress
                blog_url: blogURL, // The blog URL
                nonce: NG_VOTE.nonce // Our security nonce
            },
            dataType: 'json',
            success: function (response) {

                // Add an alert with our success message.
                alert(response.data);

                // Change the cursor back to normal.
                $(document.body).css({'cursor': 'default'});

            }
        }).fail(function (response) {
            // This stuff only happens if things fail miserably.
            $(document.body).css({'cursor': 'default'});
            if (window.console && window.console.log) {
                console.log(response);
            }
        });

    });

});