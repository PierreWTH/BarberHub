// RECUPERATION DES CRENEAUX ET INITIALISATION DU SLIDER
    
  $(document).ready(function() {

    $(document).on('click', '.personnel-radio', function() {
        var radio = $(this).find('input[type="radio"]');

        radio.prop('checked', true).trigger('change');
        $(this).addClass('clicked');
        $('.personnel-radio').not(this).removeClass('clicked');
    });

    // Fonction pour initialiser le slider
    function initSplide() {
        var splide = new Splide('.splide', {
            perPage:5,
            width: "100%",
            pagination : false,
            breakpoints: { 
                900 : {
                    perPage: 3
                },

                600 : {
                    perPage : 2
                }
            }
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

                    // Ajout des créneaux au container
                    var creneauxContainer = $('.splide__list');
                    creneauxContainer.empty();
                    creneauxContainer.append(response);
                    // Initialisation du slider
                    initSplide()
                    // On enleve le bouton reservation car aucun créneau selectionné
                    var reservationButton = document.getElementById('rendez_vous_submit');
                    reservationButton.style.display = 'none';
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

    var allButtons = document.querySelectorAll('button');
    allButtons.forEach(function(btn) {
        btn.classList.remove('clicked-button');
    });

    // Ajouter la classe "clicked-button" au bouton cliqué
    var reservationButton = document.getElementById('rendez_vous_submit');
    button.classList.add('clicked-button');
    reservationButton.style.display = 'block';
}

