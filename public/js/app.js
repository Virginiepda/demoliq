
//on fait une requête ajax pour envoyer le clap

function sendClap(){

    var url=jQuery(this).attr("data-url");
    var lastClick = this;

    jQuery.ajax({
        url: url,
        method: 'post'
    })
        .done(function(response){
            var newClapNumber = response.data.claps;    //le data.claps est ce qu'on a récupéré dans le controller
            console.log(newClapNumber);
            jQuery(lastClick).next().html(newClapNumber);
        });
}

jQuery('.clap-btn').on("click", sendClap);