
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
            // Ajout d'un nouveau champs pour les heures d'ouverture
            newChamps.append('<td><input id="heureOuverture" type="time" name="ouverture['+ i +']"></td>');
            // Nouveau champs pour les heures de fermetures
            newChamps.append('<td><input id="heureFermeture" type="time" name="fermeture['+ i +']"></td>');
            // Bouton pour supprimer le champs crée
            
            addHorairesTbody.append(newChamps);
            nbChamps++
  
        }
    }
    


