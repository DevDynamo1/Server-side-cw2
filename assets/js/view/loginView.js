var LoginView = Backbone.View.extend({
    el: '#loginForm',

    events: {
        'submit': 'login',
        'click #registerBtn': 'redirectToRegister'
    },

    // Function to handle the login form submission
    login: function(event) {
        event.preventDefault();

        // Get the username and password from the form inputs
        var username = this.$('#username').val();
        var password = this.$('#password').val();

        // Create a new UserModel instance
        var user = new UserModel({
            email: username,
            password: password
        });

        // Send a POST request to the server to authenticate the user
        user.save({}, {
            url: user.urlRoot + '/login',
            success: function(model, response) {
                // Additional authentication is required
                if (response.requiresAuthCode) {
                    // Redirect to the authentication code view
                    window.location.href = 'http://localhost/quizbuddy/index.php/api/user/authcode';
                } else {
                    // Redirect to the category page
                    window.location.href = 'http://localhost/quizbuddy/index.php/api/category';
                }
            },
            error: function(model, response) {
                alert('Login failed. Please try again.');
            }
        });
    },

    // Function to redirect to the registration page
    redirectToRegister: function(event) {
        event.preventDefault();
        window.location.href = 'http://localhost/quizbuddy/index.php/user/register';
    }
});
