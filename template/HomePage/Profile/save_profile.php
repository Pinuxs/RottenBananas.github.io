<?php
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST["age"]) && isset($_POST["selectedGenres"])) {
            $age = $_POST["age"];
            $selectedGenres = $_POST["selectedGenres"];
            $username = $_SESSION['username'];
            // Define the path to the text file where you want to save the data
           
            $file = "../landingpage/users/$username/user_profile.txt";
            

            // Open the file in write mode (creates the file if it doesn't exist)
            $fileHandle = fopen($file, "w");

            // Write the age and selected genres to the file
            fwrite($fileHandle, "Age: " . $age . PHP_EOL);
            fwrite($fileHandle, "Selected Genres: " . $selectedGenres);

            // Close the file
            fclose($fileHandle);
            $_SESSION["ProfileCompleted"] = true;
            echo "Profile data has been saved successfully!";
            header("location: ../Home/homepage.php");
        } else {
            echo "Age or selectedGenres not provided.";
        }
    } else {
        echo "Invalid request method.";
    }
} else {
    echo "User is not logged in.";
}
?>
