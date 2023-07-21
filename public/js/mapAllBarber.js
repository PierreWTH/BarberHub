
// Définition du point sur lequel la vue de la map vas se mettre
var map = L.map('map-all-barber').setView([48.582, 7.7503], 13);

// Ajout de la couche de carte 
L.tileLayer('https://maps.geoapify.com/v1/tile/positron/{z}/{x}/{y}.png?apiKey=061b3e7522c44df3be4a6d16bd067b71', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

// Style de l'icon
var barberIcon = L.icon({
    iconUrl: 'https://cdn.icon-icons.com/icons2/1465/PNG/512/598barberpole_100227.png',
    iconSize:     [50, 50], // size of the icon
    iconAnchor:   [25, 50], // point of the icon which will correspond to marker's location
    popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
});

// Regroupement des points en clusters
var markers = new L.MarkerClusterGroup();

// Boucle sur les markers
for (var i = 0; i < coordinates.length; i++)
{
    markers.addLayer(L.marker([coordinates[i]['latitude'], coordinates[i]['longitude']], 
    {icon: barberIcon})
    .bindPopup('test')
    );
    
}

map.addLayer(markers);

