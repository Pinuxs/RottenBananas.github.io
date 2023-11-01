<?php
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    // Get the movie ID from the query string
    $movieId = $_GET['movieId'];
    $username = $_SESSION["username"];
    $filePath = "../landingpage/users/$username/favourites.txt";

    // Check if the movie ID is in the favorites file
    $favorites = file_get_contents($filePath);
    if ($favorites !== false && strpos($favorites, $movieId) !== false) {
        // Movie ID is in favorites, return a response indicating it
        echo "inFavorites";
    } else {
        // Movie ID is not in favorites, return a response indicating it
        echo "notInFavorites";
    }
} else {
    // User is not logged in, return an error response
    echo "notLoggedIn";
}
?>
