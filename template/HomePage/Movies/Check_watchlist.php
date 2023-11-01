<?php
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    // Get the movie ID from the query string
    $movieId = $_GET['movieId'];
    $username = $_SESSION["username"];
    $filePath = "../landingpage/users/$username/watchlist.txt";

    // Check if the movie ID is in the watchlist file
    $watchlist = file_get_contents($filePath);
    if ($watchlist !== false && strpos($watchlist, $movieId) !== false) {
        // Movie ID is in the watchlist, return a response indicating it
        echo "inWatchlist";
    } else {
        // Movie ID is not in the watchlist, return a response indicating it
        echo "notInWatchlist";
    }
} else {
    // User is not logged in, return an error response
    echo "notLoggedIn";
}
?>
