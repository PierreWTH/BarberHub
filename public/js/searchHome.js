$(document).ready(function() {
    $('#search_q, #search_sortBy, #search_city').on('input change', function() {
        const searchText = $('#search_q').val();
        const selectedOption = $('#search_sortBy').val();
        const city = $('#search_city').val();
        search(searchText, selectedOption, city);
    });
});
// Requete ajax pour effectuer la recherche
function search(searchText, selectedOption, city) {
    $.ajax({
        url: '/barbershops',    
        type: 'GET',
        data: { search: searchText,
                sort : selectedOption,
                city : city,
        },
        success: function(response) {
            $('#searchResults').html('');
            $('#searchResults').html(response);
            $('.pagination').hide();
            console.log(response)
        },
        error: function(xhr, status, error) {
            console.log(error)
        }
    });
}