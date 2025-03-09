// Mobile menu toggle
document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.querySelector('.menu-toggle');
    const primaryMenu = document.querySelector('#primary-menu');
    
    menuToggle?.addEventListener('click', () => {
        primaryMenu.classList.toggle('active');
    });
});