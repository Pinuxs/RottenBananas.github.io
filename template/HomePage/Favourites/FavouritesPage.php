<?php 
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: ../landingpage/index.html");
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Rotten Bananas - Favourites</title>
    <link rel="stylesheet" href="FavouriteStyles.css"> 
</head> 
<body>
    <header> 
        <nav>
            <ul> 
            <li><a href="../Home/homepage.php"><img src="../landingpage/images/cartoon-rotten-banana-KXXWCG.jpg" alt="Home" style="width: 50px; height: 40px; position: absolute; top: 0; left: 0;"></a></li>
            <li><a href="../Home/homepage.php" id="Homelink">Home</a></li>
                <li><a href="../Movies/Movies.php" id="MovieLink">Movies</a></li>
                <li><a href="FavouritesPage.php">Favourites</a></li>
                <li><a href="../Home/explore.php">Explore</a></li>
                
                <li style="float:right"><a href="../Home/logout.php">logout</a></li>
                <li style="float:right"><a href="../Profile/profile.php" id="Profile"><?php echo $_SESSION["username"];?></a></li>
            </ul>  
        </nav>
    </header>
<?php

///////////////////// FUNCTIONS ////////////////////////////
// Function to fetch movie details from TMDb API
function getMovieDetails($movieId) {
    $apiKey = "f8b57db9fe486acc54732ebe68b76c7b"; 
    $url = "https://api.themoviedb.org/3/movie/{$movieId}?api_key={$apiKey}&language=en-US";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

$username = $_SESSION["username"];
$userFolder = "../landingpage/users/" . $username;
$filePath = "{$userFolder}/favourites.txt";


if (file_exists($filePath)) {
    // Read the list of movie IDs from favourites.txt
    $movieIds = file_get_contents($filePath);

    if ($movieIds !== false) {
        $movieIds = explode(',', $movieIds);

        echo "<html>";
        echo "<head>";
        echo "<title>Favourites</title>";
        echo "<link rel='stylesheet' href='moviesDescStyles.css'>";
        echo "</head>";
        echo "<body>";

        echo "<div class='container'>";
        echo "<h1>Your Favourite Movies</h1>";
        
        echo "<div class='movie-cards'>";

        foreach ($movieIds as $movieId) {
            // Fetch movie details from TMDb API
            $movieDetails = getMovieDetails($movieId);
        
            // Check if movie details exist and have the required keys
            if ($movieDetails && isset($movieDetails["poster_path"])) {
                $title = $movieDetails["title"];
                $overview = $movieDetails["overview"];
                $posterPath = $movieDetails["poster_path"];
                $posterURL = "https://image.tmdb.org/t/p/w500{$posterPath}";
        
                // Display movie details with a data attribute for movie ID
                echo "<div class='movie-card' data-movie-id='{$movieId}'>";
                echo "<img src='{$posterURL}' alt='{$title}' />";
                echo "</div>";
            } else {
                // Display a message for "No movies"
                
                echo "<p>No selected favourite movies.</p>";
                
            }
        }
        

        echo "</div>"; // .movie-card
        echo "</div>"; // .container
        
        echo "</body>";
        echo "</html>";
    } else {
        echo "Error reading favourites.txt";
    }
} else {
    echo "Favourites not found for the user.";
}
?>


<!-- Add this JavaScript code inside the HTML body, after your PHP code -->
<script>
    // Function to navigate to the movie description page with the given movie ID
    function goToMovieDescription(movieId) {
        window.location.href = `../Movies/movie_description.php?id=${movieId}`;
    }

    // Attach a click event listener to each movie card
    document.addEventListener("DOMContentLoaded", function () {
        const movieCards = document.querySelectorAll('.movie-card');

        movieCards.forEach((card) => {
            card.addEventListener('click', () => {
                // Extract the movie ID from a data attribute or other source
                const movieId = card.getAttribute('data-movie-id');
                goToMovieDescription(movieId);
            });
        });
    });

 const movieCardsList = document.querySelectorAll('.movie-cards');

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

<?php
// ... Your existing PHP code ...

// Check if watchlist.txt exists
$watchlistFilePath = "{$userFolder}/watchlist.txt";

if (file_exists($watchlistFilePath)) {
    // Read the list of movie IDs from watchlist.txt
    $watchlistMovieIds = file_get_contents($watchlistFilePath);

    if ($watchlistMovieIds !== false) {
        $watchlistMovieIds = explode(',', $watchlistMovieIds);

        echo "<div class='container'>";
        echo "<h1>Your Watchlist Movies</h1>";

        echo "<div class='movie-cards'>";

        foreach ($watchlistMovieIds as $watchlistMovieId) {
            // Fetch movie details from TMDb API for watchlist
            $watchlistMovieDetails = getMovieDetails($watchlistMovieId);

            // Check if watchlist movie details exist and have the required keys
            if ($watchlistMovieDetails && isset($watchlistMovieDetails["poster_path"])) {
                $watchlistTitle = $watchlistMovieDetails["title"];
                $watchlistOverview = $watchlistMovieDetails["overview"];
                $watchlistPosterPath = $watchlistMovieDetails["poster_path"];
                $watchlistPosterURL = "https://image.tmdb.org/t/p/w500{$watchlistPosterPath}";

                // Display watchlist movie details with a data attribute for movie ID
                echo "<div class='movie-card' data-movie-id='{$watchlistMovieId}'>";
                echo "<img src='{$watchlistPosterURL}' alt='{$watchlistTitle}' />";
                echo "</div>";
            } else {
                // Display a message for "No movies" in the watchlist
                echo "<p>No movies in your watchlist.</p>";
            }
        }

        echo "</div>"; // .movie-card
        echo "</div>"; // .container
    } else {
        echo "Error reading watchlist.txt";
    }
} else {
    echo "Watchlist not found for the user.";
}
?>
