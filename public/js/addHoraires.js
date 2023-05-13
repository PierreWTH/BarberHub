
// Définition du bouton pour ajouter des horaires
var addOpeningButton = $('.add-horaire-button');

// Définition du bouton pour supprimer des horaires
var removeOpeningButton = $('.remove-horaire-button');

// Tableau de jours de la semaine
var joursSemaine = ["lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi", "dimanche"]

// Selection de la table d'ajout d'horaires
var addHorairesTbody = $('.addHorairesTbody')

// Initialisation du nombre de champs a 0
let nbChamps = 0

// AJOUTER DES CHAMPS
// Fonction qui se déclenche au clic du bouton
$(addOpeningButton).click(function() {

    // Si le nombre de champs est inférieur ou égal a 7
    if (nbChamps <= 6){
        // Définition d'un nouveau tr
        var newChamps = $('<tr></tr>');
        // Jour actuel :
        const jourActuel = joursSemaine[nbChamps]

        // Affichage du jour de la semaine
        newChamps.append('<td>' + jourActuel + '</td>')
        // Champs caché qui récupère le jour actuel
        newChamps.append('<input type="hidden" id="inputJourActuel" name="jourActuel" value="'+ jourActuel +'" />')
        // Ajout d'un nouveau champs pour les heures d'ouvertures
        newChamps.append('<td><input id="heureOuverture" type="time" name="ouverture['+ nbChamps +']"></td>');
        // Nouveau champs pour les heures de fermetures
        newChamps.append('<td><input id="heureFermeture" type="time" name="fermeture['+ nbChamps +']"></td>');
        // Bouton pour supprimer le champs crée
        newChamps.append('<td><button type="button" class="remove-horaire-button">Supprimer le champs</button></td>')
        // Ajout des champs au Tbody
        addHorairesTbody.append(newChamps);
        
        // Incrémentation du nombre de champs
        nbChamps++
    }
    else{
        alert('Vous ne pouvez pas ajouter d\'autres champs.')
    }
    
});

// SUPPRIMER DES CHAMPS
// Au click du remove bouton
$(addHorairesTbody).on('click', '.remove-horaire-button', function() {
    // Utilisation la méthode closest() pour trouver le tr le plus proche
    var tr = $(this).closest('tr');
    // Suppression du tr 
    tr.remove();
    // Décrémentation du nombre de champs
    nbChamps--
});


