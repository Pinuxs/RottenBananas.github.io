<?php 
session_start();
// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: landingpage/index.html");
    exit;
}
if (!isset( $_SESSION["ProfileCompleted"]) ||  $_SESSION["ProfileCompleted"] !== true) {
    header("Location: ../Profile/newprofile.php");
    exit;
}

// Get the genre_id and genre_name from the URL
$genreIdFromURL = isset($_GET['genre_id']) ? $_GET['genre_id'] : null;
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="moviestyles.css">
    <meta charset="UTF-8" />
    <title>Rotten Banana - Movies</title>
</head>
<body>
<header> 
    <nav>
        <ul>
            <li><a href="../Home/homepage.php"><img src="../landingpage/images/cartoon-rotten-banana-KXXWCG.jpg" alt="Home" style="width: 50px; height: 40px; position: absolute; top: 0; left: 0;"></a></li>
            <li><a href="../Home/homepage.php" id="Homelink">Home</a></li>
            <li><a href="Movies.php" id="MovieLink">Movies</a></li>
            <li><a href="../Favourites/FavouritesPage.php">Favourites</a></li>
            <li><a href="../Home/explore.php">Explore</a></li>
            <li style="float:right"><a href="../Home/logout.php">logout</a></li>
            <li style="float:right"><a href="../Profile/profile.php" id="Profile"><?php echo $_SESSION["username"];?></a></li>
        </ul>  
    </nav>
</header>

<div class="grid-container">
    <div class="menu">
        <h1>Movie List</h1>
        <input type="text" id="search" placeholder="Search for movies...">
        <button id="search-button">Search</button>
        <div id="genre-checkboxes">
            <h2>Genre Filters</h2>
            <!-- Genre checkboxes will be inserted here -->
        </div>
        <button id="Clear-button">Clear</button>
    </div>

    <div class="movie-cards column1" id="movie-cards-column1">
        <!-- Movie cards will be inserted here -->
    </div>
    <div class="movie-cards column2" id="movie-cards-column2">
        <!-- Movie cards will be inserted here -->

    </div>
    
    <div class="movie-cards column3" id="movie-cards-column3">
        <!-- Movie cards will be inserted here -->
    </div>


</div>
<div class="movie-cards column2" id="movie-cards-column2">
  <!-- Movie cards will be inserted here -->
</div>
<div class="CenterButton">
  <div class="button-container">
    <button class="button" id="load-previous-button">Previous Page</button>
    <button class="button" id="load-next-button">Next Page</button>
  </div>
</div>

<script src="MovieScript.js"></script>
</body>
</html>
