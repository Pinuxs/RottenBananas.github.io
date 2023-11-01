document.addEventListener('DOMContentLoaded', () => {
    const apiKey = 'f8b57db9fe486acc54732ebe68b76c7b'; // Replace with your TMDb API key
    const movieApiUrl = `https://api.themoviedb.org/3/discover/movie?api_key=${apiKey}&language=en-US&page=1`;
    const genreApiUrl = 'https://api.themoviedb.org/3/genre/movie/list?api_key=f8b57db9fe486acc54732ebe68b76c7b'
    let currentPage = 1; // Initialize the current page
    const genreList = document.getElementById('genre-list');
    const genreCheckboxContainer = document.getElementById('genre-checkboxes');
    let previousPage = null;
    // Read the genre_id from the URL
  const genreIdFromURL = getUrlParam('genre_id');

    // Function to fetch more movie data from TMDb API
    async function fetchMoreMovieData() {
      try {
        const response = await fetch(`${movieApiUrl}&page=${currentPage}`);
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        const data = await response.json();
        return data.results; // Array of movie results for the current page
      } catch (error) {
        console.error('Error fetching movie data:', error);
        return [];
      }
    }
    
    // Function to create a movie card
    function createMovieCard(movie) {
      const card = document.createElement('div');
      card.className = 'movie-card';
      card.innerHTML = `
        <img src="https://image.tmdb.org/t/p/w500/${movie.poster_path}" alt="${movie.title}">
      `;
      card.addEventListener('click', () => {
        // Navigate to the movie description page with movie details
        const movieId = movie.id; 
        window.location.href = `movie_description.php?id=${movieId}`;
    });
      return card;
    }


// Modify the fetchMoviesByGenres function to include the page parameter
async function fetchMoviesByGenres(genreIds, page) {
  const genreQueryString = genreIds.map(id => `with_genres=${id}`).join('&');
  const genreApiUrl = `https://api.themoviedb.org/3/discover/movie?api_key=${apiKey}&language=en-US&page=${page}&${genreQueryString}`;

  try {
    const response = await fetch(genreApiUrl);
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    const data = await response.json();
    return data.results; // Array of movie results for the selected genre(s)
  } catch (error) {
    console.error('Error fetching movie data by genres:', error);
    return [];
  }
}

    
    // Function to display movies in cards
    function distributeMovieCards(movies) {
        
        const column1 = document.getElementById("movie-cards-column1");
        const column2 = document.getElementById("movie-cards-column2");
        const column3 = document.getElementById("movie-cards-column3");
      
        // Clear the existing content of columns
        column1.innerHTML = "";
        column2.innerHTML = "";
        column3.innerHTML = "";
      
        movies.forEach((movie, index) => {
          const card = createMovieCard(movie);
          if (index % 3 === 0) {
            column1.appendChild(card);
          } else if (index % 3 === 1) {
            column2.appendChild(card);
          } else {
            column3.appendChild(card);
          }
        });
      }
      

    // Function to fetch and display movie genres as checkboxes
    async function displayGenreCheckboxes() {
      try {
        const response = await fetch(genreApiUrl);
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        const data = await response.json();
        const genres = data.genres;
    
        genres.forEach((genre) => {
          const checkboxContainer = document.createElement('div');
          const checkbox = document.createElement('input');
          checkbox.type = 'checkbox';
          checkbox.className = 'genre-checkbox';
          checkbox.value = genre.id;
          checkbox.id = `genre-${genre.id}`;
          checkbox.addEventListener('change', () => filterMoviesByGenres());
    
          const label = document.createElement('label');
          label.textContent = genre.name;
          label.htmlFor = `genre-${genre.id}`;
    
          genreCheckboxContainer.appendChild(checkbox);
          genreCheckboxContainer.appendChild(label);
          genreCheckboxContainer.appendChild(checkboxContainer);
    
        });
    
      } catch (error) {
        console.error('Error fetching and displaying genre checkboxes:', error);
      }
    }
    
      
      // Function to filter movies based on selected genres
async function filterMoviesByGenres() {
  const selectedGenreIds = [...document.querySelectorAll('.genre-checkbox:checked')].map(checkbox => parseInt(checkbox.value));

  // Fetch more movies based on the selected genre(s)
  const filteredMovies = await fetchMoviesByGenres(selectedGenreIds);

  distributeMovieCards(filteredMovies);
}



document.getElementById('search-button').addEventListener('click', () => {
  const searchInput = document.getElementById('search').value;
  searchMovies(searchInput);
});
document.getElementById('search').addEventListener('keydown', (event) => {
  if (event.key === 'Enter') {
    const searchInput = document.getElementById('search').value;
    searchMovies(searchInput);
  }
});
async function searchMoviesByQuery(query) {
  try {
    const searchApiUrl = `https://api.themoviedb.org/3/search/movie?query=${query}&api_key=${apiKey}&language=en-US&page=1`;
    const response = await fetch(searchApiUrl);

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

async function searchMovies(query) {
  const movies = await searchMoviesByQuery(query);
  if (movies.length > 0) {
    distributeMovieCards(movies);
  } else {
    // Display a message when no results are found
    const noResultsMessage = document.createElement('p');
    noResultsMessage.textContent = 'No movies found for your search.';
    document.getElementById('movie-cards-column1').innerHTML = '';
    document.getElementById('movie-cards-column2').innerHTML = '';
    document.getElementById('movie-cards-column3').innerHTML = '';
    document.getElementById('movie-cards-column1').appendChild(noResultsMessage);
  }
}



async function loadMoreMovies(selectedGenreIds) {
  previousPage = currentPage - 1;

  // If genreIds is provided, fetch more movies based on the selected genre(s)
  if (selectedGenreIds && selectedGenreIds.length > 0) {
    const moreMoviesData = await fetchMoviesByGenres(selectedGenreIds, currentPage);
    if (moreMoviesData.length > 0) {
      moviesData = moreMoviesData; // Assign the new movies directly to moviesData
      distributeMovieCards(moviesData);
    } else {
      console.log('No more movies available for the selected genre(s).');
      // Disable the "Load More" button when there are no more movies.
      document.getElementById('load-more-button').disabled = true;
    }
  } else {
    // Fetch more unrelated movies (original behavior)
    const moreMoviesData = await fetchMoreMovieData();
    if (moreMoviesData.length > 0) {
      moviesData = moreMoviesData; // Assign the new movies directly to moviesData
      distributeMovieCards(moviesData);
    } else {
      console.log('No more unrelated movies available.');
      // Disable the "Load More" button when there are no more movies.
      document.getElementById('load-more-button').disabled = true;
    }
  }
}


// Function to load the next set of movies
async function loadNextMovies() {
  document.getElementById('load-previous-button').disabled = false;
  currentPage++;
  const selectedGenreIds = [...document.querySelectorAll('.genre-checkbox:checked')].map(checkbox => parseInt(checkbox.value));
  await loadMoreMovies(selectedGenreIds);

}


// Update the "loadPreviousMovies" function as follows:
async function loadPreviousMovies() {
  currentPage--; // Decrement the current page
  const selectedGenreIds = [...document.querySelectorAll('.genre-checkbox:checked')].map(checkbox => parseInt(checkbox.value));
  if(currentPage > 0) {
    await loadMoreMovies(selectedGenreIds);
  } else {
    document.getElementById('load-previous-button').disabled = true;
  }

}




document.getElementById('load-previous-button').addEventListener('click', async () => {
  loadPreviousMovies();
});

document.getElementById('load-next-button').addEventListener('click', async () => {
  loadNextMovies();
});



// Update your "init" function as follows to disable the "Load Previous" button initially:
async function init() {
  moviesData = []; // Start with an empty array
  distributeMovieCards(moviesData);
  displayGenreCheckboxes();
  await loadMoreMovies();
  previousPage = 0; // Initialize previousPage to 0
  document.getElementById('load-previous-button').disabled = true;
}


function getUrlParam(paramName) {
  const urlParams = new URLSearchParams(window.location.search);
  const values = urlParams.getAll(paramName);
  return values.map(value => parseInt(value));
}
init();




if (genreIdFromURL) {
  // Wait for the checkboxes to be created (you might need to add a delay if necessary)
  setTimeout(function () {
    const genreId = parseInt(genreIdFromURL);
    const genreCheckbox = document.getElementById(`genre-${genreId}`);
    if (genreCheckbox) {
      genreCheckbox.checked = true;
    }
    filterMoviesByGenres();
  }, 200); // Adjust the delay as needed
  
}

    });
