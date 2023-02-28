<?php
/**
 * @package Elementor
 */
/*
Plugin Name: Elementor-Customizer
Plugin URI: https://elementor.com/
Description: This plugin is use for customize Elementor Plugin.
Version: 1.0.1
Author: Ravi
Author URI: https://www.devinlabs.com/contact/
*/

add_action( 'elementor/editor/after_enqueue_styles', 'load_css_custom_elemenator', 10, 3 );
function load_css_custom_elemenator($d){

  	wp_register_script( 
		  'editor-frontend', 
		  plugin_dir_url( __FILE__ ).'elemnator-custom.js',
		array(),
		filemtime(plugin_dir_path( __FILE__ ).'elemnator-custom.js'),
		false
	);
  	wp_enqueue_script( 'editor-frontend' );	
}