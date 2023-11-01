<?php 
session_start();


if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    
} else {
    header("location: ../landingpage/index.html");
}

echo "<script>var username = '{$_SESSION['username']}';</script>";



?>
<!DOCTYPE html>
<html>
<head>
    <title>Profile Page</title>
    <link rel="stylesheet" type="text/css" href="../Home/stylesheet.css"> <!-- You can link your CSS file here -->
</head>
<body>

    <main>
        <section>
            <h2>Your Profile Information</h2>
            <p>Please enter these details to proceed</p>

        </section>

            <form class="form-detail" action="save_profile.php" method="post">
                <div>
                    <label><strong>Enter your age:</strong></label>
                    <input type="number" id="age" name="age" min="1" max="150" step="1">
                    <p> </p>
                </div>
            
                <div>
                    <label><strong>Select your top genres:</strong></label>
                    <div id="genre-checkboxes">
                <!-- Genre checkboxes will be inserted here -->
                
                    </div>
                </div>
                <input type="submit" value="Save Profile">
            </form>
    </main>
    

    <script src="profilescript.js">

    </script>
</body>
</html>