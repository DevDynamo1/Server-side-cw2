<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/profile.css'); ?>">

</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="text-center mb-4">User Profile</h2>
            <div id="profileData">
                <!-- Profile data and attempts table will be displayed here -->
            </div>
            <div class="row justify-content-between mt-4">
                <div class="col-md-3">
                    <button id="backBtn" class="btn btn-secondary btn-block">Back</button>
                </div>
                <div class="col-md-3">
                    <button id="changePasswordBtn" class="btn btn-primary btn-block">Change Password</button>
                </div>
                <div class="col-md-3">
                    <button id="logoutBtn" class="btn btn-danger btn-block">Logout</button>
                </div>
                <div class="col-md-3">
                    <button id="deleteAccountBtn" class="btn btn-danger btn-block">Delete Account</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.13.1/underscore-min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/backbone.js/1.4.0/backbone-min.js"></script>

<script src="<?php echo base_url('assets/js/models/profile.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/view/profile.js'); ?>"></script>

<script>

    var defaultControllerRoute = "<?php echo base_url(); ?>";


    // Instantiate the profile model with session data
    var profileModel = new ProfileModel();

    // Instantiate the profile view
    var profileView = new ProfileView({ model: profileModel
    });

    // Handle click event for changing password button
    $('#changePasswordBtn').on('click', function() {
        window.location.href = 'http://localhost/quizbuddy/index.php/user/password_change'; // Redirect to the register page
    });

    // Handle click event for logout button
    $('#logoutBtn').on('click', function() {
        $.ajax({
            url: 'http://localhost/quizbuddy/index.php/api/user/logout',
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + sessionStorage.getItem('token')
            },
            success: function(response) {
                window.location.href = defaultControllerRoute; // Redirect to login page
            },
            error: function(xhr, status, error) {
                console.error('Failed to logout:', error);
                alert('Failed to logout. Please try again.');
            }
        });
    });

    // Handle click event for back button
    $('#backBtn').on('click', function() {
        window.location.href = 'http://localhost/quizbuddy/index.php/api/category'; // Redirect to home page
    });

    // Handle click event for delete button
    $(document).on('click', '.deleteBtn', function() {
        var categoryId = $(this).data('categoryid');
        if (confirm('Are you sure you want to delete this quiz?')) {
            $.ajax({
                url: 'http://localhost/quizbuddy/index.php/api/category/' + categoryId,
                method: 'DELETE', // Use DELETE method for deletion

                headers: {
                    'Authorization': 'Bearer ' + sessionStorage.getItem('token')
                },
                success: function(response) {
                    // If deletion is successful, you may want to reload the profile data to reflect the changes
                    profileModel.fetch({
                        headers: {
                            'Authorization': 'Bearer ' + sessionStorage.getItem('token')
                        },
                        success: function() {
                            profileView.render(); // Render the updated profile data
                        },
                        error: function(model, response) {
                            alert('Failed to fetch updated profile data.');
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Failed to delete quiz:', error);
                    alert('Failed to delete quiz. Please try again.');
                }
            });
        }
    });

    // Handle click event for delete account button
    $('#deleteAccountBtn').on('click', function() {
        if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
            $.ajax({
                url: 'http://localhost/quizbuddy/index.php/api/user', // Assuming this endpoint handles user deletion
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + sessionStorage.getItem('token')
                },
                success: function(response) {
                    window.location.href = defaultControllerRoute; // Redirect to the default route after successful deletion
                },
                error: function(xhr, status, error) {
                    console.error('Failed to delete account:', error);
                    alert('Failed to delete account. Please try again.');
                }
            });
        }
    });

    // Handle click event for attempt button
    $(document).on('click', '.attemptBtn', function() {
        var categoryId = $(this).data('categoryid');
        var categoryName = $(this).closest('tr').find('td:eq(1)').text(); // Get the category name from the table row
        window.location.href = 'http://localhost/quizbuddy/index.php/api/quiz/welcome?categoryId=' + categoryId + '&categoryName=' + encodeURIComponent(categoryName);
    });

    // Handle click event for remove button
    $(document).on('click', '.removeBtn', function() {
        var bookmarkId = $(this).data('bookmarkid'); // Get the bookmark ID from the button's data attribute
        if (confirm('Are you sure you want to remove this quiz from bookmarks?')) {
            $.ajax({
                url: 'http://localhost/quizbuddy/index.php/api/bookmark/' + bookmarkId,
                method: 'DELETE', // Use DELETE method for deletion
                headers: {
                    'Authorization': 'Bearer ' + sessionStorage.getItem('token')
                },
                success: function(response) {
                    // If deletion is successful, you may want to reload the profile data to reflect the changes
                    profileModel.fetch({
                        headers: {
                            'Authorization': 'Bearer ' + sessionStorage.getItem('token')
                        },
                        success: function() {
                            profileView.render(); // Render the updated profile data
                        },
                        error: function(model, response) {
                            alert('Failed to fetch updated profile data.');
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Failed to remove bookmark:', error);
                    alert('Failed to remove bookmark. Please try again.');
                }
            });
        }
    });


</script>

</body>
</html>
