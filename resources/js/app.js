import './bootstrap';
import 'bootstrap';
import barba from '@barba/core';

document.addEventListener('DOMContentLoaded', () => {

    const loader = document.getElementById('loader-wrapper');

    // Hamburger Menu Logic - MOVED TO INLINE SCRIPT IN HEADER.BLADE.PHP TO ENSURE RELIABILITY
    // The previous logic here was creating potential conflicts or failing to load.

    const showLoader = () => {
        if (loader) {
            loader.style.display = 'flex';
            // Force reflow to ensure transition plays
            loader.getBoundingClientRect();
            loader.classList.remove('loaded');
        }
    };

    const hideLoader = () => {
        if (loader) {
            loader.classList.add('loaded');
            setTimeout(() => {
                loader.style.display = 'none';
            }, 500);
        }
    };

    barba.init({
        transitions: [{
            name: 'default-transition',
            leave(data) {
                showLoader();
                return new Promise(resolve => setTimeout(resolve, 500));
            },
            enter(data) {
                // Determine if we need to re-run scripts
                // Ideally, we should re-initialize any plugins here
                hideLoader();

                data.next.container.querySelectorAll('script').forEach(script => {
                    const newScript = document.createElement('script');
                    Array.from(script.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                    newScript.textContent = script.textContent;
                    script.parentNode.replaceChild(newScript, script);
                });
            },
            once(data) {
                hideLoader();
            }
        }]
    });
});
