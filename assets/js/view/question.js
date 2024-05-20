var AddQuestionView = Backbone.View.extend({
    el: '#addQuestionForm',

    events: {
        'submit': 'addQuestion' // Event listener for form submission
    },

    // Function to handle form submission
    addQuestion: function(event) {
        event.preventDefault();

        // Get question text from the input field
        var questionText = this.$('#questionText').val();

        // Get the category ID from the global variable
        var categoryID = categoryId;

        // Get answers from input fields
        var answers = this.$("input[name='answers[]']").map(function() {
            return $(this).val();
        }).get();

        // Get the index of the correct answer from radio buttons
        var correctAnswerIndex = this.$("input[name='correctAnswer']:checked").val();

        // Check if a correct answer is selected
        if (correctAnswerIndex === undefined) {
            alert('Please select one correct answer.');
            return;
        }

        // Prepare question data to be saved
        var questionData = {
            QuestionText: questionText,
            CategoryID: categoryID
        };

        // Create a new QuestionModel instance with question data
        var question = new QuestionModel(questionData);

        var self = this;

        // Save the question to the server
        question.save({}, {
            success: function(model, response) {
                alert('Question added successfully!');
                var questionId = response.QuestionID;

                // Iterate over answers and save them to the server
                answers.forEach(function(answer, index) {
                    var isCorrect = index == correctAnswerIndex ? 1 : 0;
                    $.ajax({
                        url: 'http://localhost/quizbuddy/index.php/api/Answer',
                        method: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({
                            AnswerText: answer,
                            QuestionID: questionId,
                            IsCorrect: isCorrect
                        }),
                        success: function(resp) {
                            console.log('Answer added successfully:', resp);
                        },
                        error: function(xhr, status, error) {
                            console.error('Failed to add answer:', error);
                        }
                    });
                });

                // Redirect to add question page with category ID
                window.location.href = 'http://localhost/quizbuddy/index.php/question/add?categoryId=' + categoryId;
            },
            error: function(model, response) {
                // Alert error message if failed to add question
                alert('Failed to add question. Please try again.');
            }
        });
    }
});

var addQuestionView = new AddQuestionView();
