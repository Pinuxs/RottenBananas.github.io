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

 $genreMapping = [];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rotten Bananas - Explore</title>
    <link rel="stylesheet" href="stylesheet.css"> 
</head> 
<body>
    <header> 
        <nav>
            <ul> 
            <li>
            <li><a href="../Home/homepage.php"><img src="../landingpage/images/cartoon-rotten-banana-KXXWCG.jpg" alt="Home" style="width: 50px; height: 40px; position: absolute; top: 0; left: 0;"></a></li>
                <a href="homepage.php" id="Homelink">Home</a></li>
                <li><a href="../Movies/Movies.php" id="MovieLink">Movies</a></li>
                <li><a href="../Favourites/FavouritesPage.php">Favourites</a></li>
                <li><a href="explore.php">Explore</a></li>

                <li style="float:right"><a href="../Home/logout.php">logout</a></li>
                <li style="float:right"><a href="../Profile/profile.php" id="Profile"><?php echo $_SESSION["username"];?></a></li>
            </ul>  
        </nav>
    </header>


<?php

$api_key = 'f8b57db9fe486acc54732ebe68b76c7b';

$username = $_SESSION["username"];
$userFolder = "../landingpage/users/" . $username;
$filePath = "{$userFolder}/favourites.txt";
$favoruiteDetails = file("$filePath", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$favoritesString = implode(",", $favoruiteDetails);
$favouritedetails = explode(",", $favoritesString);
$genreCounts = [];


//creates a loop for every movie id it pulls the associated genres
foreach ($favouritedetails as $favouritedetail) {


$movie_id = $favouritedetail;
$url = "https://api.themoviedb.org/3/movie/$movie_id?api_key=$api_key";

// Initialize cURL session
$ch = curl_init($url);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the cURL session
$response = curl_exec($ch);

// Check for cURL errors
if ($response === false) {
    die('Curl error: ' . curl_error($ch));
}

// Close the cURL session
curl_close($ch);

// Decode the JSON response
$movieData = json_decode($response, true);

if (isset($movieData['genres'])) {
    $genres = $movieData['genres'];

    // Extract genre names and update the genre counts
    foreach ($genres as $genre) {
        $genreName = $genre['name'];
        if (isset($genreCounts[$genreName])) {
            $genreCounts[$genreName]++;
        } else {
            $genreCounts[$genreName] = 1;
        }
    }

} else {
    echo "Failed to retrieve movie data.";
}
}

echo"<h1>Recommendations based on your favourites</h2>";


arsort($genreCounts); // Sort the genre counts in descending order
$topGenres = array_slice($genreCounts, 0, 2); // Get the top two genres
$highestCountedGenre = key($genreCounts); // Get the highest counted genre

// Combine the top two genres for the API request
$topGenreIds = array_keys($topGenres);
$combinedGenres = implode(",", $topGenreIds);

// Fetch the list of movie genres from TMDb
$genreUrl = "https://api.themoviedb.org/3/genre/movie/list?api_key=$api_key";
$genreResponse = file_get_contents($genreUrl);
$genreData = json_decode($genreResponse, true);

if (isset($genreData['genres'])) {
    $genresList = $genreData['genres'];

    // Create an array to store the genre mapping
    $genreMapping = [];

    foreach ($genresList as $genre) {
        // Store the genre name and ID in the mapping
        $genreMapping[$genre['name']] = $genre['id'];
    }

    // Split the combined genres into individual genre names
    $combinedGenreNames = explode(",", $combinedGenres);

    // Convert genre names to genre IDs
    $selectedGenreIds = array_map(function ($genreName) use ($genreMapping) {
        return $genreMapping[$genreName];
    }, $combinedGenreNames);

    // Create a comma-separated string of genre IDs
    $selectedGenreIdsString = implode(",", $selectedGenreIds);

    // Fetch and print movies that belong to the selected genres (by genre IDs)
    $url = "https://api.themoviedb.org/3/discover/movie?api_key=$api_key&with_genres=$selectedGenreIdsString";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $movieData = json_decode($response, true);

$combinedTitle = implode(",", $combinedGenreNames);

    // Display the movies with links to description pages
    if (isset($movieData['results'])) {
        echo "<div class='title-and-button'>";
        echo "<h2>Your top two genres:</h2>";
        echo "<a href='../Movies/Movies.php?genre_id=$selectedGenreIds[0]' style=''><button id='myBtn'>View all $combinedTitle</button></a>";  
        echo "</div>";
        echo "<div class='movie-cards'>";
        foreach ($movieData['results'] as $movie) {
            echo "<div class='col-md-6'>";
            echo "<div class='movie-card'>";
            
            // Create a link to the description page with the movie's ID
            echo '<a href="../movies/movie_description.php?id=' . $movie['id'] . '">';
            echo "<img src='https://image.tmdb.org/t/p/w185{$movie['poster_path']}' alt='{$movie['title']}'>";
            echo "</a>";
            
            echo "</div>";
            echo "</div>";
        }
        echo "</div>";
    } else {
        echo "Failed to retrieve movies for genres: " . implode(", ", $combinedGenreNames);
    }
} else {
    echo "Failed to retrieve genre list from TMDb.";
}




// Find the third most counted genre
$genreCounts = array_diff_key($genreCounts, $topGenres); // Remove the top two genres
arsort($genreCounts); // Sort the remaining genre counts
$thirdMostCountedGenre = key($genreCounts);

if (isset($genreData['genres'])) {
$genresList = $genreData['genres'];

// Create an array to store the genre mapping

$genreMapping = array();

foreach ($genresList as $genre) {
    // Store the genre name and ID in the mapping
    $genreMapping[$genre['name']] = $genre['id'];
}

// Split the combined genres into individual genre names
$ThirdGenreName = explode(",", $thirdMostCountedGenre);

// Convert genre names to genre IDs
$selectedGenreIds = array_map(function ($genreName) use ($genreMapping) {
    return $genreMapping[$genreName];
}, $ThirdGenreName);

// Create a comma-separated string of genre IDs
$selectedGenreIdsString = implode(",", $selectedGenreIds);
$genretitle = implode(", ", $ThirdGenreName);
// Display movies for the third most counted genre
echo "<div class='row'>";
echo "<div class='col-md-12'>";
echo "<div class='title-and-button'>";
echo "<h2>Third Most liked genre</h2>";
echo "<a href='../Movies/Movies.php?genre_id=$selectedGenreIdsString' style=''><button id='myBtn'>View all $genretitle</button></a>";  
echo "</div>";

// Fetch and print movies of the third most counted genre
$url = "https://api.themoviedb.org/3/discover/movie?api_key=$api_key&with_genres=" . $selectedGenreIdsString;
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$movieData = json_decode($response, true);

if (isset($movieData['results'])) {

    echo "<div class='movie-cards'>";
    foreach ($movieData['results'] as $movie) {
        echo '<a href="../movies/movie_description.php?id=' . $movie['id'] . '">';
        echo "<div class='movie-card'>";
        echo "<img src='https://image.tmdb.org/t/p/w185{$movie['poster_path']}' alt='{$movie['title']}'>";
        echo "</div>";
        echo "</a>";
    }
    echo "</div>";
} else {
    echo "Failed to retrieve movies for genre: $thirdMostCountedGenre";
}

echo "</div>";
echo "</div>";
} else {
    echo "Failed to retrieve genre list from TMDb.";
}

$watchlistFilePath = "{$userFolder}/watchlist.txt";
$watchlistDetails = file("$watchlistFilePath", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$watchlistString = implode(",", $watchlistDetails);
$watchlist = explode(",", $watchlistString);

$watchlistGenreCounts = [];

// Iterate through movies in the watchlist to gather genre information
foreach ($watchlist as $watchlistMovieId) {
    $url = "https://api.themoviedb.org/3/movie/$watchlistMovieId?api_key=$api_key";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $movieData = json_decode($response, true);

    if (isset($movieData['genres'])) {
        $genres = $movieData['genres'];

        // Extract genre names and update the genre counts
        foreach ($genres as $genre) {
            $genreName = $genre['name'];
            if (isset($watchlistGenreCounts[$genreName])) {
                $watchlistGenreCounts[$genreName]++;
            } else {
                $watchlistGenreCounts[$genreName] = 1;
            }
        }
    } else {
        echo "Failed to retrieve movie data for movie ID: $watchlistMovieId.";
    }
}
echo"<h1>Recommendations based on your watchlist</h2>";

// Sort the watchlist genre counts in descending order
arsort($watchlistGenreCounts);

// Get the top two genres from the watchlist
$watchlistTopGenres = array_slice($watchlistGenreCounts, 0, 2);
$watchlistCombinedGenres = implode(",", array_keys($watchlistTopGenres));

// Fetch the list of movie genres from TMDb (if you haven't already)
$genreUrl = "https://api.themoviedb.org/3/genre/movie/list?api_key=$api_key";
$genreResponse = file_get_contents($genreUrl);
$genreData = json_decode($genreResponse, true);

if (isset($genreData['genres'])) {
    $genresList = $genreData['genres'];

    // Create an array to store the genre mapping
    $genreMapping = [];

    foreach ($genresList as $genre) {
        // Store the genre name and ID in the mapping
        $genreMapping[$genre['name']] = $genre['id'];
    }

    // Split the combined genres into individual genre names
    $combinedGenreNames = explode(",", $watchlistCombinedGenres);

    // Convert genre names to genre IDs
    $selectedGenreIds = array_map(function ($genreName) use ($genreMapping) {
        return $genreMapping[$genreName];
    }, $combinedGenreNames);

    // Create a comma-separated string of genre IDs
    $selectedGenreIdsString = implode(",", $selectedGenreIds);

    // Fetch and print movies that belong to the selected genres (by genre IDs)
    $url = "https://api.themoviedb.org/3/discover/movie?api_key=$api_key&with_genres=$selectedGenreIdsString";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $movieData = json_decode($response, true);

    // Display the movies with links to description pages
    if (isset($movieData['results'])) {
        echo "<div class='title-and-button'>";
        echo "<h2>Your top two watchlist genres:</h2>";
        echo "<a href='../Movies/Movies.php?genre_id=$selectedGenreIds[0]' style=''><button id='myBtn'>View all $watchlistCombinedGenres</button></a>";
        echo "</div>";
        echo "<div class='movie-cards'>";
        foreach ($movieData['results'] as $movie) {
            echo "<div class='col-md-6'>";
            echo "<div class='movie-card'>";

            // Create a link to the description page with the movie's ID
            echo '<a href="../movies/movie_description.php?id=' . $movie['id'] . '">';
            echo "<img src='https://image.tmdb.org/t/p/w185{$movie['poster_path']}' alt='{$movie['title']}'>";
            echo "</a>";

            echo "</div>";
            echo "</div>";
        }
        echo "</div>";
    } else {
        echo "Failed to retrieve movies for genres: " . implode(", ", $combinedGenreNames);
    }
} else {
    echo "Failed to retrieve genre list from TMDb.";
}

// Function to calculate Jaccard Similarity between two arrays of genres
function calculateJaccardSimilarity($userGenres, $otherUserGenres) {
    $intersection = count(array_intersect($userGenres, $otherUserGenres));
    $union = count(array_unique(array_merge($userGenres, $otherUserGenres)));
    return $intersection / $union;
}

// Read the current user's genre choices
$currentUserId = $username; // Replace with the current user's username
$userProfileFile = "../landingPage/users/$currentUserId/user_profile.txt";
if (file_exists($userProfileFile)) {
    $currentUserProfileData = file_get_contents($userProfileFile);
    // Extract the selected genres part
    if (preg_match('/Selected Genres: (.*?)$/', $currentUserProfileData, $matches)) {
        $currentUserGenres = explode(',', $matches[1]);
    } else {
        // Handle the case where the format doesn't match
        // You can log an error or take other appropriate action.
    }
} else {
    // Handle the case where the user's profile file doesn't exist
    // You can log an error or take other appropriate action.
}
// Initialize an array to store user similarity scores
$userSimilarityScores = array();

// Iterate through all users
$usersDirectory = "../landingPage/users";
$allUsers = array_diff(scandir($usersDirectory), array('..', '.'));
foreach ($allUsers as $userId) {
    if ($userId !== $currentUserId) {
        $otherUserGenres = explode(',', file_get_contents("$usersDirectory/$userId/user_profile.txt"));
        $similarity = calculateJaccardSimilarity($currentUserGenres, $otherUserGenres);
        $userSimilarityScores[$userId] = $similarity;
    }
}

// Sort the users by similarity in descending order
arsort($userSimilarityScores);

// Get the top two closest users
$closestUsers = array_slice($userSimilarityScores, 0, 2);


// Assuming you have already fetched the top two closest users and stored their IDs in $closestUsers
foreach ($closestUsers as $userId => $similarity) {
    // Make API requests for movie recommendations based on the genres of these users

    $profileData = file_get_contents("../landingPage/users/$userId/user_profile.txt");

    // Extract the selected genres part
    if (preg_match('/Selected Genres: (.*?)$/', $profileData, $matches)) {
        $selectedGenres = $matches[1];
        // Split the selected genres into an array
        $closestUserGenres = explode(',', $selectedGenres);
    } else {
        // Handle the case where the format doesn't match
        // You can log an error or take other appropriate action.
    }


    // Calculate the common genres between the current user and the closest user
    $commonGenres = array_intersect($currentUserGenres, $closestUserGenres);

    // If there are common genres, you can use them to make API requests
    if (!empty($commonGenres)) {
        $selectedGenreIds = array(); // Store the genre IDs for API requests
        
        if (!isset($myGenreMapping) || empty($myGenreMapping)) {
            $myGenreMapping = array(); // Initialize the genre mapping array
        
            // Fetch the list of movie genres from TMDb
            $genreUrl = "https://api.themoviedb.org/3/genre/movie/list?api_key=$api_key";
            $genreResponse = file_get_contents($genreUrl);
            $genreData = json_decode($genreResponse, true);
        
            if (isset($genreData['genres'])) {
                foreach ($genreData['genres'] as $genre) {
                    $myGenreMapping[$genre['id']] = $genre['name']; // Use genre IDs as keys
                }
            } else {
                echo "Failed to fetch genre data from TMDb.";
            }
        } 

        // Convert common genre names to genre IDs
        foreach ($commonGenres as $genreName) {
            if (isset($myGenreMapping[$genreName])) {
                $selectedGenreIds[] = $myGenreMapping[$genreName];
            } else {
                echo $genreName;
                echo "ERROR: Genre '$genreName' not found in the mapping.";
                error_log("Genre '$genreName' not found in the mapping.");
            }
        }

        // Create a comma-separated string of genre IDs
        $selectedGenreIdsString = implode(",", $selectedGenreIds);
        // Fetch and print movie recommendations for the current user based on the common genres
        $url = "https://api.themoviedb.org/3/discover/movie?api_key=$api_key&with_genres=$genreName";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $movieData = json_decode($response, true);

        // Display the movie recommendations for the current user
        if (isset($movieData['results'])) {
            echo "<div class='title-and-button'>";
            echo "<h2>Movie Recommendations based on similar genres with $userId</h2>";
            echo "<a href='../Movies/Movies.php?genre_id=$genreName' style=''><button id='myBtn'>View all $selectedGenreIdsString</button></a>";  
            echo "</div>";
            echo "<div class='movie-cards'>";
            foreach ($movieData['results'] as $movie) {
                echo '<a href="../movies/movie_description.php?id=' . $movie['id'] . '">';
                echo "<div class='movie-card'>";
                echo "<img src='https://image.tmdb.org/t/p/w185{$movie['poster_path']}' alt='{$movie['title']}'>";
                echo "</div>";
                echo "</a>";
            }
            echo "</div>";
        } else {
            echo "No movie recommendations found based on common genres with $userId";
        }
    }
}


// Output or display the movie recommendations to the user
?>






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
    // Check the number of favorite movies
    var numberOfFavorites = <?php echo count($favouritedetails); ?>;
    
    // Check if there are less than 3 favorite movies
    if (numberOfFavorites < 3) {
        // Display an alert
        alert("You must add at least 3 favorites before you can look for recommendations.");
                // Redirect to the movie page (change the URL to your desired destination)
                window.location.href = '../Movies/Movies.php';
    }
</script>

</body>
</html>