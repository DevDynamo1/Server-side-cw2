<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Tags and Set Difficulty</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/quiz.css'); ?>">
    <style>
        h1 {
            color: #ffffff; /* White text color */
            text-shadow: 2px 2px 0 rgba(255, 255, 255, 0.03), 4px 4px 0 #090908; /* 3D text shadow effect */
        }

        /* Center-align and increase size for form elements */
        #tagsForm label {
            font-size: 1.5rem; /* Increased font size */
            display: block;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="text-center mb-4">Add Tags and Set Difficulty</h1>
            <form id="tagsForm" action="http://localhost/quizbuddy/index.php/api/Category/level" method="PUT">
                <div class="form-group">
                    <label for="tags">Tags</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="tagInput" name="tagInput">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-secondary" id="addTagButton">Add</button>
                        </div>
                    </div>
                    <div id="tagsContainer" class="mt-2"></div>
                </div>
                <div class="form-group">
                    <label for="difficulty">Difficulty Level</label>
                    <select class="form-control" id="difficulty" name="difficulty" required>
                        <option value="">Select Difficulty</option>
                        <option value="easy">Easy</option>
                        <option value="normal">Normal</option>
                        <option value="hard">Hard</option>
                    </select>
                </div>
                <div class="form-group" id="visibilityGroup">
                    <label for="visibility">Visibility</label>
                    <select class="form-control" id="visibility" name="visibility" required>
                        <option value="public">Public</option>
                        <option value="private">Private</option>
                    </select>
                </div>
                <div class="form-group" id="pinGroup" style="display: none;">
                    <label for="pin">PIN</label>
                    <input type="text" class="form-control" id="pin" name="pin">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Finish</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.13.1/underscore-min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/backbone.js/1.4.0/backbone-min.js"></script>

<script>
    // Define a view for the tags form
    var TagView = Backbone.View.extend({
        el: '#tagsForm',

        events: {
            'submit': 'submitTags',
            'click #addTagButton': 'addTag',
            'change #visibility': 'togglePINInput'
        },

        initialize: function() {
            this.tags = [];
            this.categoryID = <?php echo json_encode($categoryId); ?>;
        },

        addTag: function() {
            var tagInput = this.$('#tagInput');
            var tagValue = tagInput.val().trim();
            if (tagValue) {
                var self = this;
                $.ajax({
                    url: 'http://localhost/quizbuddy/index.php/api/tag',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        tagname: tagValue,
                        CategoryID: this.categoryID
                    }),
                    success: function(response) {
                        self.tags.push(tagValue);
                        tagInput.val('');
                        self.renderTags();
                    },
                    error: function(xhr, status, error) {
                        alert('Failed to add tag. Please try again.');
                    }
                });
            }
        },

        renderTags: function() {
            var tagsContainer = this.$('#tagsContainer');
            tagsContainer.empty();
            this.tags.forEach(function(tag) {
                tagsContainer.append('<span class="badge badge-secondary mr-2">' + tag + '</span>');
            });
        },

        submitTags: function(event) {
            event.preventDefault();
            var difficulty = this.$('#difficulty').val();
            var visibility = this.$('#visibility').val();
            var pin = this.$('#pin').val(); // Get the PIN value

            // Assuming we need to perform a final action with the difficulty level or just redirect
            if (difficulty && visibility) {
                var level = difficulty;
                var visibility = visibility;
                var dataToSend = {
                    CategoryID: this.categoryID,
                    Level: level,
                    Visibility: visibility
                };

                // Include PIN in dataToSend if visibility is private
                if (visibility === 'private' && pin) {
                    dataToSend.PIN = pin;
                }

                $.ajax({
                    url: 'http://localhost/quizbuddy/index.php/api/Category/level',
                    method: 'PUT',
                    contentType: 'application/json',
                    data: JSON.stringify(dataToSend),
                    success: function(response) {
                        alert('Difficulty level and visibility updated successfully!');
                        // Redirect to the category endpoint
                        window.location.href = 'http://localhost/quizbuddy/index.php/api/category';
                    },
                    error: function(xhr, status, error) {
                        alert('Failed to update difficulty level and visibility. Please try again.');
                    }
                });
            } else {
                alert('Please select a difficulty level and visibility.');
            }
        },

        togglePINInput: function() {
            var visibility = this.$('#visibility').val();
            var pinGroup = this.$('#pinGroup');

            if (visibility === 'private') {
                pinGroup.show();
            } else {
                pinGroup.hide();
            }
        }

    });

    // Instantiate the tag view
    var tagView = new TagView();
</script>

</body>
</html>
