$(document).ready(function() {
    
    $('#barbershop_adresse').selectize({
        valueField: 'value',
        labelField: 'label',
        searchField: 'label',
        // Pas possible de creer de nouveaux champs
        create: false,
        // Pas de surbrillance pour les correspondances dans la liste
        highlight: false,
        // 1 choix max
        maxItems: 1,
        // Fermeture select apres choix
        closeAfterSelect: true,
        // Temps entre chaque requete
        loadThrottle: 300, 
        // Texte d'attente requete
        loadingClass: "Recherche...",
        placeholder: "Choississez une adresse...",
        // Personnalisation du rendu
        render: {
            option: function(item, escape){
                console.log(item)
                return '<div>' + escape(item.label) + '</div>'
            }
        },
    
        // 
        load: function(query, callback) {
            // Si l'utilisateur n'a rien écrit ou si moins de 4 caractères
            if (!query.length) return callback();
            if (query.length < 4) return callback();

            //Requete AJAX vers GEOGOUV
            $.ajax({
                url:'https://api-adresse.data.gouv.fr/search/',
                type: 'GET',
                dataType: 'json',
                data: 
                {
                    q: query,
                    limit: 10
                },
                // En cas d'erreur : rien a afficher
                error: function() 
                {
                    callback();
                },
                // En cas de succes : on affiche l'adresse et on met l'adresse en valeur
                success: function(res) 
                {
                    callback(res.features.map(function(feature)
                    {   
                        console.log(res.features)
                            return {
                                value: feature.properties.name,
                                label: feature.properties.name
                            };
                    })); 
                }
            });
        }
    });
});