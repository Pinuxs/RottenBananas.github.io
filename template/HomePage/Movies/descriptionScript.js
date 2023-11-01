

// Function to fetch movie details by ID from TMDb API
async function fetchMovieDetailsById(movieId, apiKey) {
    try {
      const apiUrl = `https://api.themoviedb.org/3/movie/${movieId}?api_key=${apiKey}`;
      const response = await fetch(apiUrl);
  
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
  
      const movieData = await response.json();
      return movieData;
    } catch (error) {
      console.error('Error fetching movie details:', error);
      return null;
    }
  }

  // Function to fetch movie trailer by movie ID from TMDb API
  async function fetchMovieTrailerById(movieId, apiKey) {
    try {
      const apiUrl = `https://api.themoviedb.org/3/movie/${movieId}/videos?api_key=${apiKey}`;
      const response = await fetch(apiUrl);
  
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
  
      const trailerData = await response.json();
      // Assuming the first result is the official trailer (you can implement more logic to select a specific trailer)
      const trailerKey = trailerData.results[0]?.key;
      return trailerKey ? `https://www.youtube.com/watch?v=${trailerKey}` : null;
    } catch (error) {
      console.error('Error fetching movie trailer:', error);
      return null;
    }
  }

  
  // Function to display movie details and trailer on a card
  function displayMovieDetailsAndTrailer(movieDetails, trailerUrl) {
    const movieCard = document.createElement('div');
    movieCard.className = 'movie-detail-cards';
    movieCard.innerHTML = `
      <h2>${movieDetails.title}</h2>
      <img src="https://image.tmdb.org/t/p/w500/${movieDetails.poster_path}" alt="${movieDetails.title}">
      <p>${movieDetails.overview}</p>
      <button id="trailerButton">Watch Trailer</button>

    `;
  
    const movieContainer = document.getElementById('ClickedMovie-container'); 
    movieContainer.innerHTML = ''; // Clear the container
    movieContainer.appendChild(movieCard);

  // Add an event listener to the "Watch Trailer" button
  const trailerButton = movieCard.querySelector('#trailerButton');
  trailerButton.addEventListener('click', function () {
    // Check if the trailerUrl is defined and open it in a new tab
    if (trailerUrl) {
      window.open(trailerUrl, '_blank');
    } else {
      console.log('Trailer URL is not available.');
    }
  });
  }
  


  // Get the URLSearchParams object from the current URL
const queryParams = new URLSearchParams(window.location.search);

// Get the 'id' parameter from the URL
const movieId = queryParams.get('id');
  // Usage example:
  const apiKey = 'f8b57db9fe486acc54732ebe68b76c7b'; // Replace with your TMDb API key
// Replace with the ID of the movie you want to display
  

  // Fetch movie details and trailer, then display them
  fetchMovieDetailsById(movieId, apiKey)
    .then((movieDetails) => {
      if (movieDetails) {
        return fetchMovieTrailerById(movieId, apiKey).then((trailerUrl) => {
          displayMovieDetailsAndTrailer(movieDetails, trailerUrl);
        });
      } else {
        console.log('Movie not found or there was an error fetching details.');
      }
    })
    .catch((error) => {
      console.error('Error:', error);
    });


    const apiUrl = `https://api.themoviedb.org/3/movie/${movieId}/similar?api_key=${apiKey} `;
    
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
        card.className = "similar-movie-card";
        card.innerHTML = `
            <img src="https://image.tmdb.org/t/p/w500/${movie.poster_path}" alt="${movie.title}" width="400" height="500">
        `;
        card.addEventListener('click', () => {
            // Navigate to the movie description page with movie details
            const movieId = movie.id; 
            window.location.href = `movie_description.php?id=${movieId}`;
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
   








