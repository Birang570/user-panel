document.querySelector('.dropdown-btn').addEventListener('click', () => {
    const dropdownMenu = document.querySelector('.dropdown-menu');
    dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
});

window.addEventListener('click', (e) => {
    if (!e.target.matches('.dropdown-btn')) {
        document.querySelector('.dropdown-menu').style.display = 'none';
    }
});

