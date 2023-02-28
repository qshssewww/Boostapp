<?php
/*
Plugin Name:  Boostapp elementor
Description:  Basic WordPress Plugin Header Comment
Version:      20180916
Author:       Shlomo Framowitz
Author URI:   247Soft.co.il
*/

if(!DEFINED('DS')) DEFINE('DS', DIRECTORY_SEPARATOR);
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
include __DIR__.DS.'..'.DS.'..'.DS.'..'.DS.'..'.DS.'..'.DS.'app/init.php';

add_action( 'elementor/frontend/before_enqueue_scripts', 'widget_scripts' );
function widget_scripts() {
    exit('widget_scripts');
    wp_register_script( 'some-library', plugins_url( 'js/custom-script.js', __FILE__ ) );
}




add_action('wp', 'autoLogin');

function autoLogin(){


    if(empty($_GET['action']) || $_GET['action'] != 'elementor') return false;

    if( strstr($_SERVER['REQUEST_URI'], 'wp-admin/post.php') && $_GET['action'] == 'elementor' ) {
        if (Auth::guest()){
            wp_logout();
            redirect_to(App::url());
            exit;
        }

        
        $wpUser = sprintf("team@%s.com", Auth::user()->CompanyNum);
        // create user if dosn't exists
        if( !email_exists( $wpUser )) {
            $userdata = array(
                'user_login'  =>  $wpUser,
                'user_email'  =>  $wpUser, 
                'user_pass'   =>  Auth::user()->password,   // password will be username always
                'first_name'  =>  (!empty($SettingsInfo->AppName))?$SettingsInfo->AppName:Auth::user()->display_name,  // first name will be username
                'role'        =>  'editor'     //register the user with subscriber role only
            );
    
            $user_id = wp_insert_user( $userdata ) ;       
        }

        $current_user = wp_get_current_user();
        if(is_user_logged_in() === FALSE || $current_user->user_email != $wpUser){
            $user_id = $wpdb->get_var($wpdb->prepare("SELECT * FROM ".$wpdb->users." WHERE user_email = %s", $wpUser ) );
            wp_set_auth_cookie($user_id);
            do_action('wp_login', $wpUser);
        }
    }
}



// function callback($buffer) {
//     if( strstr($_SERVER['REQUEST_URI'], 'wp-admin/post.php') && $_GET['action'] != 'elementor' ) return $buffer;
//     // modify buffer here, and then return the updated 
   

//     $doc = new \DOMDocument();
//     $doc->loadHTML($buffer);

//     $element = $doc->getElementById('tmpl-elementor-panel-menu');




//     return $element->saveHTML();
//   }
  
//   function buffer_start() { ob_start("callback"); }
  
//   function buffer_end() { ob_end_flush(); }
  
//   add_action('wp_loaded', 'buffer_start');
//   add_action('shutdown', 'buffer_end');
