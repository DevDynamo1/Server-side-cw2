var RegistrationView = Backbone.View.extend({
    el: '#registrationForm',

    // Event bindings for the view
    events: {
        'submit': 'register',
        'click #loginBtn': 'redirectToLogin'
    },

    // Function to handle user registration
    register: function(event) {
        // Prevent the default form submission behavior
        event.preventDefault();

        // Retrieve data from the form
        var username = this.$('#username').val();
        var email = this.$('#email').val();
        var password = this.$('#password').val();
        var confirm_password = this.$('#confirm_password').val();

        // Validate password match
        if (password !== confirm_password) {
            alert('Passwords do not match');
            return;
        }

        // Create user data object
        var userdata = {
            username: username,
            email: email,
            password: password
        };

        // UserModel instance
        var user = new UserModel(userdata);

        // Save the user model
        user.save({}, {
            url: user.urlRoot + '/add_user',

            // Success callback function
            success: function(model, response) {
                alert('Registration successful! Check your email to get the authentication code.');
                window.location.href = 'http://localhost/quizbuddy/index.php/user/login'; // Redirect to login page
            },

            // Error callback function
            error: function(model, response) {
                if (response.responseJSON && response.responseJSON.status === 'error') {
                    var errorCode = response.responseJSON.error_code;
                    var errorMessage = response.responseJSON.message;

                    if (errorCode === 'EMAIL_EXISTS') {
                        errorMessage = 'Registration failed: Email already exists';
                    }

                    alert(errorMessage); // Notify user about the error
                } else {
                    // Unexpected error or no response from the server
                    alert('Registration failed. Please try again.');
                }
            }
        });
    },

    // Function to redirect to login page
    redirectToLogin: function(event) {
        event.preventDefault();
        window.location.href = 'http://localhost/quizbuddy/index.php/user/login'; // Redirect to login page
    }
});
