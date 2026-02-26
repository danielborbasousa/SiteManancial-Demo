document.addEventListener("DOMContentLoaded", function() {
    // Enable mouse-wheel scrolling for the horizontal carousels
    const carousels = document.querySelectorAll('.stream-carousel');
    
    carousels.forEach(carousel => {
        carousel.addEventListener('wheel', (evt) => {
            evt.preventDefault();
            carousel.scrollLeft += evt.deltaY;
        });
    });

    // fetch icons from a simple API (here a local JSON file)
    // the response should include replacements for search, notification, book, profile, etc.
    fetch('icons.json')
        .then(res => {
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            return res.json();
        })
        .then(data => {
            if (data.search && document.getElementById('searchIcon')) {
                document.getElementById('searchIcon').textContent = data.search;
            }
            if (data.notification && document.getElementById('notifIcon')) {
                document.getElementById('notifIcon').textContent = data.notification;
            }
            if (data.book && document.getElementById('brandIcon')) {
                document.getElementById('brandIcon').textContent = data.book;
            }
            if (data.profile && document.getElementById('profileIcon')) {
                // profileIcon is a div; insert emoji or svg inside
                const prof = document.getElementById('profileIcon');
                prof.textContent = data.profile;
                prof.style.backgroundColor = 'transparent';
                prof.style.fontSize = '1.25rem';
                prof.style.display = 'flex';
                prof.style.justifyContent = 'center';
                prof.style.alignItems = 'center';
            }
        })
        .catch(err => {
            console.error('Icon API error:', err);
        });
});