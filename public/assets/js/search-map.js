// assets/js/search-map.js

(function () {
    function initMap() {
        const UNISAN_LAT = 13.84;
        const UNISAN_LNG = 121.98;
        const ZOOM_LEVEL = 13;

        if (!document.getElementById('map')) return; // Exit if map container missing

        // Initialize Map
        // Check if map is already initialized on this container? 
        // Leaflet errors if we init twice.
        // We should check if container has leaflet class or clear it?
        // Usually safe to re-init if container is fresh from server (replaced by Barba).
        // If Barba cached content, it might have it attached. But we assume fresh fetch or fresh DOM node.

        const ZOOM_THRESHOLD = 15;
        const map = L.map('map').setView([UNISAN_LAT, UNISAN_LNG], ZOOM_LEVEL);

        // Map Zoom Listener for Pin Toggling
        function updateMapClasses() {
            const zoom = map.getZoom();
            const mapContainer = document.getElementById('map');
            if (zoom >= ZOOM_THRESHOLD) {
                mapContainer.classList.add('zoom-high');
            } else {
                mapContainer.classList.remove('zoom-high');
            }
        }

        map.on('zoomend', updateMapClasses);
        updateMapClasses(); // Initial check
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        let markersLayer = L.layerGroup().addTo(map);
        let allProviders = [];
        let userMarker;

        // Elements
        const keywordInput = document.getElementById('keyword');
        const categoryInput = document.getElementById('category');
        const distanceInput = document.getElementById('distance');
        const filterBtn = document.getElementById('filterBtn');
        const resultsGrid = document.getElementById('resultsGrid'); // Replaces resultsList
        const resultCountSpan = document.getElementById('resultCount');

        // User Location Context
        const myLatVal = document.getElementById('myLat') ? document.getElementById('myLat').value : '';
        const myLngVal = document.getElementById('myLng') ? document.getElementById('myLng').value : '';
        let centerLat = UNISAN_LAT;
        let centerLng = UNISAN_LNG;

        // Set Center to User's location if available
        if (myLatVal && myLngVal) {
            centerLat = parseFloat(myLatVal);
            centerLng = parseFloat(myLngVal);

            userMarker = L.marker([centerLat, centerLng], {
                icon: L.divIcon({
                    className: 'my-location-icon',
                    html: '<div style="background:blue; width:12px; height:12px; border-radius:50%; border:2px solid white;"></div>'
                })
            }).addTo(map).bindPopup("My Location");

            map.setView([centerLat, centerLng], ZOOM_LEVEL);
        }

        // Haversine
        function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
            var R = 6371;
            var dLat = deg2rad(lat2 - lat1);
            var dLon = deg2rad(lon2 - lon1);
            var a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2)
                ;
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            var d = R * c;
            return d;
        }

        function deg2rad(deg) { return deg * (Math.PI / 180) }

        // Fetch Providers
        fetch('/api/markers') // Use Laravel Route
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        // Throw error with the raw text sample
                        throw new Error('Failed to parse JSON: ' + text.substring(0, 100)); // Show first 100 chars
                    }
                });
            })
            .then(data => {
                allProviders = data;
                renderMarkers(); // Initial Render
            })
            .catch(err => {
                console.error('Error fetching markers:', err);
                alert('Debug Error: ' + err.message);
            });

        // Render Logic
        function renderMarkers() {
            markersLayer.clearLayers();
            if (resultsGrid) resultsGrid.innerHTML = '';
            let count = 0;

            const keyword = keywordInput ? keywordInput.value.toLowerCase() : '';
            const category = categoryInput ? categoryInput.value.toLowerCase() : '';
            const maxDist = parseFloat(distanceInput ? distanceInput.value : 50) || 50;

            allProviders.forEach(provider => {
                const pLat = parseFloat(provider.latitude);
                const pLng = parseFloat(provider.longitude);
                const dist = getDistanceFromLatLonInKm(centerLat, centerLng, pLat, pLng);

                // Filter Conditions
                let matchesKeyword = provider.skills.toLowerCase().includes(keyword) ||
                    provider.full_name.toLowerCase().includes(keyword);
                let matchesCategory = category === '' || provider.categories.toLowerCase().includes(category);
                let matchesDistance = dist <= maxDist;

                if (matchesKeyword && matchesCategory && matchesDistance) {
                    count++;

                    // Add Map Marker
                    // Add Map Marker
                    const profileImg = provider.profile_picture
                        ? provider.profile_picture
                        : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(provider.full_name) + '&background=random';

                    const role = provider.categories.split(',')[0] || 'Provider';

                    const customIcon = L.divIcon({
                        className: 'custom-map-marker',
                        html: `
                            <div class="pin-simple"></div>
                            <div class="pin-detailed">
                                <img src="${profileImg}" alt="${provider.full_name}">
                                <div class="pin-info">
                                    <span class="pin-name">${provider.full_name}</span>
                                    <span class="pin-role">${role}</span>
                                    <span class="pin-action">See Details</span>
                                </div>
                            </div>
                        `,
                        iconSize: [0, 0], // CSS handles sizing
                        iconAnchor: [0, 0] // Centered via CSS transform
                    });

                    const marker = L.marker([pLat, pLng], { icon: customIcon });
                    marker.on('click', () => {
                        window.location.href = `/view-profile/${provider.user_id}`;
                    });
                    markersLayer.addLayer(marker);

                    // Add Card to Grid
                    if (resultsGrid) {
                        const card = document.createElement('div');
                        card.className = 'service-card';
                        card.id = `card-${provider.user_id}`; // For scroll linking
                        card.innerHTML = `
                            <div class="provider-header">
                                ${provider.profile_picture
                                ? `<img src="${provider.profile_picture}" class="provider-img" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">`
                                : `<div class="provider-img" style="background-color:#ccc; width: 50px; height: 50px; border-radius: 50%;"></div>`
                            }
                                <div class="provider-info">
                                    <h4>${provider.full_name} <i class="fas fa-check-circle verified-badge"></i></h4>
                                    <p class="service-role">${provider.categories.split(',')[0]} Provider</p>
                                </div>
                            </div>
                            
                            <div class="star-rating">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            </div>

                            <div class="service-desc">
                                <strong>Skills:</strong> ${provider.skills}
                                <br>
                                <small style="color:blue;">${dist.toFixed(2)} km from center</small>
                            </div>

                            <a href="/view-profile/${provider.user_id}" class="btn-view-profile" style="text-decoration:none; display:inline-block; text-align:center;">View Profile</a>
                        `;
                        resultsGrid.appendChild(card);
                    }
                }
            });

            if (resultCountSpan) resultCountSpan.textContent = count;
            if (count === 0 && resultsGrid) {
                resultsGrid.innerHTML = '<p style="grid-column: 1/-1; text-align:center;">No services found matching your criteria.</p>';
            }
        }

        if (filterBtn) filterBtn.addEventListener('click', renderMarkers);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMap);
    } else {
        initMap();
    }
})();
