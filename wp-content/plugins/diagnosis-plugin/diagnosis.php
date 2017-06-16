<?php
/*
  Plugin Name: Diagnosis Plugin
  Plugin URI: http://wwww.drviv.sci.ng
  Description: All about diagnosis.
  Version: 1.0
  Author: Ezeibe Sandra Chioma
  Author URI: http://gitlab.com/sandysci
 */

add_action( 'admin_menu', 'addAdminMenu2' );
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'diagnosis-plugin/userform.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'diagnosis-plugin/database.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'diagnosis-plugin/pages.php';

add_action('admin_init','plugin_init');


function plugin_init(){
    $to      = 'ezeibesandra@gmail.com';
    $subject = 'Testemail';
    $message = 'Sandy Testmail is working';
     $mail = wp_mail( $to, $subject, $message);
    if($mail){
      echo("yesss");
    }
    else{
       echo("nooooo");
    }
}

function addAdminMenu2(){
    add_menu_page('Diagnosis', 'Diagnosis', 'manage_options', 'diagnosis_page', 'diagnosis_page_function', '', 4);
    add_submenu_page('diagnosis_page','Diagnosis List', 'Diagnosis List','manage_options', 'diagnosis_list_page', 'diagnosis_page2_function');
    add_submenu_page(null,'Diagnosis Detail', 'Diagnosis Detail','manage_options', 'diagnosis_detail_page', 'diagnosis_detail_function');
    add_submenu_page(null,'Diagnosis Chat', 'Diagnosis Chat','manage_options', 'diagnosis_chat_page', 'diagnosis_chat_function');
}

function admin_css() {
  wp_enqueue_style( 'custom_wp_admin_css', plugins_url('css/bootstrap.css', __FILE__) );
  wp_enqueue_style( 'custom_wp_admin_css2', plugins_url('css/style.css', __FILE__) );
  wp_enqueue_script( 'custom_wp_admin_js', plugins_url('js/bootstrap.js', __FILE__) );
}


add_action('admin_enqueue_scripts', 'admin_css');

function diagnosis_page_function(){
    
  $dpage =  new DiagnosisPage;
  $dpage->diagnosis_page_function();
}
function diagnosis_detail_function(){
    
  $dpage =  new DiagnosisPage;
  $dpage->diagnosis_detail_function();
}
function diagnosis_chat_function(){
    
  $dpage =  new DiagnosisPage;
  $dpage->diagnosis_chat_function();
}

function diagnosis_page2_function(){
  $dpage =  new DiagnosisPage;
  $dpage->diagnosis_page2_function();
}
function wpse_load_plugin_css() {
  $user = new User;
  $user->daignosis_plugin_css();

  }

// add_action( 'wp_enqueue_scripts', 'wpse_load_plugin_css' );            

function create_plugin_database_table()
{
    $db = new DignosisDB;
    $db->Activate();
}
 
register_activation_hook( __FILE__, 'create_plugin_database_table' );
 
function loadshortcode() {
    ob_start();
    $user = new User;
    $user->registration_form();
    $user->validate();
   // $user->complete_registration();
    return ob_get_clean();
}
add_shortcode( 'diagnosis_form', 'loadshortcode' );
?>