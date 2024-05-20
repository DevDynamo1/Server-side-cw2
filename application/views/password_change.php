<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/quiz3.css'); ?>">

    <style>
        body {
            background-image: url('<?php echo base_url('assets/background.jpg'); ?>');
        }
        .form-group input[type="password"] {
            text-align: center; /* Center align text in input fields */
            font-size: 18px; /* Increase font size for better readability */
            padding: 10px; /* Add padding for spacing */
            font-weight: bold; /* Make text bold */
        }
        .form-group label {
            font-size: 20px;
            font-weight: bold; /* Make the label bold */
            color: #f0f0f0; /* Light color for better visibility */
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5); /* Black shadow for better contrast */
        }


    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-center mb-4">Update Password</h2>
            <form id="updatePasswordForm">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" class="form-control" id="current_password" name="current_password">
                </div>
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password">
                </div>
                <div class="form-group">
                    <label for="confirm_new_password">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Update Password</button>
                <!-- Back button -->
                <button id="backBtn" class="btn btn-secondary btn-block mt-3">Back</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.13.1/underscore-min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/backbone.js/1.4.0/backbone-min.js"></script>


<script src="<?php echo base_url('assets/js/models/password.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/view/password.js'); ?>"></script>

<script>
    var sessionUserId = <?php echo json_encode($_SESSION['user_id']); ?>;
</script>

</body>
</html>
