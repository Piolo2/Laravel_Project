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

            // Toggle icon between bars and times (close)
            if (navLinks.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    }
});
