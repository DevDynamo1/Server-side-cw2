var app = app || {};

app.CategoryList = Backbone.Collection.extend({
    model: app.Category,
    url: 'http://localhost/quizbuddy/index.php/api/category/search',

    searchCategories: function(searchTerm, difficulty) {
        var self = this;
        this.fetch({
            data: { search: searchTerm, difficulty: difficulty },
            reset: true,
            success: function(collection, response, options) {
                console.log('Fetched categories:', collection);
            },
            error: function(collection, response, options) {
                console.error('Failed to fetch categories:', response);
            }
        });
    }
});
