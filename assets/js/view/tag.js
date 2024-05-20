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
        this.categoryID = categoryId;
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
                    alert('Quiz updated successfully!');
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