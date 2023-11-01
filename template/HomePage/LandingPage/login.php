<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
 
    // Define the path to the user's folder
    $userFolder = "users/" . $username;

    // Check if the user's folder exists and the userdetails file exists within it
    if (file_exists($userFolder) && file_exists("$userFolder/userdetails.txt")) {
        // Read the userdetails.txt file
        $userDetails = file("$userFolder/userdetails.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $userProfile = file("$userFolder/user_profile.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);



        // Check if the entered username and password match the stored credentials
        if ($userDetails[0] === $username && $userDetails[1] === $password) {
            // Set session variables to indicate the user is logged in
            $_SESSION["loggedin"] = true;
            $_SESSION["username"] = $username;
            
            
           
            
            // Redirect to a protected page (e.g., dashboard.php)
            if (count($userProfile) >= 1) {
                header("location: ../Home/homepage.php");
                $_SESSION["ProfileCompleted"] = true;
                $_SESSION['userprofile'] = $userProfile;
                
            }else {
                header("Location: ../Profile/NewProfile.php");}
               
            exit;
        } else {
            echo "<script>alert('Invalid username or password');</script>";
            echo "<script>location.href = 'index.html';</script>";
            exit;
        }
    } else {
        echo "<script>alert('User not found');</script>";
        header("Location: index.html");
        exit;
    }

}
?>