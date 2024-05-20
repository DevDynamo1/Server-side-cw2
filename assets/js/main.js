// // Instantiate the login view
// var loginView = new LoginView();

// Instantiate the login view if login form is present
if (document.getElementById('loginForm')) {
    var loginView = new LoginView();
}

// Instantiate the registration view if registration form is present
if (document.getElementById('registrationForm')) {
    var registrationView = new RegistrationView();
}

// Handle button click events
$('#loginBtn').on('click', function() {
    window.location.href = 'http://localhost/quizbuddy/index.php/api/user/login'; // Redirect to the login page
});

$('#registerBtn').on('click', function() {
    window.location.href = 'http://localhost/quizbuddy/index.php/user/register'; // Redirect to the register page
});

$('#profileBtn').on('click', function() {
    window.location.href = 'http://localhost/quizbuddy/index.php/user/profile_view'; // Redirect to the register page
});

document.getElementById('finishButton').addEventListener('click', function() {
    window.location.href = 'http://localhost/quizbuddy/index.php/tag/add?categoryId=' + categoryId;
});

