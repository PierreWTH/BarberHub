// RECUPERATION DES CRENEAUX ET INITIALISATION DU SLIDER

  $(document).ready(function() {

    // Fonction pour initialiser le slider
    function initSplide() {
        var splide = new Splide('.splide', {
            perPage: 5,
        });
        splide.mount();
    }
    // On écoute le clic des input radio
    $(document).on('change', 'input[type=radio]', function() {
        // Si c'est checked
        if (this.checked) {
            // On récupere la value de personnel ID
            var personnel_id = $(this).attr('value');
            var data = {
                personnel_id: personnel_id
            };
        
            $.ajax({
                type: "POST",
                url: "/getCreneauxbyPersonnel/" + personnel_id,
                data: JSON.stringify(data),
                contentType: 'application/json',
                success: function(response) {
                    console.log(response);
                    // Ajout des créneaux au container
                    var creneauxContainer = $('.splide__list');
                    creneauxContainer.empty();
                    creneauxContainer.append(response);
                    // Initialisation du slider
                    initSplide()
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        };
    });
});

//RECUPERATION DU CRENEAUX CHOISI ET RESET STYLE BOUTON

function selectedCreneau(button, value) {
event.preventDefault();
    document.getElementById('rendez_vous_debut').value = value;
    console.log( document.getElementById('rendez_vous_debut').value)
    
    var allButtons = document.querySelectorAll('button');
    allButtons.forEach(function(btn) {
        btn.classList.remove('clicked-button');
    });

    // Ajouter la classe "clicked-button" au bouton cliqué
    var reservationButton = document.getElementById('rendez_vous_submit');
    button.classList.add('clicked-button');
    reservationButton.style.display = 'block';
}