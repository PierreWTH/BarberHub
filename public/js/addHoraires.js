// Définition du bouton pour ajouter des horaires
var addOpeningButton = $('#add-opening');

// Tableau de jours de la semaine
var joursSemaine = ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"]

// Fonction qui se déclenche au clic du bouton
$(addOpeningButton).click(function() {

    // Boucle d'affichage des 7 champs pour chaque jour
    for(indexHoraires  = 0; indexHoraires<=6; indexHoraires++){
  
        var horairesCollectionHolder = document.querySelector('#barbershop_horaires');
        
        horairesCollectionHolder.innerHTML += horairesCollectionHolder.dataset.prototype.replace(/__name__/g, indexHoraires);
        
    }
    
 });
