<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Score</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/quiz3.css'); ?>">

    <style>
        .star-rating {
            text-align: center;
        }
        .star-rating .fa {
            font-size: 1.5em;
            cursor: pointer;
            color: #ddd;
        }
        .star-rating .fa:hover,
        .star-rating .fa.active {
            color: #f5b301;
        }
        /* Custom CSS for increasing text size and styling */
        #commentSection label {
            font-size: 20px; /* Increased font size */
            font-weight: bold; /* Bold font weight */
            color: #333; /* Darker text color */
            margin-bottom: 10px; /* Add some spacing below the labels */
        }

    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="text-center mb-4">Quiz Score: <span id="quizScore"></span></h2>
            <div id="commentSection">
                <h3 class="mt-4">Add a Comment</h3>
                <form id="commentForm">
                    <div class="form-group">
                        <label for="comment">Comment</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="rating">Rating</label>
                        <div id="starRating" class="star-rating">
                            <span class="fa fa-star" data-value="1"></span>
                            <span class="fa fa-star" data-value="2"></span>
                            <span class="fa fa-star" data-value="3"></span>
                            <span class="fa fa-star" data-value="4"></span>
                            <span class="fa fa-star" data-value="5"></span>
                        </div>
                        <input type="hidden" id="rating" name="rating" value="0">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Submit</button>
                </form>
            </div>
            <button id="backToHome" class="btn btn-secondary btn-block mt-4">Back to Homepage</button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.13.1/underscore-min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/backbone.js/1.4.0/backbone-min.js"></script>

<script>
    $(document).ready(function() {
        // Retrieve the score parameter from the URL
        var urlParams = new URLSearchParams(window.location.search);
        var score = urlParams.get('score');
        var categoryID = urlParams.get('categoryId'); // Example CategoryID


        // Display the score in the HTML page
        $('#quizScore').text(score);

        // Define a model for the feedback
        var FeedbackModel = Backbone.Model.extend({
            urlRoot: 'http://localhost/quizbuddy/index.php/api/Feedback' // Assuming the server handles feedback at this endpoint
        });

        // Define a view for the comment form
        var CommentView = Backbone.View.extend({
            el: '#commentForm',

            events: {
                'submit': 'submitFeedback'
            },

            submitFeedback: function(event) {
                event.preventDefault();
                var commentText = this.$('#comment').val();
                var rating = this.$('#rating').val();

                // Create a new feedback model
                var feedback = new FeedbackModel({
                    CategoryID: categoryID,
                    Comment: commentText,
                    Rating: rating,
                });

                // Perform a POST request to the server
                feedback.save({}, {
                    success: function(model, response) {
                        alert('Feedback submitted successfully!');
                        // Clear the form
                        $('#commentForm')[0].reset();
                        $('.star-rating .fa').removeClass('active');
                        window.location.href = 'http://localhost/quizbuddy/index.php/api/category'
                    },
                    error: function(model, response) {
                        alert('Failed to submit feedback. Please try again.');
                    }
                });
            }
        });

        // Instantiate the comment view
        var commentView = new CommentView();

        // Handle back to homepage button click
        $('#backToHome').on('click', function() {
            window.location.href = 'http://localhost/quizbuddy/index.php/api/category'; // Update the redirection URL for the homepage
        });

        // Star rating interaction
        $('.star-rating .fa').on('click', function() {
            var rating = $(this).data('value');
            $('#rating').val(rating);
            $('.star-rating .fa').removeClass('active');
            $(this).addClass('active').prevAll().addClass('active');
        });
    });
</script>

</body>
</html>
