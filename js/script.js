// Módulo de JavaScript para interações do cliente
document.addEventListener("DOMContentLoaded", function() {
    // Habilita rolagem com mouse para os carrosséis horizontais
    const carousels = document.querySelectorAll('.stream-carousel');
    
    carousels.forEach(carousel => {
        carousel.addEventListener('wheel', (evt) => {
            evt.preventDefault();
            carousel.scrollLeft += evt.deltaY;
        });
    });

    // Busca ícones de um arquivo JSON local
    fetch('icons.json')
        .then(res => {
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            return res.json();
        })
        .then(data => {
            // Substitui os ícones se encontrados nos elementos
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
                // profileIcon é um div; insere emoji ou svg dentro
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
            console.error('Erro na API de ícones:', err);
        });
});