
// Définition du point sur lequel la vue de la map vas se mettre
var map = L.map('map-all-barber').setView([48.582, 7.7503], 13);

// Ajout de la couche de carte 
L.tileLayer('https://maps.geoapify.com/v1/tile/positron/{z}/{x}/{y}.png?apiKey=061b3e7522c44df3be4a6d16bd067b71', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

// Icone de base
var barberIcon = L.icon({
    iconUrl: 'https://cdn.icon-icons.com/icons2/1465/PNG/512/598barberpole_100227.png',
    iconSize:     [50, 50], // size of the icon
    iconAnchor:   [25, 50], // point of the icon which will correspond to marker's location
    popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
});
// Icone selectionnée
var highlightedIcon = L.icon({
    iconUrl: 'https://www.zupimages.net/up/23/31/ix43.png',
    iconSize:     [70, 70], // size of the icon
    iconAnchor:   [35, 50], // point of the icon which will correspond to marker's location
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
    {icon: barberIcon,
    barberId: coordinates[i]['id']
    
    })
    .bindPopup(popup)
    markers.addLayer(marker);
    
}

map.addLayer(markers);

const boxShadow = 'rgba(240, 213, 91, 1) -5px 5px, rgba(79, 78, 77, 0.3) -10px 10px, rgba(135, 134, 133, 0.2) -15px 15px, rgba(179, 178, 176, 0.1) -20px 20px, rgba(227, 226, 222, 0.05) -25px 25px'

// Reinitialise les markers et le style des divs
function resetAll() {
    markers.eachLayer(function(marker){
        marker.setIcon(barberIcon); 
        const barberAllCards = document.querySelectorAll('.map-barbershop-card');
        barberAllCards.forEach(function(barberAllCard) {
            barberAllCard.style.boxShadow = '';
        });
    });
}
// Si on clique sur la map : tout se reset
map.on('click', function() {
    resetAll();
});

document.addEventListener('DOMContentLoaded', function(){

    /******* CLIC SUR UNE BARBER CARD ******/
    // On selectionne chaque carte de barbier
    const barberCards = document.querySelectorAll('.map-barbershop-card')
    // Pour chaque on écoute le clic
    barberCards.forEach(function(barberCard){
        barberCard.addEventListener('click', function(event){
            // Si on clique sur le lien Découvrir, on arrete la fonction
            if (event.target.tagName === 'A') {
                return
            }
            // On remet tout à 0 à chaque clic
            resetAll();
            // On ajoute l'ombre
            barberCard.style.boxShadow = boxShadow

            // On récupère latitude et longitude du barber sur lequel on a cliqué
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
                } 
                else {
                    marker.setIcon(barberIcon); 
                }
            })
        })
    })

    /******* CLIC SUR UN MARKER ******/
    // Pour chaque marker, au clic
    markers.eachLayer(function(marker){
        marker.on('click', function(event) {
            // On remet les icones de bases sur les markers
            resetAll();
            // Si le niveau de zoom est inférieur à 15, on zoom sur le point
            if(map.getZoom() < 15){
                map.setView(marker.getLatLng(), 15)
            }
            // On change l'icone
            marker.setIcon(highlightedIcon)

            // On récupère la div a afficher
            const divToHighlight = document.getElementById('barber-card-' + marker.options.barberId);

            // Si elle existe, on scroll jusqu'a elle et on lui ajoute une ombre
            if (divToHighlight) {
                divToHighlight.scrollIntoView({ behavior: 'smooth', block: 'center' });
                divToHighlight.style.boxShadow = boxShadow
            }

        })
    })
    
});

