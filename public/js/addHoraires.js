
// Définition du bouton pour ajouter des horaires
var addOpeningButton = $('#add-opening');

// Tableau de jours de la semaine
var joursSemaine = ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"]

// Index de chaque champs
var indexHoraires = 0

// Fonction qui se déclenche au clic du bouton
$(addOpeningButton).click(function() {

    if (indexHoraires <=6){
  
        var horairesCollectionHolder = document.querySelector('#barbershop_horaires');

        console.log(horairesCollectionHolder.dataset.prototype)
        
        horairesCollectionHolder.innerHTML += horairesCollectionHolder.dataset.prototype.replace(/__name__label__/g, joursSemaine[indexHoraires]);
        
        indexHoraires++;
        console.log(indexHoraires)
    }
    else{
        console.log('MAX');
    }
 });