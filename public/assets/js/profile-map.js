// assets/js/profile-map.js

document.addEventListener('DOMContentLoaded', function () {
    // Unisan, Quezon Coordinates approx center
    const UNISAN_LAT = 13.84;
    const UNISAN_LNG = 121.98;
    const ZOOM_LEVEL = 13;

    // Get existing coords if any
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');

    let initialLat = latInput.value || UNISAN_LAT;
    let initialLng = lngInput.value || UNISAN_LNG;

    const map = L.map('map').setView([initialLat, initialLng], ZOOM_LEVEL);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let marker;

    // If we have a saved location, show it
    if (latInput.value && lngInput.value) {
        marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);
    }

    // Map Click Event
    map.on('click', function (e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        if (marker) {
            marker.setLatLng(e.latlng);
        } else {
            marker = L.marker(e.latlng, { draggable: true }).addTo(map);
            // Re-attach dragend event if it's a new marker
            marker.on('dragend', function (e) {
                const lat = e.target.getLatLng().lat;
                const lng = e.target.getLatLng().lng;
                latInput.value = lat.toFixed(8);
                lngInput.value = lng.toFixed(8);
                updateBarangayFromCoordinates(lat, lng);
            });
        }

        latInput.value = lat.toFixed(8);
        lngInput.value = lng.toFixed(8);

        updateBarangayFromCoordinates(lat, lng);
    });

    // Update inputs when marker is dragged
    if (marker) {
        marker.on('dragend', function (e) {
            const lat = e.target.getLatLng().lat;
            const lng = e.target.getLatLng().lng;
            latInput.value = lat.toFixed(8);
            lngInput.value = lng.toFixed(8);
            updateBarangayFromCoordinates(lat, lng);
        });
    }

    // Forward Geocoding: Dropdown -> Map
    const barangaySelect = document.getElementById('barangaySelect');
    if (barangaySelect) {
        console.log('Barangay Select element found, attaching listener.');

        // Manual backup coordinates for some known areas in Unisan (approximate)
        // If Nominatim fails, we can fall back to these if defined.
        const barangayCoordinates = {
            'Poblacion': { lat: 13.84, lng: 121.98 },
            'F. de Jesus (Poblacion)': { lat: 13.841, lng: 121.982 },
            'R. Lapu-lapu (Poblacion)': { lat: 13.839, lng: 121.981 },
            'R. Magsaysay (Poblacion)': { lat: 13.838, lng: 121.979 },
            'Raja Soliman (Poblacion)': { lat: 13.842, lng: 121.978 },
            // Add more as discovered or needed
        };

        barangaySelect.addEventListener('change', function () {
            const selectedBarangay = this.value;
            console.log('Barangay selected:', selectedBarangay);
            if (!selectedBarangay) return;

            // Check hardcoded first (instant)
            if (barangayCoordinates[selectedBarangay]) {
                const coords = barangayCoordinates[selectedBarangay];
                updateMapAndInputs(coords.lat, coords.lng);
                console.log('Used hardcoded coordinates for:', selectedBarangay);
                return;
            }

            // Fallback to API
            const queries = [
                `Barangay ${selectedBarangay}, Unisan, Quezon`,
                `${selectedBarangay}, Unisan, Quezon`,
                `${selectedBarangay}, Quezon` // Broader search
            ];

            tryFetchLocations(queries, 0);

            function tryFetchLocations(queries, index) {
                if (index >= queries.length) {
                    alert(`Could not find "${selectedBarangay}" on the map automatically. Please pin your location manually.`);
                    return;
                }

                const query = queries[index];
                console.log('Fetching location for:', query);
                const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`;

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            const result = data[0];
                            const lat = parseFloat(result.lat);
                            const lng = parseFloat(result.lon);
                            updateMapAndInputs(lat, lng);
                        } else {
                            // Try next query
                            tryFetchLocations(queries, index + 1);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching location:', error);
                        // Try next query even on error
                        tryFetchLocations(queries, index + 1);
                    });
            }

            function updateMapAndInputs(lat, lng) {
                const newLatLng = new L.LatLng(lat, lng);
                map.setView(newLatLng, 15);

                if (marker) {
                    marker.setLatLng(newLatLng);
                } else {
                    marker = L.marker(newLatLng, { draggable: true }).addTo(map);
                    marker.on('dragend', function (e) {
                        const lat = e.target.getLatLng().lat;
                        const lng = e.target.getLatLng().lng;
                        latInput.value = lat.toFixed(8);
                        lngInput.value = lng.toFixed(8);
                        updateBarangayFromCoordinates(lat, lng);
                    });
                }

                latInput.value = lat.toFixed(8);
                lngInput.value = lng.toFixed(8);
            }
        });
    } else {
        console.error('Barangay Select element NOT found!');
    }

    function updateBarangayFromCoordinates(lat, lng) {
        // Use Nominatim for reverse geocoding
        const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data && data.address) {
                    console.log('Detected Address:', data.address);

                    // Possible keys for Barangay/Village in OSM
                    const placeName = data.address.village ||
                        data.address.hamlet ||
                        data.address.suburb ||
                        data.address.neighbourhood ||
                        data.address.quarter ||
                        data.address.town || // Sometimes smaller towns
                        '';

                    if (placeName) {
                        const select = document.querySelector('select[name="address"]');
                        if (select) {
                            let found = false;
                            // Normalize for comparison
                            const normalize = s => s.toLowerCase().trim().replace('barangay', '').trim();
                            const target = normalize(placeName);

                            for (let i = 0; i < select.options.length; i++) {
                                const optionText = normalize(select.options[i].value);

                                // Check exact match or inclusion
                                if (optionText === target || (target.length > 3 && optionText.includes(target))) {
                                    // Setting selectedIndex manually does NOT trigger the 'change' event
                                    // So we don't need to worry about infinite loops here
                                    select.selectedIndex = i;
                                    found = true;
                                    console.log('Auto-selected Barangay:', select.options[i].value);
                                    break;
                                }
                            }

                            if (!found) {
                                console.log('No matching Barangay found in dropdown for:', placeName);
                            }
                        }
                    }
                }
            })
            .catch(error => console.error('Error fetching address:', error));
    }
});
