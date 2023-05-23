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
            const span = this.querySelector('span');

            this.dataset.nb = nb;
            span.innerHTML = nb + ' J\'aime';
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
