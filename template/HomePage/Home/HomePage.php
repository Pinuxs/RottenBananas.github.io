<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: ../landingpage/index.html");
    exit;
}
if (!isset( $_SESSION["ProfileCompleted"]) ||  $_SESSION["ProfileCompleted"] !== true) {
    header("Location: ../Profile/newprofile.php");
    exit;
}

if (isset($_SESSION['NewUser']) && $_SESSION['NewUser'] == true) {
    $isNewUser = 1;
    $_SESSION['NewUser'] = false;
} else {   $isNewUser = 0; }


$username = $_SESSION['username'];

$userProfile = file("../landingpage/users/$username/user_profile.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$_SESSION['userprofile'] = $userProfile;
$profile = $_SESSION['userprofile'];



$apiKey = 'f8b57db9fe486acc54732ebe68b76c7b';

// Define an array of genre IDs from the $profile array
$genreIds = []; // Initialize an array to store genre IDs

foreach ($profile as $itemResult) {
    // Perform a regular expression match to extract genre IDs
    if (preg_match('/Selected Genres:\s*([\d,]+)/', $itemResult, $matches)) {
        $genreIds = explode(',', $matches[1]); // Split the comma-separated IDs into an array
        break; // Exit the loop after the first match
    }
}

// Initialize an array to store genre names
$genreNames = [];

// Fetch genre names from TMDb API for each genre ID
foreach ($genreIds as $genreId) {
    // Make an API request to TMDb to get genre details
    $genreUrl = "https://api.themoviedb.org/3/genre/movie/list?api_key=$apiKey";
    
    // Use cURL to make the request (you can use other HTTP libraries as well)
    $ch = curl_init($genreUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $genreResponse = curl_exec($ch);
    curl_close($ch);

    // Parse the JSON response
    $genreData = json_decode($genreResponse, true);

    // Find the genre name for the current genre ID
    foreach ($genreData['genres'] as $genre) {
        if ($genre['id'] == $genreId) {
            $genreNames[] = $genre['name'];
            break; // Exit the loop after finding the genre name
        }
    }
}
$results = array();
$patternResults = '/^.*?:\s*(.*)$/';

foreach ($profile as $itemResult) {
    // Perform a regular expression match on each element of the array
    if (preg_match($patternResults, $itemResult, $matches)) {
        $results[] = $matches[1];
    }
}

array_shift($results);

foreach ($results as $value) {
    // Split the comma-separated values into an array
    $values = explode(",", $value);

    // Add the values to the new array
    $genreIds[] = $values;
}


?>

<!DOCTYPE html>
<html>
<head>
    <title>Rotten Banana - Home</title>
    <link rel="stylesheet" href="stylesheet.css"> 
</head> 
<body>
    <header> 
        <nav>
            <ul> 
            <li><a href="../Home/homepage.php"><img src="../landingpage/images/cartoon-rotten-banana-KXXWCG.jpg" alt="Home" style="width: 50px; height: 40px; position: absolute; top: 0; left: 0;"></a></li>
                <li><a href="../Home/homepage.php" id="Homelink">Home</a></li>
                <li><a href="../Movies/Movies.php" id="MovieLink">Movies</a></li>
                <li><a href="../Favourites/FavouritesPage.php">Favourites</a></li>
                <li><a href="../Home/explore.php">Explore</a></li>
                
                <li style="float:right"><a href="logout.php">logout</a></li>
                <li style="float:right"><a href="../Profile/profile.php" id="Profile"><?php echo $_SESSION["username"];?></a></li>
            </ul>  
        </nav>
    </header>
    <main>

    <section id="movie-list">
    <div class="container"> 
        <h2 class="Popular-Title">Popular Movies</h2>
        <div class="movie-cards" id="movie-cards">
            <!-- JavaScript will populate this section with movie cards -->
        </div>
        </div>
    </section>

        <section id="movies-based-on-genres">
            <div>
                <div class="genre-Movies" ><?php 

                      // Iterate through the genre IDs and names
                        foreach ($genreIds as $index => $genreId) {
                            if(isset($genreNames[$index])) {

                            
                            $genreName = $genreNames[$index];
                            // Display the genre name as a section header
                            echo "<div class='title-and-button'>";
                            echo "<h2>$genreName</h2>";
                            
                            echo "<a href='../Movies/Movies.php?genre_id=$genreId'><button id='myBtn'>View all $genreName</button></a>";  
                            echo "</div>";                         
                            // Open a new row div for each genre
                            echo '<div class="movie-cards"id="movie-cards" >';
                            
                            

                            // Make the API request for movies in this genre
                            $url = "https://api.themoviedb.org/3/discover/movie?api_key=$apiKey&with_genres=$genreId";
                            $response = file_get_contents($url);
                            
                            // Parse the JSON response
                            $moviesData = json_decode($response, true);

                            // Process and display movie data
                            foreach ($moviesData['results'] as $movie) {
                                // Wrap the movie card with an anchor tag
                                echo '<a href="../movies/movie_description.php?id=' . $movie['id'] . '">';
                                echo '<div class="movie-card">';
                                
                                
                                // Display the movie poster if available (you'll need to construct the URL)
                                $posterUrl = 'https://image.tmdb.org/t/p/w185' . $movie['poster_path'];
                                echo '<img src="' . $posterUrl . '" alt="' . $movie['title'] . ' Poster">';
                                echo '</a>'; // Close the anchor tag
                                echo '</div>';
                                
                            }
                            
                            // Close the row div for this genre
                            echo '</div>';
                            
                        }else {
                            break;
                        }
                    } 
                ?></div>
            </div>

        </section>

        <div id="overlay" class="overlay">
    <div class="overlay-content">
        <h1>Welcome to Our Website!</h1>
        <p>Here's how to navigate our website:</p>
        <ul>
            <li>Click "Home" to go to the homepage.</li>
            <li>Click "Movies" to see our movie collection.</li>
            <li>Click "Favourites" to view your favorite movies.</li>
            <li>Click "Explore" to explore movies by genre.</li>
            <li>To scroll through movies click and drag</li>
        </ul>
        <button id="close-overlay">Got it</button>
    </div>
</div>
    </main>
    <script src="script.js"></script>
    <script>
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

<script>
   <?php
    if ($isNewUser == 1) {
        echo 'var isNewUser = true;';
    } else {
        echo 'var isNewUser = false;';
    }
?>

document.addEventListener("DOMContentLoaded", function () {
    if (isNewUser) {
        displayOverlay();
    }

    // Function to display the overlay
    function displayOverlay() {
        const overlay = document.getElementById('overlay');
        overlay.style.display = 'block';
    }

    // Function to close the overlay
    function closeOverlay() {
        const overlay = document.getElementById('overlay');
        overlay.style.display = 'none';
    }

    // Add an event listener to the "Got it" button to close the overlay
    const gotItButton = document.querySelector('#close-overlay');
    gotItButton.addEventListener('click', closeOverlay);
});

</script>


</body>
</html>