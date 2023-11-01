<?php
session_start();
// Check if the users folder exists
if (!is_dir('users')) {
    // If the folder doesn't exist, create it
    mkdir('users');
}

// Get the user's name, password, and email from the form
$username = $_POST['username'];
$password = $_POST['password'];
$email = $_POST['email'];

// Check if the provided email is already associated with another account
$emailExists = false;
$userFolders = scandir('users');
foreach ($userFolders as $userFolder) {
    if ($userFolder !== '.' && $userFolder !== '..') {
        $userDetailsFile = 'users/' . $userFolder . '/userdetails.txt';
        if (file_exists($userDetailsFile)) {
            $userDetails = file($userDetailsFile, FILE_IGNORE_NEW_LINES);
            if (count($userDetails) >= 3 && trim($userDetails[2]) === $email) {
                $emailExists = true;
                break;
            }
        }
    }
}

if ($emailExists) {
    // The provided email is associated with another account
    echo "<script>alert('Email already in use');</script>";
    echo "<script>location.href = 'index.html';</script>";
    exit;
}

// Check if the password meets the minimum length requirement (e.g., 8 characters)
if (strlen($password) < 8) {
    echo "<script>alert('Password must be at least 8 characters long');</script>";
    echo "<script>location.href = 'index.html';</script>";
    exit;
}

// If the email is not associated with another account and the password meets the length requirement, create the user's folder
if (!is_dir('users/' . $username)) {
    // If the folder doesn't exist, create it
    mkdir('users/' . $username);

    // Create the user's files
    $userdetails = fopen('users/' . $username . '/userdetails.txt', 'w');
    $favourites = fopen('users/' . $username . '/favourites.txt', 'w');
    $userProfile = fopen('users/' . $username . '/user_profile.txt', 'w');

    // Write the user's details to the files
    fwrite($userdetails, $username . "\n" . $password . "\n" . $email);
    fwrite($favourites, "");

    // Close the files
    fclose($userdetails);
    fclose($favourites);

    $_SESSION["loggedin"] = true;
    $_SESSION["username"] = $username;
    $_SESSION["NewUser"] = true;

    // Redirect the user to the new URL
    echo "<script>location.href = '../Home/HomePage.php';</script>";
} else {
    echo "<script>alert('Username already in use');</script>";
    echo "<script>location.href = 'index.html';</script>";
    exit;
}
?>
