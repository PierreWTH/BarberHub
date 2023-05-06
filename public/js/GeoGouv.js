// AUTOCOMPLETE L'ADRESSE (en utilisant jQuery et Select2)

// Une fois que la page est totalement chargée
$(document).ready(function() 
{
    // Selection du champs adresse et initialisation de Select2
    $('#adresse-input').select2(
    {
        // recherche se déclenche après minimum 2 caractères rentrés
        minimumInputLenght:2,
        ajax: 
        {
            url: 'https://api-adresse.data.gouv.fr/search/',
            dataType: 'json'
            delay: 250,
            data : function(params)
            {
                return
                {
                    q: params.term
                };
            },
        }
    }
}