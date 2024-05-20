// Define a view for the login form
var LoginView = Backbone.View.extend({
    el: '#loginForm',

    events: {
        'submit': 'login'
    },

    login: function(event) {
        event.preventDefault();
        var code = this.$('#username').val();

        // user model instance
        var user = new UserModel({
            code: code,
        });

        // Perform a POST request to the server
        user.save({}, {
            url: user.urlRoot + '/authenticate',
            success: function(model, response) {
                alert('Login successful!');
                window.location.href = 'http://localhost/quizbuddy/index.php/api/category';
            },
            error: function(model, response) {
                alert('Login failed. Please try again.');
            }
        });
    }
});

// Instantiate the login view
var loginView = new LoginView();