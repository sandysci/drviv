<?php
/*
Plugin Name: Sandy Test
Description: A test plugin to demonstrate wordpress functionality
Author: Sandysci
Version: 0.1
*/

add_action( 'admin_menu', 'addAdminMenu' );

function addAdminMenu(){
add_menu_page('GEE Admin Settings', 'GEE Admin', 'manage_options', 'gee_admin_settings_page', 'admin_pg_function', '', 7);
add_submenu_page('gee_admin_settings_page','User Role Editor', 'Edit User Roles','manage_options', 'user_role_editor_slug', 'edit_user_roles_function');
}

function admin_pg_function(){
if(!current_user_can('manage_options')){
wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
}
/*add any form processing code here in PHP:*/
echo '
<div style="width:750px;">
<h1><span style="position:relative;top:-7px">GoldEagleXpress.com Custom Admin Settings</span></h1>';
/*add the rest of your page content above here if it's HTML and below here if it's PHP!*/
}/*end admin_pg_function function.*/
/*end cody by Ian L. to add custom menu item to wp-admin...*/

function edit_user_roles_function(){
if(!current_user_can('manage_options')){
wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
}
echo'
<div style="width:750px;">
<h1><span style="position:relative;top:-7px">GoldEagleXpress.com Custom User Role Settings</span></h1>
';
}
?>