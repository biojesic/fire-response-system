// public/js/dashboard.js
// document.addEventListener('DOMContentLoaded', function() {
//     initMap();
// });

// let selectedTeams = [];
// let map;

// var firestationIcon = L.icon({
//     iconUrl: '/images/firestation-icon.png',
//     iconSize: [45, 45],
//     iconAnchor: [25, 50],
//     popupAnchor: [0, -50]
// });

// var fireincidentIcon = L.icon({
//     iconUrl: '/images/fire-icon.png',
//     iconSize: [50, 50],
//     iconAnchor: [25, 50],
//     popupAnchor: [0, -50]
// });

// var fireresponderIcon = L.icon({
//     iconUrl: '/images/fireresponder-icon.webp',
//     iconSize: [55, 55],
//     iconAnchor: [25, 50],
//     popupAnchor: [0, -50]
// });

// function toggleTeamSelection(incidentId, teamId) {
//     let selectedTeams = document.getElementById(`selected_teams_${incidentId}`).value.split(',');

//     const teamButton = document.querySelector(`button[data-incident-id="${incidentId}"][data-team-id="${teamId}"]`);

//     if (selectedTeams.includes(teamId.toString())) {
//         selectedTeams = selectedTeams.filter(id => id !== teamId.toString());
//         teamButton.classList.remove('bg-blue-700');
//     } else {
//         selectedTeams.push(teamId.toString());
//         teamButton.classList.add('bg-blue-700');
//     }

//     document.getElementById(`selected_teams_${incidentId}`).value = selectedTeams.join(',');

//     console.log('Selected Teams for Incident ' + incidentId + ':', selectedTeams);
// }

// function markAsContained(event, form) {
//     event.preventDefault();

//     let formData = new FormData(form);

//     fetch(form.action, {
//             method: 'POST',
//             body: formData
//         })
//         .then(response => response.json())
//         .then(data => {
//             if (data.status === 'success') {
//                 alert(data.message);
//                 location.reload();
//             } else {
//                 alert(data.message);
//             }
//         })
//         .catch(error => {
//             console.error('Error:', error);
//             alert('Something went wrong. Please try again later.');
//         });
// }

// function initMap() {
//     const fireStationLocation = JSON.parse(document.getElementById('fire-station-location').textContent);

//     map = L.map('map').setView(fireStationLocation, 13);

//     L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
//         attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
//     }).addTo(map);

//     L.marker(fireStationLocation, {
//             icon: firestationIcon
//         })
//         .addTo(map)
//         .bindPopup('Fire Station Location');

//     const incidents = JSON.parse(document.getElementById('fire-reports-data').textContent);

//     incidents.forEach(incident => {
//         if (incident.latitude && incident.longitude) {
//             const incidentCoords = [incident.latitude, incident.longitude];

//             L.marker(incidentCoords, {
//                     icon: fireincidentIcon
//                 })
//                 .addTo(map)
//                 .bindPopup(
//                     `<strong>${incident.location}</strong><br>Status: ${incident.status}<br>Incident ID: ${incident.id}`
//                 );
//         }
//     });

//     const firefighterLocations = JSON.parse(document.getElementById('firefighter-data').textContent);

//     firefighterLocations.forEach(location => {
//         const firefighterCoords = [location.latitude, location.longitude];

//         L.marker(firefighterCoords, {
//                 icon: fireresponderIcon
//             })
//             .addTo(map)
//             .bindPopup(
//                 `<strong>Firefighter Location</strong><br>Latitude: ${location.latitude}<br>Longitude: ${location.longitude}`
//             );
//     });
// }
