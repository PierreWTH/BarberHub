// AUTOCOMPLETE L'ADRESSE (en utilisant jQuery et Select2)

// Une fois que la page est totalement chargée
$(document).ready(function() {
    // Selection du champ adresse et initialisation de Select2
    $('#barbershop_adresse').select2({
        // Traduction en français
        language: {
            searching: function() {
                return "Recherche..";
            },
            noResults: function () {
                return "Aucun résultat."
            },
            inputTooShort: function (e) {
                var t = e.minimum - e.input.length, n = "Entrez " + t + " caractères ou plus.";
                return n
            },
            errorLoading: function () {
                return "Erreur : les résultats ne peuvent pas être affichés."
            }
        },
        // Recherche se déclenche après minimum 4 caractères rentrés (L'API a besoin de 3 caractères minimum)
        minimumInputLength: 4,
        minimumResultsForSearch: Infinity,
        // Appel a l'api
        ajax: {
            url: 'https://api-adresse.data.gouv.fr/search/',
            dataType: 'json',
            // Delai de 300ms entre chaque appel pour ne pas surcharger l'API
            delay: 300,
            data: function(params) {
                return {
                    q: params.term
                };
            },
            // Transforme les résultats de la requête AJAX en format utilisable par Select2
            processResults: function(data) {
                // Récupération des données
                return {
                    results: data.features.map(function(feature) {
                        return {
                            id: feature.properties.id,
                            text: feature.properties.label,
                            city: feature.properties.city,
                            postcode: feature.properties.postcode,
                            adresse: feature.properties.name
                        };
                    })
                }
            }
        }
    })

    // A la sélection d'une adresse
    $('#barbershop_adresse').on('select2:select', function(e) {
        const selectedAdresse = e.params.data;
        
        // Remplissage des champs cp et ville en fonction de l'adresse choisie puis desactivation des champs
        $('#barbershop_cp').val(selectedAdresse.postcode).prop('disabled', true);
        $('#barbershop_ville').val(selectedAdresse.city).prop('disabled', true);

        // Afficher l'élément selectionnée dans le champs adresse
        // Création d'un ouvel élément d'option : (texte affiché, valeur de l'option, defautSelected, Selected)
        var displayedAdress = new Option(selectedAdresse.adresse, selectedAdresse.adresse, true, true)
        // On vide le select et on ajoute la displayedAdress
        $('#barbershop_adresse').empty().append(displayedAdress).trigger('change');
        
        
    });
});
