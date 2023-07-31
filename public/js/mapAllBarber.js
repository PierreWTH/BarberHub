
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

var highlightedIcon = L.icon({
    iconUrl: 'https://cdn.icon-icons.com/icons2/1239/PNG/512/flash_84029.png',
    iconSize:     [50, 50], // size of the icon
    iconAnchor:   [25, 50], // point of the icon which will correspond to marker's location
    popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
});


// Regroupement des points en clusters
var markers = new L.MarkerClusterGroup({
    // Distance a laquelle se font les clusters (+ c'est petit - il y a de clusters )
    maxClusterRadius : 50
});

// Boucle sur les markers
for (var i = 0; i < coordinates.length; i++)
{   
    const name = coordinates[i]['name']
    // On décode l'adresse
    const adresse = decodeURI(coordinates[i]['adresse'].replace(/\+/g, ' '))
    const ville = coordinates[i]['ville']

    const popup =  name + '<br>' + adresse + ' - '+ ville
    var marker = L.marker([coordinates[i]['latitude'], coordinates[i]['longitude']], 
    {icon: barberIcon})
    .bindPopup(popup)
    markers.addLayer(marker);
    
}

map.addLayer(markers);

document.addEventListener('DOMContentLoaded', function(){
    // On selectionne chaque carte de barbier
    const barberCards = document.querySelectorAll('.map-barbershop-card')
    // Pour chaque on écoute le clic
    barberCards.forEach(function(barberCard){
        barberCard.addEventListener('click', function(){
            // On récupère latitude et longitude
            const latitude = parseFloat(barberCard.getAttribute('data-latitude'));
            const longitude = parseFloat(barberCard.getAttribute('data-longitude'));
        // On parcours tous les markers
        markers.eachLayer(function(marker){
            
            // Si la lat et long du repere correspondent a lat et long du barbier
            if (marker.getLatLng().lat === latitude && marker.getLatLng().lng === longitude) {
                // On change l'icone et on centre la vue dessus
                marker.setIcon(highlightedIcon)
                map.setView(marker.getLatLng(), 17)
                marker.openPopup()
            } else {
                marker.setIcon(barberIcon); 
            }
        })
        })
    })

    
})

