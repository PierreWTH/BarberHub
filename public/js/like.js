class Like {
    constructor(likeElements) {
      this.likeElements = likeElements;

      if(this.likeElements){
        this.init();
      }
    }
    init() {
        this.likeElements.map(element => {
            element.addEventListener('click', this.onClick)
        })
    }

    onClick(event) {
        event.preventDefault();
        const url = this.href;

        fetch(url)
            .then(response => response.json())
            .then(data => {
            // Récupération du nombre de likes
            const nb = data.nbLike;
            const span = this.querySelector('.like-span');

            this.dataset.nb = nb;
            if(span){
                span.innerHTML = nb + ' J\'aime';
            }
            const notyf = new Notyf();
            const heartFilled = this.querySelector('#heartFilled');
            const heartUnfilled = this.querySelector('#heartUnfilled');
            

            if(heartFilled && heartUnfilled){
                heartFilled.classList.toggle('hidden');
                heartUnfilled.classList.toggle('hidden');
            }
            // Notification
            if (heartFilled.classList.contains('hidden')) {
                notyf.error('Retiré des favoris');
            } else {
                notyf.success('Ajouté aux favoris');
            }


            
        })
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Fonction pour liker
    const likeElements = [].slice.call(document.querySelectorAll('a[data-action=like]'))
    // Si on l'a bien récupéré
    if(likeElements) {
        new Like(likeElements);

    }
})
