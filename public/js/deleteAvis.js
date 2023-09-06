$(document).ready(function() {
        
    const notyf = new Notyf();

    $('.delete-avis').click(function(e) {
        e.preventDefault();
        var barbershopId = $(this).data('barbershop-id'); 
        var avisId = $(this).data('avis-id');
        var commentToDelete = $(this).closest('.barbershop-avis'); // Select the comment element
        
        $.ajax({
            type: "POST",
            url: "/barbershop/" + barbershopId + "/avis/" + avisId + "/delete",
            contentType: 'application/json',
            success: function(response) {
                commentToDelete.remove();
                notyf.error("Avis supprimé.");
                if ($('.barbershop-avis').length === 0) {
                    
                    $('#barbershop-avis-container').html('<div class="barbershop-avis"><p> Aucun avis pour le moment.  </p></div>');
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    });
});