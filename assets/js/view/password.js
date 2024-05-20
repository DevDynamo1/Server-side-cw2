var UpdatePasswordView = Backbone.View.extend({
    el: '#updatePasswordForm',

    events: {
        'submit': 'updatePassword',
        'click #backBtn': 'goBack' // Event listener for the back button
    },

    updatePassword: function(event) {
        event.preventDefault();
        var current_password = this.$('#current_password').val();
        var new_password = this.$('#new_password').val();
        var confirm_new_password = this.$('#confirm_new_password').val();

        // Validate new password and confirm new password
        if (new_password !== confirm_new_password) {
            alert('New password and confirm new password do not match.');
            return;
        }

        // Create a new user model with the user ID from the session
        var user = new UserModel({
            id: sessionUserId, // Use the session user ID here
            current_password: current_password,
            new_password: new_password,
            confirm_new_password: confirm_new_password
        });

        // Perform a PUT request to the server
        user.save({}, {
            type: 'PUT',
            success: function(model, response) {
                alert('Password updated successfully!');
                // Redirect or do something else on successful update
                window.location.href = 'http://localhost/quizbuddy/index.php/user/profile_view'; // Redirect to the register page
            },
            error: function(model, response) {
                alert('Password update failed. Please try again.');
            }
        });
    },

    // Function to handle back button click event
    goBack: function(event) {
        event.preventDefault();
        window.location.href = 'http://localhost/quizbuddy/index.php/api/user/profile_view';
    }
});

// Instantiate the update password view
var updatePasswordView = new UpdatePasswordView();