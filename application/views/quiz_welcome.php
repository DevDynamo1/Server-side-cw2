<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Quiz</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/quiz2.css'); ?>">

</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="text-center mb-4">Welcome to Quiz: <span id="quizCategory"></span></h1>

            <div id="pinInputContainer" class="form-group">
                <input type="text" class="form-control" id="pinInput" placeholder="Enter PIN">
            </div>

            <button id="startQuiz" class="btn btn-primary btn-block">Start Quiz</button>
            <hr>
            <button id="bookmarkCategory" class="btn btn-info btn-block mt-4">Bookmark Category</button>
            <button id="copyLink" class="btn btn-success btn-block mt-4">Copy Quiz Link</button>
            <button id="backToHome" class="btn btn-secondary btn-block mt-4">Back to Homepage</button>

            <div id="feedbackSection">
                <h3 class="mt-4">Feedback and Ratings</h3>
                <div id="comments" class="list-group"></div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        var urlParams = new URLSearchParams(window.location.search);
        var categoryId = urlParams.get('categoryId');
        var categoryName = urlParams.get('categoryName');
        var visibility = urlParams.get('visibility');

        $('#quizCategory').text(categoryName);

        if (visibility === 'Private') {
            $('#pinInputContainer').show();
        }

        $('#startQuiz').on('click', function() {
            if (visibility === 'Private') {
                var pin = $('#pinInput').val().trim();
                $.ajax({
                    url: 'http://localhost/quizbuddy/index.php/api/quiz/authenticate_pin',
                    method: 'POST',
                    data: { categoryId: categoryId, pin: pin },
                    success: function(response) {
                        if (response.success) {
                            startQuiz(categoryId);
                        } else {
                            alert('Invalid PIN. Please try again.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error authenticating PIN:', error);
                        alert('Failed to authenticate PIN. Please try again.');
                    }
                });
            } else {
                startQuiz(categoryId);
            }
        });

        function startQuiz(categoryId) {
            window.location.href = 'http://localhost/quizbuddy/index.php/api/quiz?categoryId=' + categoryId;
        }

        $('#bookmarkCategory').on('click', function() {
            var payload = {
                categoryId: categoryId
            };
            var jsonString = JSON.stringify(payload);
            $.ajax({
                url: 'http://localhost/quizbuddy/index.php/api/bookmark',
                method: 'POST',
                contentType: 'application/json',
                data: jsonString,
                success: function(response) {
                    if (response.status) {
                        alert('Category bookmarked successfully!');
                    } else {
                        alert('Failed to bookmark category.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to bookmark category:', error);
                    alert('Failed to bookmark category.');
                }
            });
        });

        $('#copyLink').on('click', function() {
            var quizLink = window.location.href;
            navigator.clipboard.writeText(quizLink).then(function() {
                alert('Quiz link copied to clipboard!');
            }, function(err) {
                console.error('Failed to copy link: ', err);
            });
        });

        $.ajax({
            url: 'http://localhost/quizbuddy/index.php/api/feedback/feedback?categoryId=' + categoryId,
            method: 'GET',
            success: function(response) {
                if (response.status) {
                    var feedbackHtml = '';
                    response.data.forEach(function(feedback) {
                        var stars = '';
                        if (feedback.Rating !== null) {
                            for (var i = 0; i < feedback.Rating; i++) {
                                stars += '&#9733;';
                            }
                        }
                        feedbackHtml += `
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="stars">${stars}</div>
                                    <p class="card-text">${feedback.Comment}</p>
                                    <p class="card-text username"><small class="text-muted">${feedback.Username}</small></p>
                                </div>
                            </div>
                        `;
                    });
                    $('#comments').html(feedbackHtml);
                } else {
                    $('#comments').html('<div>No feedback available.</div>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to load feedback:', error);
            }
        });

        $('#backToHome').on('click', function() {
            window.location.href = 'http://localhost/quizbuddy/index.php/api/category';
        });
    });
</script>

</body>
</html>
