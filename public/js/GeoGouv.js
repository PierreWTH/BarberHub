// AUTOCOMPLETE L'ADRESSE (en utilisant jQuery et Select2)

// Une fois que la page est totalement chargée
$(document).ready(function() {
    // Selection du champ adresse et initialisation de Select2
    $('#barbershop_adresse').select2({
        // recherche se déclenche après minimum 4 caractères rentrés (sinon erreur dans la console)
        minimumInputLength: 4,
        minimumResultsForSearch: Infinity,
        // Appel a l'api
        ajax: {
            url: 'https://api-adresse.data.gouv.fr/search/',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: decodeURI(params.term)
                };
            },
            // Transforme les résultats de la requête AJAX en format utilisable par Select2
            processResults: function(data) {
                // Récupération des données
                var results = data.features.map(function(feature) {
                    return {
                        id: feature.properties.id,
                        text: feature.properties.label,
                        city: feature.properties.city,
                        postcode: feature.properties.postcode,
                        adresse: feature.properties.name
                    };
                });

                return {
                    results: results
                };
            }
        }
    })
    // A la sélection d'une adresse
    .on('select2:select', function(e) {
        var selectedAdresse = e.params.data;
        
        // Remplissage des champs en fonction de l'adresse choisie
        $('#barbershop_cp').val(selectedAdresse.postcode);
        $('#barbershop_ville').val(selectedAdresse.city);
        $('#barbershop_adresse').val(selectedAdresse.adresse);
        
    });
});
