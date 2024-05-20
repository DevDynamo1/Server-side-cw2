<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/quiz3.css'); ?>">

</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="text-center mb-4">Quiz</h2>
            <form id="quizForm">
                <div id="quizContainer"></div>
                <button type="button" class="btn btn-primary submit-btn">Submit</button> <!-- Change type to "button" -->
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.13.1/underscore-min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/backbone.js/1.4.0/backbone-min.js"></script>

<script>
    var QuizView = Backbone.View.extend({
        el: '#quizContainer',

        initialize: function() {
            this.fetchQuestions();
        },

        fetchQuestions: function() {
            var self = this;
            // var categoryId = 28;
            var categoryId = this.categoryID = <?php echo json_encode($categoryId); ?>;

            $.ajax({
                url: 'http://localhost/quizbuddy/index.php/api/Question/get',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ CategoryID: categoryId }),
                success: function(response) {
                    if (response.status) {
                        self.renderQuestions(response.data);
                    } else {
                        console.error('Failed to fetch questions:', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to fetch questions:', error);
                }
            });
        },

        renderQuestions: function(questions) {
            var self = this;
            questions.forEach(function(question) {
                var questionText = question.QuestionText;
                var $quizCard = $('<div class="quiz-card"></div>');
                $quizCard.append('<h5 class="question">' + questionText + '</h5>');

                var $answersContainer = $('<div class="answers-container"></div>');

                // Fetch answers for this question
                self.fetchAnswers(question.QuestionID, $answersContainer);

                $quizCard.append($answersContainer);

                self.$el.append($quizCard);
            });
        },

        fetchAnswers: function(questionId, $answersContainer) {
            var self = this;
            $.ajax({
                url: 'http://localhost/quizbuddy/index.php/api/Answer/get',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ QuestionID: questionId }),
                success: function(response) {
                    if (response.status) {
                        self.renderAnswers(response.data, $answersContainer, questionId);
                    } else {
                        console.error('Failed to fetch answers for question ' + questionId + ':', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to fetch answers for question ' + questionId + ':', error);
                }
            });
        },

        renderAnswers: function(answers, $answersContainer, questionId) {
            var self = this;
            answers.forEach(function(answer) {
                var answerText = answer.AnswerText;
                // var isCorrect = answer.IsCorrect ? '1' : '0'; // Convert boolean to 1 or 0
                var $answerBtn = $('<button type="button" class="btn btn-outline-primary answer-btn">' + answerText + '</button>');
                $answerBtn.data('answer-id', answer.AnswerID);
                $answerBtn.data('is-correct', answer.IsCorrect); // Store whether the answer is correct
                $answersContainer.append($answerBtn);
            });
        },

        submitQuiz: function(event) {
            var self = this;
            var correctAnswersCount = 0;

            $('.quiz-card').each(function() {
                var $questionCard = $(this);
                var selectedAnswerId = $questionCard.find('.answer-btn.selected').data('answer-id');
                var isCorrect = $questionCard.find('.answer-btn.selected').data('is-correct');

                if (selectedAnswerId) {
                    if (isCorrect === "1") {
                        correctAnswersCount++;
                    }
                }
            });

            // Log Category ID and Score to console
            console.log('Category ID:', self.categoryID);
            console.log('Score:', correctAnswersCount);

            // Send the score data to the server
            $.ajax({
                url: 'http://localhost/quizbuddy/index.php/api/attempt',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    CategoryID: parseInt(self.categoryID),
                    Score: parseInt(correctAnswersCount)
                }), // Make sure to include CategoryID and Score
                success: function(response) {
                    if (response.status) {
                        alert('Score saved successfully!');
                        // Redirect to some page after submitting the quiz
                        window.location.href = 'http://localhost/quizbuddy/index.php/api/feedback?score=' + correctAnswersCount + '&categoryId=' + self.categoryID;
                    } else {
                        console.error('Failed to save score:', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to save score:', error);
                }
            });
        }

    });

    // Instantiate the QuizView and handle the submit button click event
    var quizView = new QuizView();
    $(document).on('click', '.submit-btn', function() {
        quizView.submitQuiz();
    });

    // Handle answer button click event
    $(document).on('click', '.answer-btn', function() {
        $(this).toggleClass('selected').siblings().removeClass('selected');
    });
</script>

</body>
</html>
