// Define a view for the category form
var CategoryView = Backbone.View.extend({
    el: '#categoryForm',

    events: {
        'submit': 'submitCategory'
    },

    submitCategory: function(event) {
        event.preventDefault();
        var categoryName = this.$('#categoryName').val();
        var description = this.$('#description').val();

        // Create a new category model
        var category = new CategoryModel({
            CategoryName: categoryName,
            Description: description
        });

        // Perform a POST request to the server
        category.save({}, {
            success: function(model, response) {
                alert('Category added successfully!');
                var categoryId = response.CategoryID; // Get the category ID
                // Redirect to the question controller with the category ID
                window.location.href = 'http://localhost/quizbuddy/index.php/question/add?categoryId=' + categoryId;
            },
            error: function(model, response) {
                alert('Failed to add category. Please try again.');
            }
        });
    }
});

// Instantiate the category view
var categoryView = new CategoryView();