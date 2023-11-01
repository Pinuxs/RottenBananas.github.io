<?php session_start();

if (!isset( $_SESSION["ProfileCompleted"]) ||  $_SESSION["ProfileCompleted"] !== true) {
    header("Location: ../Profile/newprofile.php");
    exit;
}
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] !== true) {
    header("location: landingpage/index.html");
    exit;
} 
$username = $_SESSION['username'];
$userFolder = "../landingpage/users/" . $username;
$userDetails = file("$userFolder/userdetails.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$email = $userDetails[2];
$profile = $_SESSION['userprofile'];
$apiKey = 'f8b57db9fe486acc54732ebe68b76c7b';
// Initialize an array to store the titles
$titles = array();
$results = array();

// Define a regular expression pattern to match titles (words before the colon)
$patternTitle = '/^(.*?):/m';
$patternResults = '/^.*?:\s*(.*)$/';
// Perform a regular expression match and store the results in $matches
foreach ($profile as $itemTitle) {
    // Perform a regular expression match on each element of the array
    if (preg_match($patternTitle, $itemTitle, $matches)) {
        $titles[] = $matches[1];
    }
}
foreach ($profile as $itemResult) {
    // Perform a regular expression match on each element of the array
    if (preg_match($patternResults, $itemResult, $matches)) {
        $results[] = $matches[1];
    }
}


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

?>



<!DOCTYPE html>
<html>
<head>
    <title>Profile Page</title>
    <link rel="stylesheet" type="text/css" href="../Home/stylesheet.css"> <!-- You can link your CSS file here -->
</head>
<body>
<header> 
        <nav>
            <ul> 
            <li><a href="../Home/homepage.php"><img src="../landingpage/images/cartoon-rotten-banana-KXXWCG.jpg" alt="Home" style="width: 50px; height: 40px; position: absolute; top: 0; left: 0;"></a></li>
            <li><a href="../Home/homepage.php" id="Homelink">Home</a></li>
                <li><a href="../movies/Movies.php" id="MovieLink">Movies</a></li>
                <li><a href="../Favourites/FavouritesPage.php">Favourites</a></li>
                <li><a href="../Home/explore.php">Explore</a></li>

                <li style="float:right"><a href="../Home/logout.php">logout</a></li>
                <li style="float:right"><a href="../Profile/profile.php" id="Profile"><?php echo $_SESSION["username"];?></a></li>
            </ul>  
        </nav>
    </header>
    <main>
        <section>
            <h2>Your Profile Information</h2>
            <?php
            echo "<p>Username:{$_SESSION["username"]}</p>";
            print_r($titles[0] . ": " . $results[0] . "<br>") ;
            echo "<br>";
            echo "<p>$email</p>";
            echo "<br>";
            print_r($titles[1] . ": <br>") ;
            foreach ($genreNames as $genreName) {
                echo "<li>$genreName</li>";
            }
            ?>
        </section>
        <br>
        <button onclick="goToPage()">Edit Profile</button>
       <script>
        function goToPage() {
            // Redirect to the desired page when the button is clicked
            window.location.href = "NewProfile.php";
        }
    </script>
    </main>
    <footer>
        <p>&copy; 2023 Rotten Bananas</p>
    </footer>
</body>
</html>