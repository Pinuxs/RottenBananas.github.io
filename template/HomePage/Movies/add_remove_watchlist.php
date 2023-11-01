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

    // Define the path to the user's folder and watchlist.txt file
    $userFolder = "../landingpage/users/" . $username;
    $watchlistFilePath = $userFolder . '/watchlist.txt';

    // Get the user's watchlist from the session (if available)
    $watchlist = isset($_SESSION['watchlist']) ? $_SESSION['watchlist'] : array();

    // Handle the 'toggle' action to add/remove the movie from the watchlist
    if ($action === "toggle") {
        // Check if the movieId is already in the user's watchlist
        $index = array_search($movieId, $watchlist);

        if ($index !== false) {
            // Movie is already in the watchlist, remove it
            unset($watchlist[$index]);
            $_SESSION['watchlist'] = array_values($watchlist);

            // Write the updated watchlist to the user's watchlist.txt file
            file_put_contents($watchlistFilePath, implode(',', $watchlist));
            echo "removed";
        } else {
            // Movie is not in the watchlist, add it
            $watchlist[] = $movieId;
            $_SESSION['watchlist'] = $watchlist;

            // Write the updated watchlist to the user's watchlist.txt file
            file_put_contents($watchlistFilePath, implode(',', $watchlist));
            echo "added";
        }
    } else {
        echo "Invalid action.";
    }
} else {
    echo "Action or movieId not provided.";
}
?>
