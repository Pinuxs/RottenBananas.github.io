<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo "User not logged in.";
    exit;
}

// Check if the 'action' and 'movieId' parameters are provided in the POST request
if (isset($_POST['action']) && isset($_POST['movieId'])) {
    $action = $_POST['action'];
    $movieId = $_POST['movieId'];
    $username = $_SESSION["username"];

    // Define the path to the user's folder and favourite.txt file
    $userFolder = "../landingpage/users/" . $username;
    $favouriteFilePath = $userFolder . '/favourites.txt';

    // Get the user's favorites from the session (if available)
    $favorites = isset($_SESSION['favorites']) ? $_SESSION['favorites'] : array();

    // Handle the 'toggle' action to add/remove the movie from favorites
    if ($action === "toggle") {
        // Check if the movieId is already in the user's favorites
        $index = array_search($movieId, $favorites);

        if ($index !== false) {
            // Movie is already in favorites, remove it
            unset($favorites[$index]);
            $_SESSION['favorites'] = array_values($favorites);

            // Write the updated favorites to the user's favourite.txt file
            file_put_contents($favouriteFilePath, implode(',', $favorites));
            echo "removed";
        } else {
            // Movie is not in favorites, add it
            $favorites[] = $movieId;
            $_SESSION['favorites'] = $favorites;

            // Write the updated favorites to the user's favourite.txt file
            file_put_contents($favouriteFilePath, implode(',', $favorites));
            echo "added";
        }
    } else {
        echo "Invalid action.";
    }
} else {
    echo "Action or movieId not provided.";
}
?>
