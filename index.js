var button = document.getElementById('delete');

button.onclick = function() {
    deleteAll();
}

function deleteAll() {
    $.ajax({
        url: 'index.php',
        type: 'post',
        data: {
            'json' : 'img.json',
            'pathIMG' : 'img/'},
        success: function (response) {
           console.log('suppression réussi');
           window.location.replace('');//Renvoie la page actuel sans les données POST
        },
        error: function () {
           console.log('erreur desuppression');
        }
    });
}