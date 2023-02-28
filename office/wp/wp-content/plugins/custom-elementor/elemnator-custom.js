$(document).ready(function() {
    $(document).on("click","#elementor-panel-header-menu-button",function() {
        $("#elementor-panel-page-menu-footer").hide();
    });	
	
	$(document).on("click",".elementor-icon",function() {
        $("#elementor-panel-category-wordpress").hide();
    });	
});


$( window ).load(function() {

	/* Working Code for hide Wordpress button */
	$("#elementor-panel-category-wordpress").hide();
	$('#elementor-panel-category-theme-elements-single').hide();
	$('#elementor-panel-category-theme-elements').hide();


	
	$(document).on("click","#elementor-panel-category-basic",function(){		
		$("#elementor-panel-category-wordpress").hide();
    });
	$(document).on("click","#elementor-panel-category-general",function(){		
		$("#elementor-panel-category-wordpress").hide();
    });	
	$(document).on("click","#elementor-panel-category-wordpress",function() {
		$("#elementor-panel-category-wordpress").hide();
    });
});