<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Question Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/quiz.css'); ?>">

</head>
<body>

<div class="card">
    <h1 class="text-center mb-4">Add Question</h1>
    <form id="addQuestionForm">
        <div class="form-group">
            <label for="questionText">Question Text</label>
            <input type="text" class="form-control" id="questionText" name="questionText" required>
        </div>
        <div class="form-group">
            <label for="answer1">Answer 1</label>
            <input type="text" class="form-control" id="answer1" name="answers[]" required>
            <div class="radio-group">
                <input type="radio" name="correctAnswer" value="0" id="correctAnswer1">
                <label for="correctAnswer1">Correct Answer</label>
            </div>
        </div>
        <div class="form-group">
            <label for="answer2">Answer 2</label>
            <input type="text" class="form-control" id="answer2" name="answers[]" required>
            <div class="radio-group">
                <input type="radio" name="correctAnswer" value="1" id="correctAnswer2">
                <label for="correctAnswer2">Correct Answer</label>
            </div>
        </div>
        <div class="form-group">
            <label for="answer3">Answer 3</label>
            <input type="text" class="form-control" id="answer3" name="answers[]" required>
            <div class="radio-group">
                <input type="radio" name="correctAnswer" value="2" id="correctAnswer3">
                <label for="correctAnswer3">Correct Answer</label>
            </div>
        </div>
        <div class="form-group">
            <label for="answer4">Answer 4</label>
            <input type="text" class="form-control" id="answer4" name="answers[]" required>
            <div class="radio-group">
                <input type="radio" name="correctAnswer" value="3" id="correctAnswer4">
                <label for="correctAnswer4">Correct Answer</label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Add Question</button>
        <button id="finishButton" type="button" class="btn btn-secondary btn-block">Finish</button>
    </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.13.1/underscore-min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/backbone.js/1.4.0/backbone-min.js"></script>

<script src="<?php echo base_url('assets/js/models/question.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/view/question.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/main.js'); ?>"></script>

<script>
    var categoryId = <?php echo json_encode($categoryId); ?>;
</script>

</body>
</html>
