
// Définition du bouton pour ajouter des horaires
var addOpeningButton = $('.add-horaire-button');

// Tableau de jours de la semaine
var joursSemaine = ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"]

// Selection de la table d'ajout d'horaires
var addHorairesTbody = $('.addHorairesTbody')

// Initialisation du nombre de champs a 0
let nbChamps = 0

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
        newChamps.append('<input type="hidden" id="inputJourActuel" name="jourActuel" value=" '+ jourActuel +'" />')
        // Ajout d'un nouveau champs pour les heures d'ouvertures
        newChamps.append('<td><input id="heureOuverture" type="time" name="ouverture['+ nbChamps +']"></td>');
        // Nouveau champs pour les heures de fermetures
        newChamps.append('<td><input id="heureFermeture" type="time" name="fermeture['+ nbChamps +']"></td>');
        // 
        addHorairesTbody.append(newChamps);
        
        nbChamps++
    }
    else{
        alert('Vous ne pouvez pas ajouter d\'autres champs.')
    }
    
});
