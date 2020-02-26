$('#add-image').click(function(){
    // compte le nombre de div.form-group se trouvant à l'intérieur de div#ad_images
    //const index = $('#ad_images div.form-group').length;
    const index = +$('#widgets-counter').val();

    // récupération du code des champs à ajouter
    const tmpl = $('#ad_images').data('prototype').replace(/__name__/g, index);

    //console.log(tmpl);
    // Ajouter le code des champq à ajouter dans la div#ad_images
    $('#ad_images').append(tmpl);

    // incrémentation de l'index
    $('#widgets-counter').val(index + 1);
    
    // Gestion du bouton de suppression
    handleDeleteButtons();
})

function handleDeleteButtons(){
    $('button[data-action="delete"]').click(function(){
        const target = this.dataset.target;
        $(target).remove();
    })
}

function updateCounter(){
    const count = +$('#ad_images div.form-group').length();
    $('#widgets-counter').val(count);
}

// Mise à jour du nombre d'images
updateCounter();

// On appel aussi la fonction de suppression au chargement de la page
handleDeleteButtons();