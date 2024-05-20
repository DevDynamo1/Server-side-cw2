var ProfileView = Backbone.View.extend({
    el: '#profileData',

    initialize: function() {
        // Fetch the user profile data on initialization
        this.model.fetch({
            headers: {
                'Authorization': 'Bearer ' + sessionStorage.getItem('token')
            },
            success: this.render.bind(this),
            error: function(model, response) {
                alert('Failed to fetch profile data.');
            }
        });
    },

    render: function() {
        // Get user profile data
        var name = this.model.get('data').profile.UserName;
        var email = this.model.get('data').profile.email;
        var created_at = this.model.get('data').profile.created_at;

        // Build profile HTML
        var profileHTML = '<p><strong>User Name:</strong> ' + name + '</p>';
        profileHTML += '<p><strong>Email:</strong> ' + email + '</p>';
        profileHTML += '<p><strong>Joined on:</strong> ' + created_at + '</p>';

        // Get attempt data
        var attemptData = this.model.get('data').attempt;
        var attemptsHTML = '<br><br><h3>Attempt Quiz</h3>';
        attemptsHTML += '<div class="table-responsive"><table class="table table-striped"><thead><tr><th>CategoryID</th><th>Category Name</th><th>Score</th></tr></thead><tbody>';

        // Build attempt data HTML
        for (var i = 0; i < attemptData.length; i++) {
            var attempt = attemptData[i];
            attemptsHTML += '<tr>';
            attemptsHTML += '<td>' + attempt.CategoryID + '</td>';
            attemptsHTML += '<td>' + attempt.CategoryName + '</td>'; // Display CategoryName instead of CategoryID
            attemptsHTML += '<td>' + attempt.Score + '</td>';
            attemptsHTML += '</tr>';
        }

        attemptsHTML += '</tbody></table></div>';

        // Get created quiz data
        var quizzes = this.model.get('data').created_quizzes;
        var quizzesHTML = '<br><br><h3>Created Quiz</h3>';
        quizzesHTML += '<div class="table-responsive"><table class="table table-striped "><thead><tr><th>CategoryID</th><th>Category Name</th><th>Action</th></tr></thead><tbody>';

        // Build created quiz data HTML
        for (var i = 0; i < quizzes.length; i++) {
            var quiz = quizzes[i];
            quizzesHTML += '<tr>';
            quizzesHTML += '<td>' + quiz.CategoryID + '</td>';
            quizzesHTML += '<td>' + quiz.CategoryName + '</td>'; // Display CategoryName instead of CategoryID
            quizzesHTML += '<td><button class="btn btn-danger deleteBtn" data-categoryid="' + quiz.CategoryID + '">Delete</button></td>'; // Add delete button with data-categoryid attribute
            quizzesHTML += '</tr>';
        }

        quizzesHTML += '</tbody></table></div>';

        // Get bookmarked quiz data
        var bookmarks = this.model.get('data').bookmarks;
        var bookmarksHTML = '<br><br><h3>Bookmarked Quiz</h3>';
        bookmarksHTML += '<div class="table-responsive"><table class="table table-striped "><thead><tr><th>CategoryID</th><th>Category Name</th><th>Attempt</th><th>Remove</th></tr></thead><tbody>';

        // Build bookmarked quiz data HTML
        for (var i = 0; i < bookmarks.length; i++) {
            var bookmark = bookmarks[i];
            bookmarksHTML += '<tr>';
            bookmarksHTML += '<td>' + bookmark.CategoryID + '</td>';
            bookmarksHTML += '<td>' + bookmark.CategoryName + '</td>'; // Display CategoryName instead of CategoryID
            bookmarksHTML += '<td><button class="btn btn-success attemptBtn" data-categoryid="' + quiz.CategoryID + '">Attempt</button></td>'; // Add attempt button with data-categoryid attribute
            bookmarksHTML += '<td><button class="btn btn-danger removeBtn" data-bookmarkid="' + bookmark.id + '">Remove</button></td>'; // Add remove button with data-bookmarkid attribute
            bookmarksHTML += '</tr>';
        }

        bookmarksHTML += '</tbody></table></div>';

        // Render profile view with all the data
        this.$el.html(profileHTML + attemptsHTML + quizzesHTML + bookmarksHTML);
    }
});
