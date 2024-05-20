<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/home.css'); ?>">
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="text-center mb-4">Email Verification</h1>
            <form id="loginForm">
                <div class="form-group">
                    <label for="username">Authentication Code: </label>
                    <input type="text" class="form-control" id="username" name="username">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Authenticate</button>
            </form>
            <div class="mt-3"> <!-- Wrapper for Resend and Login buttons -->
                <button id="resendBtn" class="btn btn-success btn-block">Resend</button> <!-- Resend button -->
                <button id="login" class="btn btn-secondary btn-block mt-3">Login</button> <!-- Login button -->
            </div>
        </div>
    </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.13.1/underscore-min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/backbone.js/1.4.0/backbone-min.js"></script>

<script src="<?php echo base_url('assets/js/models/user.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/view/verification.js'); ?>"></script>

<script>
    $(document).ready(function() {
        // Resend Button Click Event
        $('#resendBtn').on('click', function() {
            $.ajax({
                type: 'PUT',
                url: 'http://localhost/quizbuddy/index.php/api/user/resend',
                success: function(response) {
                    alert('Activation email resent successfully!');
                },
                error: function(xhr, status, error) {
                    alert('Error resending activation email: ' + error);
                }
            });
        });

        // Register Button Click Event
        $('#login').on('click', function() {
            window.location.href = 'http://localhost/quizbuddy/index.php/api/user/login';
        });
    });
</script>


</body>
</html>
