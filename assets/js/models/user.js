var UserModel = Backbone.Model.extend({
    urlRoot: 'http://localhost/quizbuddy/index.php/api/User',
    defaults: {
        username: '',
        email: '',
        password: ''
    }
});
