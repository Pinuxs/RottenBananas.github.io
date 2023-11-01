<?php 
session_start();
// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: landingpage/index.html");
    exit;
} 
if (!isset( $_SESSION["ProfileCompleted"]) ||  $_SESSION["ProfileCompleted"] !== true) {
    header("Location: newprofile.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rotten Banana - Details</title>
    <link rel="stylesheet" href="moviesDescStyles.css">
</head>

<header> 
    <nav>
        <ul> 
        <li><a href="../Home/homepage.php"><img src="../landingpage/images/cartoon-rotten-banana-KXXWCG.jpg" alt="Home" style="width: 50px; height: 40px; position: absolute; top: 0; left: 0;"></a></li>
                <li><a href="../Home/homepage.php" id="Homelink">Home</a></li>
                <li><a href="../Movies/Movies.php" id="MovieLink">Movies</a></li>
                <li><a href="../Favourites/FavouritesPage.php">Favourites</a></li>
                <li><a href="../Home/explore.php">Explore</a></li>
            
            <li style="float:right"><a href="../Home/logout.php">logout</a></li>
            <li style="float:right"><a href="../Profile/profile.php"><?php echo $_SESSION["username"]; ?></a></li>
        </ul>  
    </nav>
</header>

<body>
<div class="movie-details-container">

    <div id="ClickedMovie-container">
        <!-- Movie card will be displayed here -->

        </div>
        <div class="button-container">
    <button id="addToFavoritesButton">Add to Favorites</button>
    <button id="addToWatchlistButton">Add to Watchlist</button>
</div>
</div>

    <p></p>
    <p></p>
    <p></p>
    <h2 class="Title">Similar Movies</h2>
    <div class="similar-movie-cards">
        <div class="similar-movie-card" id="movie-cards">
            <!-- Similar movies will go here -->
        </div>
    </div>

    <script src="descriptionScript.js">
        // JavaScript code (as provided in the previous answer) goes here
    </script>

    <script>
// JavaScript code to handle the button click event
// JavaScript code to handle the button click event
document.addEventListener("DOMContentLoaded", function () {
    var addToFavoritesButton = document.getElementById("addToFavoritesButton");
    var addToWatchlistButton = document.getElementById("addToWatchlistButton");
    var movieId = <?php echo json_encode($_GET['id']); ?>;

    // Function to update button text based on whether the movie is in favorites or watchlist
    function updateButtonText() {
        // Send an AJAX request to check if the movie is in favorites
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = xhr.responseText;
                if (response === "inFavorites") {
                    addToFavoritesButton.innerText = "Remove from Favorites";
                } else if (response === "notInFavorites") {
                    addToFavoritesButton.innerText = "Add to Favorites";
                } else {
                    // Handle error or not logged in
                }
            }
        };

        xhr.open("GET", "check_favorite.php?movieId=" + movieId, true);
        xhr.send();
        
        // Send an AJAX request to check if the movie is in the watchlist
        var xhrWatchlist = new XMLHttpRequest();
        xhrWatchlist.onreadystatechange = function () {
            if (xhrWatchlist.readyState === 4 && xhrWatchlist.status === 200) {
                var response = xhrWatchlist.responseText;
                if (response === "inWatchlist") {
                    addToWatchlistButton.innerText = "Remove from Watchlist";
                } else if (response === "notInWatchlist") {
                    addToWatchlistButton.innerText = "Add to Watchlist";
                } else {
                    // Handle error or not logged in
                }
            }
        };

        xhrWatchlist.open("GET", "check_watchlist.php?movieId=" + movieId, true);
        xhrWatchlist.send();
    }

    // Initial button text setup
    updateButtonText();

    // Add click event listener to toggle favorites
    addToFavoritesButton.addEventListener("click", function () {
        // Send an AJAX request to add/remove from favorites (similar to your previous code)
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Update button text after adding/removing from favorites
                updateButtonText();
                console.log(xhr.responseText);
            }
        };

        xhr.open("POST", "add_remove_favorite.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send("action=toggle&movieId=" + movieId);
    });

    // Add click event listener to toggle watchlist
    addToWatchlistButton.addEventListener("click", function () {
        // Send an AJAX request to add/remove from watchlist (similar to your previous code)
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Update button text after adding/removing from watchlist
                updateButtonText();
                console.log(xhr.responseText);
            }
        };

        xhr.open("POST", "add_remove_watchlist.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send("action=toggle&movieId=" + movieId);
    });
});


</script>
<script>
 const movieCardsList = document.querySelectorAll('.similar-movie-cards');

movieCardsList.forEach((movieCards) => {
  movieCards.addEventListener('mousedown', (e) => {
    isDragging = true;
    startX = e.clientX - movieCards.offsetLeft;
    scrollLeft = movieCards.scrollLeft;
  });

  movieCards.addEventListener('mouseup', () => {
    isDragging = false;
    movieCards.style.cursor = 'grab';
  });

  movieCards.addEventListener('mousemove', (e) => {
    if (!isDragging) return;
    e.preventDefault();
    const x = e.clientX - movieCards.offsetLeft;
    const walk = (x - startX) * 2; // Adjust the scroll speed as needed
    movieCards.scrollLeft = scrollLeft - walk;
  });

  movieCards.style.cursor = 'grab';

  movieCards.addEventListener('mouseleave', () => {
    isDragging = false;
  });
});

</script>
</body>
</html>
