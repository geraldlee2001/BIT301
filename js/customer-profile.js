$(document).ready(function() {
    $('#profileForm').submit(function(e) {
        e.preventDefault();
        // Simulate a delay (you can replace this with an actual AJAX request)
        setTimeout(function() {
            // Show the success message
            $('#success-message').show();
            
            // Automatically hide the success message after 3 seconds (3000 milliseconds)
            setTimeout(function() {
                $('#success-message').hide();
            }, 3000);
        },); // Delay for 1 second (adjust as needed)
    });
});




