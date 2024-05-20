<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Categories</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/home.css'); ?>">

</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <h1 class="mb-4">Search Categories</h1>
            <div class="input-group mb-3">
                <input type="text" class="form-control" id="searchInput" placeholder="Enter category name">
                <div class="input-group-append">
                    <button class="btn btn-primary" id="searchBtn" type="button">Search</button>
                </div>
            </div>
            <div class="input-group mb-3">
                <select class="custom-select" id="difficultySelect">
                    <option value="all" selected>All Difficulty Levels</option>
                    <option value="easy">Easy</option>
                    <option value="normal">Normal</option>
                    <option value="hard">Hard</option>
                </select>
                <div class="input-group-append">
                    <button class="btn btn-secondary" id="filterBtn" type="button">Filter</button>
                </div>
            </div>
            <div id="searchResults" class="mt-3"></div>
            <div class="mt-3">
                <button class="btn btn-primary btn-block" id="addQuizBtn">Add Quiz</button>
            </div>
            <div class="mt-3">
                <button class="btn btn-secondary btn-block" id="profileBtn">Profile</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.13.1/underscore-min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/backbone.js/1.4.0/backbone-min.js"></script>

<script>
    $(document).ready(function() {
        $('#searchBtn').on('click', function() {
            var searchTerm = $('#searchInput').val().trim(); // Get the search term from input
            var difficulty = $('#difficultySelect').val();

            // Perform AJAX request to search for categories
            $.ajax({
                url: 'http://localhost/quizbuddy/index.php/api/category/search',
                method: 'GET', // HTTP method
                data: { search: searchTerm, difficulty: difficulty },
                dataType: 'json', // Expected data type from the server
                success: function(response) {
                    console.log('Response:', response);
                    if (response && response.categories && response.categories.length > 0) {
                        displaySearchResults(response.categories, difficulty);
                    } else {
                        $('#searchResults').html('<p>No categories found.</p>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error); // Log the error
                    $('#searchResults').html('<p>Error occurred while searching categories.</p>'); // Display error message
                }
            });
        });

        // Event listener for filter button click
        $('#filterBtn').on('click', function() {
            $('#searchBtn').trigger('click');
        });

        // Event listener for add quiz button click
        $('#addQuizBtn').on('click', function() {
            window.location.href = 'http://localhost/quizbuddy/index.php/category/add';
        });

        // Event listener for profile button click
        $('#profileBtn').on('click', function() {
            window.location.href = 'http://localhost/quizbuddy/index.php/api/user/profile_view';
        });

        // Attach click event listener to search result items
        $('#searchResults').on('click', 'li', function() {
            // Retrieve the CategoryID, CategoryName, and Visibility from the data attributes
            var categoryId = $(this).data('category-id');
            var categoryName = $(this).data('category-name');
            var visibility = $(this).data('visibility');
            // Redirect to quiz page with the selected category ID, name, and visibility
            window.location.href = 'http://localhost/quizbuddy/index.php/api/quiz/welcome?categoryId=' + categoryId + '&categoryName=' + categoryName + '&visibility=' + visibility;
        });
    });

    // Function to display search results
    function displaySearchResults(categories, difficulty) {
        console.log('Categories:', categories);
        var resultsHtml = '<ul class="list-group">';
        // Iterate through each category and generate HTML
        categories.forEach(function(category) {
            // Check if the category matches the selected difficulty or if difficulty is 'all'
            if (difficulty === 'all' || category.Level.toLowerCase() === difficulty) {
                resultsHtml += '<li class="list-group-item" data-category-id="' + category.CategoryID + '" data-category-name="' + category.CategoryName + '" data-visibility="' + category.Visibility + '">' + category.CategoryName + ' (' + category.Level + ') - Visibility: ' + category.Visibility + '</li>';
            }
        });
        resultsHtml += '</ul>';
        $('#searchResults').html(resultsHtml);
    }
</script>


</body>
</html>
