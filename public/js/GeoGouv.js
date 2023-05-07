// AUTOCOMPLETE L'ADRESSE (en utilisant jQuery et Select2)

// Une fois que la page est totalement chargée
$(document).ready(function() 
{
    // Selection du champs adresse et initialisation de Select2
    $('#barbershop_adresse').select2
    ({
        // recherche se déclenche après minimum 2 caractères rentrés
        minimumInputLenght:2,
        // Appel a l'api
        ajax: 
        {
            url: 'https://api-adresse.data.gouv.fr/search/',
            dataType: 'json',
            delay: 250,
            data : function(params)
            {
                return
                {
                    q: params.term
                };
            },
            // Transforme les résultats de la requete AJAX en format utilisable par Select2
            processResults: function(data) {
                // Récuperation des données
                var results = data.features.map(function(feature){
                    return {
                        id: feature.properties.id,
                        text:feature.properties.label,
                        city: feature.properties.city,
                        postcode: feature.properties.postcode,
                        adresse: feature.properties.name 
                    };
                });
                return {
                    results: results
                };
            },
            cache:true
        },
        placeholder : 'Rechercher une adresse',
        allowClear: true
    })
    // A la selection d'une adresse
    .on('select2:select', function(e) {
        var selectedAdresse = e.params.data;
        
        // Remplissage du code postal et de la ville en fonction de l'adresse choisie
        $('#cp-input').val(selectedAdresse.postcode);
        $('#ville-input').val(selectedAdresse.city);

    });
});