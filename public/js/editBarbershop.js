// SEULEMENT EN MODE EDIT

// Remplir les horaires depuis la BDD
$(document).ready(function() {

    var joursSemaine = ["lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi", "dimanche"]

    var o = 0
    var f = 0
    //Remplissages des horaires d'ouverture
    $('[id="heureOuverture"]').each(function() {

        day = joursSemaine[o]
        $(this).val(editHoraires[day]['ouverture']);
            o++
    
    });
    // Remplissages des horaires de fermeture
    $('[id="heureFermeture"]').each(function() {

        day = joursSemaine[f]
        $(this).val(editHoraires[day]['fermeture']);
            f++
        
    });

});


// Supprimer une photo 
let links = document.querySelectorAll("[data-delete]");

// Boucle sur les liens
for(let link of links)
{
    link.addEventListener("click", function(e){
        //On empeche la navigation
        e.preventDefault();

        // On demande confirmation
        if(confirm("Voulez vous supprimez cette photo ? ")){
            // On envoie la requete ajax
            fetch(this.getAttribute("href"),{
                method : "DELETE",
                headers : {
                    "X-Requested-With": "XMLHttpRequest",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({"_token": this.dataset.token})
            }).then(response => response.json())
            .then(data => {
                if(data.success){
                    this.parentElement.remove();
                }
                else{
                    alert(data.error);
                }
            })
        }   
    });
}