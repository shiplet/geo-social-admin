<?php
/*
   Plugin Name: Geo Social Admin
   Plugin URI: http://github.com/shiplet/geo-social-admin
   Description: Global social admin settings
   Version: 0.1
   Author: Michael Shiplet
   Author URI: http://shiplet.github.io
   License: The MIT License (MIT)
 */

/*
   Copyright: 2015 Michael Shiplet (email: mnshiplet@gmail.com)
   All Rights Reserved
 */

// Include the class
include_once dirname(__FILE__) . '/GeoSocialAdmin.class.php';

error_log('The geosocial admin plugin');

// instantiate the plugin
$geo_social_admin = new Geo_Social_Admin;

// trigger plugin to run when admin_menu does
add_action('admin_menu', array($geo_social_admin, 'adminOptions'));
add_action('admin_menu', 'add_jq_script');
add_action('admin_head', array($geo_social_admin, 'adminHead'));


// trigger plugin to create table on activation
register_activation_hook( __FILE__, array($geo_social_admin, 'tableCreate'));
register_activation_hook( __FILE__, array($geo_social_admin, 'dbInsert'));

// register scripts
function add_jq_script() {
    wp_register_script('geo_social_admin_script', plugins_url('/geo-social-admin/js/main.js'), array('jquery'), null, true);
    wp_enqueue_script('geo_social_admin_script');    
}


global $wpdb;

$table_name_api = 'geo_social_admin_api';
$table_name_social = 'geo_social_admin_social';

switch($_SERVER['REQUEST_METHOD'])
{
    case 'POST':
    if ($_POST['api_source']) {
	$wpdb->insert(
	    $table_name_api,
		array(
		    'time' => current_time('mysql'),
			'api_source' => $_POST['api_source'],
			'api_key' => $_POST['api_key'],
			'api_secret' => $_POST['api_secret']
		)
	);
    }

    if ($_POST['social_source']) {
	$wpdb->insert(
	    $table_name_social,
		array(
		    'time' => current_time('mysql'),
			'social_source' => $_POST['social_source'],
			'social_content_type' => $_POST['social_content_type'],
			'social_title' => $_POST['social_title'],
			'social_geo' => $_POST['social_geo'],
			'social_url' => $_POST['social_url']
		)
	);
    }

    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
    break;

    case 'GET':
    break;
}
