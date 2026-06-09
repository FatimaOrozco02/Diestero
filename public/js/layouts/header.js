
// CONTROL RESPONSIVO DEL MENÚ DE HAMBURGUESA
$('.navbar-nav .nav-link').click(function() {
    // Verifica si el botón de hamburguesa está visible (significa que está en móvil)
    if ($('.navbar-toggler').is(':visible')) {
        // Ejecuta el cierre del colapsable nativo de Bootstrap
        $('#navbarNavAltMarkup').collapse('hide');
    }
});


