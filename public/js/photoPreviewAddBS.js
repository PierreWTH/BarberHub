var input = document.getElementById('barbershop_images');
var preview = document.getElementById('image-preview');

input.addEventListener('change', function (e) {
    var file = e.target.files[0]
    var reader = new FileReader();

    reader.onload = function(e) {
        preview.src = e.target.result;
        preview.style.display = 'block';
    };

    reader.readAsDataURL(file);
})