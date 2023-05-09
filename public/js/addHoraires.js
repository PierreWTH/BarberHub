
// Définition du bouton pour ajouter des horaires
var addOpeningButton = $('#add-opening');

// Fonction qui se déclenche au clic du bouton
$(addOpeningButton).click(function() {
  
var horairesCollectionHolder = document.querySelector('#barbershop_horaires');

  
console.log(horairesCollectionHolder.dataset.prototype)

  
horairesCollectionHolder.innerHTML += horairesCollectionHolder.dataset.prototype.replace(/__name__/g, indexHoraires);
  
indexHoraires++;
console.log(indexHoraires)
  
 });