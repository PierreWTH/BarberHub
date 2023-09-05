$(document).ready(function () {
    var input = document.getElementById('barbershop_realisations');
    var preview = $('#image-preview');

    input.addEventListener('change', function (e) {
        preview.empty(); // Efface le contenu précédent de la prévisualisation

        for (var i = 0; i < e.target.files.length; i++) {
            var file = e.target.files[i];
            var reader = new FileReader();

            reader.onload = function (event) {
                var img = $('<img>'); // Crée un élément img jQuery
                img.attr('src', event.target.result);
                img.css('max-width', '200px'); // Vous pouvez définir la largeur maximale de l'image
                preview.append(img);
            };

            reader.readAsDataURL(file);
        }
    });
});