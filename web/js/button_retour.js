/*
 Mise en place par Truknet le 15/04/2017.

 Ne pas oublier de mettre en place dans le code HTML des pages concernées
 <div class="cRetour"></div>

 ainsi que le code CSS
 .cRetour {
 border-radius:3px;
 padding:10px;
 font-size:15px;
 text-align:center;
 color:#fff;
 background:rgba(0, 0, 0, 0.25);
 z-index:99999;
 transition:all ease-in 0.2s;
 position: fixed;
 cursor: pointer;
 bottom: 1em;
 right: 20px;
 display: none;
 }
 .cRetour:before{ content: "\25b2"; }
 .cRetour:hover{
 background:rgba(0, 0, 0, 1);
 transition:all ease-in 0.2s;
 }
*/

jQuery(document).ready(function() {
    var duration = 500;
    jQuery(window).scroll(function() {
        if (jQuery(this).scrollTop() > 100) {
            // Si un défillement de 100 pixels ou plus.
            // Ajoute le bouton
            jQuery('.cRetour').fadeIn(duration);
        } else {
            // Sinon enlève le bouton
            jQuery('.cRetour').fadeOut(duration);
        }
    });

    jQuery('.cRetour').click(function(event) {
        // Un clic provoque le retour en haut animé.
        event.preventDefault();
        jQuery('html, body').animate({scrollTop: 0}, duration);
        return false;
    })
});
