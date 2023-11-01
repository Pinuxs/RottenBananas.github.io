const genreApiUrl = 'https://api.themoviedb.org/3/genre/movie/list?api_key=f8b57db9fe486acc54732ebe68b76c7b';
const genreList = document.getElementById('genre-list');
const genreCheckboxContainer = document.getElementById('genre-checkboxes');
const ageInput = document.getElementById('age');

async function unselectAllGenreCheckboxes() {
    const genreCheckboxes = document.querySelectorAll('.genre-checkbox');
    genreCheckboxes.forEach((checkbox) => {
        checkbox.checked = false;
    });
}

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
            

            const label = document.createElement('label');
            label.textContent = genre.name;
            label.htmlFor = `genre-${genre.id}`;

            genreCheckboxContainer.appendChild(checkbox);
            genreCheckboxContainer.appendChild(label);

            genreCheckboxContainer.appendChild(checkboxContainer);
        });

        // Read user_profile.txt and set default values
        await readUserProfile();
    } catch (error) {
        console.error('Error fetching and displaying genre checkboxes:', error);
    }
}

async function readUserProfile() {
    try {
        // Fetch the user profile data from the server
        const response = await fetch(`../LandingPage/users/${username}/user_profile.txt`);

        if (!response.ok) {
            throw new Error('Error reading user profile');
            console.log(response);
        }

        const profileData = await response.text();
        const profileLines = profileData.split('\n');

        const ageValue = profileLines[0].replace(/\D/g, '');
           // Set the age input value
           ageInput.value = ageValue;

        // Set the age input value
        if (profileLines[0]) {
            ageInput.value = ageValue;
        }

        // Select checkboxes based on the selected genres
        if (profileLines[1]) {
            const genresData = profileLines[1].trim(); // Remove leading/trailing spaces
            const genresArray = genresData.split(': ')[1]; // Split by ": " and take the second part
            const selectedGenres = genresArray.split(',');
            selectedGenres.forEach((genreId) => {
                const checkbox = document.getElementById(`genre-${genreId}`);
                if (checkbox) {
                    checkbox.checked = true;
                } else {
                    console.warn(`Checkbox with ID 'genre-${genreId}' not found.`);
                }
                console.log(selectedGenres)
            });
        }

    } catch (error) {
        console.error('Error reading user_profile.txt:', error);
    }
}

function collectSelectedGenres() {
    const genreCheckboxes = document.querySelectorAll('.genre-checkbox');
    const selectedGenres = [];

    genreCheckboxes.forEach((checkbox) => {
        if (checkbox.checked) {
            selectedGenres.push(checkbox.value);
        }
    });

    // Create a hidden input field to store the selected genres
    const genreInput = document.createElement('input');
    genreInput.type = 'hidden';
    genreInput.name = 'selectedGenres';
    genreInput.value = selectedGenres.join(',');

    // Append the hidden input to the form
    document.querySelector('.form-detail').appendChild(genreInput);
}

document.querySelector('.form-detail').addEventListener('submit', collectSelectedGenres);

function validateForm() {
  const genreCheckboxes = document.querySelectorAll('.genre-checkbox');
  const selectedGenres = [];
  
  genreCheckboxes.forEach((checkbox) => {
      if (checkbox.checked) {
          selectedGenres.push(checkbox.value);
      }
  });

  const ageValue = ageInput.value.trim();

  if (selectedGenres.length < 3) {
      alert('Please select at least 3 genres.');
      return false;
  }

  if (isNaN(ageValue) || ageValue === '') {
      alert('Please enter a valid age.');
      return false;
  }

  return true;
}

// Update the form submission event listener
document.querySelector('.form-detail').addEventListener('submit', function (event) {
  if (!validateForm()) {
      event.preventDefault(); // Prevent the form from submitting if validation fails
  }
});

// Initialize the app by fetching and displaying initial movie
async function init() {
    displayGenreCheckboxes();

}

init();
