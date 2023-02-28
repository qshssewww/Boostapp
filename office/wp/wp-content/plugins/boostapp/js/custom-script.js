jQuery(document).ready(function(){

    elementorFrontend.hooks.addAction( 'init', function() {
        // Do something that is based on the elementorFrontend object.
        console.log(window.elementorFrontend,  jQuery('#tmpl-elementor-panel-menu'));
       } );
       

    
    
});