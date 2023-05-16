$(document).ready(function() {

    var joursSemaine = ["lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi", "dimanche"]

    var o = 0
    var f = 0

    $('[id="heureOuverture"]').each(function() {

        day = joursSemaine[o]
        $(this).val(editHoraires[day]['ouverture']);
            o++
    
    });

    $('[id="heureFermeture"]').each(function() {

        day = joursSemaine[f]
        $(this).val(editHoraires[day]['fermeture']);
            f++
        
    });

});