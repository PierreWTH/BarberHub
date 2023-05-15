
// Définition du bouton pour ajouter des horaires
var addOpeningButton = $('.add-horaire-button');

// Définition du bouton pour supprimer des horaires
var removeOpeningButton = $('.remove-horaire-button');

// Tableau de jours de la semaine
var joursSemaine = ["lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi", "dimanche"]

// Selection de la table d'ajout d'horaires
var addHorairesTbody = $('.addHorairesTbody')

//Nombre de champs
var nbChamps = 0

// AJOUTER DES CHAMPS
// Fonction qui se déclenche au clic du bouton
$(addOpeningButton).click(function() {
    
    if (nbChamps < 7)
    {
        // Boucle pour générer les 7 champs
        for (var i = 0; i<=6; i++)
        {
            // Définition d'un nouveau tr
            var newChamps = $('<tr></tr>');
            // Jour actuel :
            const jourActuel = joursSemaine[i]

            // Affichage du jour de la semaine
            newChamps.append('<td>' + jourActuel + '</td>')
            // Champs caché qui récupère le jour actuel
            newChamps.append('<input type="hidden" id="inputJourActuel" name="jourActuel" value="'+ jourActuel +'" />')
            // Ajout d'un nouveau champs pour les heures d'ouvertures
            newChamps.append('<td><input id="heureOuverture" type="time" name="ouverture['+ i +']"></td>');
            // Nouveau champs pour les heures de fermetures
            newChamps.append('<td><input id="heureFermeture" type="time" name="fermeture['+ i +']"></td>');
            // Bouton pour supprimer le champs crée
            
            addHorairesTbody.append(newChamps);
            nbChamps++
        }
    }
    
});

// SUPPRIMER DES CHAMPS
// Au click du remove bouton
$(addHorairesTbody).on('click', '.remove-horaire-button', function() {
    // Utilisation la méthode closest() pour trouver le tr le plus proche
    var trList = $(addHorairesTbody).find('tr');
    // Suppression de tous les tr
    trList.remove();
    // Décrémentation du nombre de champs
});


