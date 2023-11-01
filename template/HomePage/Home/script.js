const apiKey = 'f8b57db9fe486acc54732ebe68b76c7b';
const apiUrl = `https://api.themoviedb.org/3/discover/movie?api_key=${apiKey}&language=en-US&sort_by=popularity.desc`;


// Function to fetch movie data from TMDb API
async function fetchMovieData() {
    try {
        const response = await fetch(apiUrl);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const data = await response.json();
        return data.results; // Array of movie results
    } catch (error) {
        console.error('Error fetching movie data:', error);
        return [];
    }
}

// Function to create a movie card
function createMovieCard(movie) {
    const card = document.createElement("div");
    card.className = "movie-card";
    card.innerHTML = `
        <img src="https://image.tmdb.org/t/p/w500/${movie.poster_path}" alt="${movie.title}" width="400" height="500">
    `;
    card.addEventListener('click', () => {
        // Navigate to the movie description page with movie details
        const movieId = movie.id; 
        window.location.href = `../Movies/movie_description.php?id=${movieId}`;
    });
    return card;
}

// Populate the "Movie List" section with movie cards
const movieListSection = document.getElementById("movie-cards");

fetchMovieData()
    .then(movies => {
        movies.forEach(movie => {
            const movieCard = createMovieCard(movie);
            movieListSection.appendChild(movieCard);
        });
    })
    .catch(error => {
        console.error('Error populating movie cards:', error);
    });

    // Get the movie card element
const movieCard = document.querySelector('.movie-card');

