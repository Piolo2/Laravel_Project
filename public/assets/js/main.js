// Main JS file
console.log('Unisan Skill Mapping System Loaded');

// Hamburger Menu Logic
document.addEventListener('DOMContentLoaded', function () {
    const hamburger = document.querySelector('.hamburger-menu');
    const navLinks = document.querySelector('.nav-links');

    if (hamburger && navLinks) {
        const icon = hamburger.querySelector('i');

        hamburger.addEventListener('click', function () {
            navLinks.classList.toggle('active');
        });
    }
});
