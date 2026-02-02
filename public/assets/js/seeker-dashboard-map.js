// assets/js/seeker-dashboard-map.js

document.addEventListener('DOMContentLoaded', function () {
    const UNISAN_LAT = 13.84;
    const UNISAN_LNG = 121.98;
    const ZOOM_LEVEL = 13;

    // Initialize Map
    if (!document.getElementById('seeker-map')) return;

    const map = L.map('seeker-map').setView([UNISAN_LAT, UNISAN_LNG], ZOOM_LEVEL);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    // Fetch Providers and Add Markers
    fetch('api/get_markers.php')
        .then(response => response.json())
        .then(data => {
            data.forEach(provider => {
                const lat = parseFloat(provider.latitude);
                const lng = parseFloat(provider.longitude);

                L.marker([lat, lng])
                    .addTo(map)
                    .bindPopup(`
                        <div style="text-align:center;">
                            <strong>${provider.full_name}</strong><br>
                            <span style="font-size:0.8rem; color:#666;">${provider.skills}</span>
                        </div>
                    `);
            });
        })
        .catch(err => console.error('Error loading map markers:', err));
});
