function bp_get_querystring( n ) {
   var half = location.search.split( n + '=' )[1];
   return half ? decodeURIComponent( half.split('&')[0] ) : null;
}


    window.twttr = (function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0],
    t = window.twttr || {};
  if (d.getElementById(id)) return t;
  js = d.createElement(s);
  js.id = id;
  js.src = "https://platform.twitter.com/widgets.js";
  fjs.parentNode.insertBefore(js, fjs);
 
  t._e = [];
  t.ready = function(f) {
    t._e.push(f);
  };
 
  return t;
}(document, "script", "twitter-wjs"));

twttr.ready;

jQuery(document).ready( function() {


	var maxHeight = 0;
	jQuery(".column").each(function(){
		maxHeight = jQuery(this).height() > maxHeight ? jQuery(this).height() : maxHeight;
	}).height(maxHeight);

	var maxHeight = 0;
	jQuery(".column_2").each(function( ){
		maxHeight = jQuery(this).height() > maxHeight ? jQuery(this).height() : maxHeight;
	}).height(maxHeight);


	jQuery('.lanzar-menu').click( function( event ) {

		event.preventDefault();
		jQuery('.panel_menu_login').slideUp();
		jQuery('.panel_menu_usuario').slideUp();
		jQuery('.panel_menu_libros').slideUp();
		jQuery('.menu-phone').slideToggle();
		jQuery(this).toggleClass('active');

	});

	jQuery('.desplegar_libros_btt').click( function( event ) {

		event.preventDefault();
		jQuery('.panel_menu_login').slideUp();
		jQuery('.panel_menu_usuario').slideUp();
		jQuery('.menu-phone').slideUp();
		jQuery('.panel_menu_libros').slideToggle();

	});

	jQuery('.desplega_menu_btt').click( function( event ) {

		event.preventDefault();
		jQuery('.panel_menu_login').slideUp();
		jQuery('.panel_menu_libros').slideUp();
		jQuery('.menu-phone').slideUp();
		jQuery('.panel_menu_usuario').slideToggle();

	});

	jQuery('.desplega_login_btt').click( function( event ) {

		event.preventDefault();
		jQuery('.panel_menu_usuario').slideUp();
		jQuery('.panel_menu_libros').slideUp();
		jQuery('.menu-phone').slideUp();
		jQuery('.panel_menu_login').slideToggle();

	});



	jQuery('.leer_resena').click( function( event ) {

		event.preventDefault();
		this_id = jQuery(this).attr('id');

		jQuery('#resena_' + this_id ).toggleClass('desplegado');
		jQuery(this).toggleClass('activo');

		if ( jQuery(this).hasClass('activo') ) {

			jQuery(this).html('enconger reseña');

		} else {

			jQuery(this).html('leer reseña completa');

		}

	});

	/* Mostrar Listas */

	jQuery('#btn_listas_pop_up').click( function( event ) {
		event.preventDefault();
		jQuery('.overlay.listas').fadeIn();
	});

	jQuery('#btn_libreria_pop_up').click( function( event ) {
		event.preventDefault();
		jQuery('.overlay.add_libreria').fadeIn();
	})

	jQuery('.overlay .cerrar').click( function( event ) {
		event.preventDefault();
		jQuery('.overlay').fadeOut();

	})

	/* Ocultar feedback */

	jQuery('.overlay .cerrar.feedback-close').click( function( event ) {

		event.preventDefault();
		jQuery('.overlay').fadeOut();
		window.location = window.location.href.split("?")[0];

	});


    jQuery('.cambiar_password').click( function() {
        jQuery('.reset_password').slideToggle();
        var text = jQuery(this).text();
        jQuery(this).text(
            text == 'Cambiar contraseña' ? 'Cancelar' : 'Cambiar contraseña'
        ); 
    })
    
    if($(".featus-genre").length > 0){
        localStorage.setItem('organizar-libros', 'grid');
    }
    
    $(".boton-lista").click(function(){
        if(!$(this).hasClass("active")){
            localStorage.setItem('organizar-libros', 'lista');
            $(".js-grider").removeClass("griding");
            $(".js-no-grider").css("display", "block");
            $(this).addClass("active");
            $(".boton-grid").removeClass("active");
        }

    });
    
    $(".boton-grid").click(function(){
        if(!$(this).hasClass("active")){
            localStorage.setItem('organizar-libros', 'grid');
            $(".js-grider").addClass("griding");
            $(".js-no-grider").css("display", "none");
            $(this).addClass("active");
            $(".boton-lista").removeClass("active");
        }
    });
            
    if(localStorage.getItem('organizar-libros') == 'lista'){
        console.log("lista!");
        $(".js-grider").removeClass("griding");
        $(".js-no-grider").css("display", "block");
        $(".boton-lista").addClass("active");
        $(".boton-grid").removeClass("active");
    }else{
        $(".js-grider").addClass("griding");
        $(".js-no-grider").css("display", "none");
    }
    
    $(".js-grider").css("opacity", "1");
    $(".js-no-grider").css("opacity", "1");
    $(".organizator").css("opacity", "1");
    
});
