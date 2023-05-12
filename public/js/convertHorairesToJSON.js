//Selection du formulaire et écoute de l'évenement submit
document.getElementById('addBarbershopForm').addEventListener('submit', function (event) {
    // On empeche l'envoi du formulaire
    event.preventDefault();

    // Définition d'un tableau associatif vide qui contiendra les heures d'ouvertures
    var horaires = {}

    // Pour chaque tr du tableau
    $('#addBarbershopForm .addHorairesTbody tr').each(function() {

        // REMPLISSAGE DES VARIABLES

        // Input Hidden qui récupere le jour
        var jour = $(this).find('input[name="jourActuel"]').val() 
        // Input qui récupère l'ouverture (^ sert a indiquer que le nom doit commencer par ça)
        var ouverture = $(this).find('input[name^="ouverture"]').val() || 'ferme'
        // Input qui récupère la fermeture
        var fermeture = $(this).find('input[name^="fermeture"]').val() || 'ferme'

        // Si les variables sont valides
        if (jour && ouverture && fermeture){
    
            // Ajout dans le tableau
            horaires[jour] = {
                'jour' : jour,
                'ouverture' : ouverture,
                'fermeture' : fermeture    
            }
           
        }

    })   
    // Remplissage du champs caché Horaires avec le JSON des horaires (JSON.stringify pour convertir en JSON)
    $('#barbershop_horaires').val(JSON.stringify(horaires))
    console.log($('#barbershop_horaires').val())
    //On submit le formulaire
    this.submit()
});
