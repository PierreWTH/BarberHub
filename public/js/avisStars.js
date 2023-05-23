window.onload = () => {
    
    // On récupère toutes les étoiles
    const stars = document.querySelectorAll(".fa-star");

    // On récupere l'input
    const note = document.querySelector("#avis_note")

    const noteValue = note.value;
    resetStars(noteValue);

    // Valeur de base a 0
    note.value = 0

    // On boucle sur les étoiles pour ajouter des ecouteur d'evenement

    for(star of stars){
        // On ecoute le survol
        star.addEventListener("mouseover", function(){
            resetStars();
            this.style.color = "#f7ff3c";
            this.classList.add("fa-solid")
            this.classList.remove("fa-regular")

            // Récupération de l'élémenet précedent et de meme balise
            let previousStar = this.previousElementSibling;
            
            // tant qu'il y a une étoile précédente
            while(previousStar){
            // On passe l'étoile qui précede en jaune et en solid
            previousStar.style.color = "#f7ff3c";
            previousStar.classList.add("fa-solid")
            previousStar.classList.remove("fa-regular")
            // On récupere l'étoile qui la précede
            previousStar = previousStar.previousElementSibling;
            }
        });

        // On ecoute le clic
        star.addEventListener("click", function(){
            note.value = this.dataset.value;
        })
        // Quand on enleve la souris, on reviens a la value du champs
        star.addEventListener("mouseout", function(){
            resetStars(note.value);
        })
    }


    function resetStars(note = 0){
        for(star of stars){
            // Si le nombre d'étoile est plus grand que la note on met en noir 
            if(star.dataset.value > note){
                star.style.color = "black";
                star.classList.add("fa-regular")
                star.classList.remove("fa-solid")
            }
            else{
            // Sinon on met en jaune
                star.style.color = "#f7ff3c"
                star.classList.add("fa-solid")
                star.classList.remove("fa-regular")
            }
        }
    }
}