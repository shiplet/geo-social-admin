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

// instantiate the plugin
$geo_social_admin = new Geo_Social_Admin;

// Variable declarations
global $wpdb;
$admin_geo_tag = $geo_social_admin->get_geo_tag();
$admin_api = 'admin_api';
$admin_social = 'admin_social';

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

// Handle POST and GET requests
switch($_SERVER['REQUEST_METHOD'])
{        
    case 'POST':
    $table_name_api = $geo_social_admin->get_api_table();
    $table_name_social = $geo_social_admin->get_social_table();

    if ($_POST['admin_api_valid'] === 'true') {
	foreach ($_POST[$admin_api] as $source) {
	    $wpdb->insert(
		$table_name_api,
		    array(
			'time' => current_time('mysql'),
			    'api_source' => $source['api_source'],
			    'api_key' => $source['api_key'],
			    'api_secret' => $source['api_secret']
		    )
	    );
	}
    } elseif (
	!isset($_POST['admin_api_valid'])
	    && $_POST[$admin_api]['api_source']
	&& $_POST[$admin_api]['api_key']
	&& $_POST[$admin_api]['api_secret']
    ) {
	$wpdb->update(
	    $table_name_api,
		array(
		    'time' => current_time('mysql'),
			'api_source' => $_POST[$admin_api]['api_source'],
			'api_key' => $_POST[$admin_api]['api_key'],
			'api_secret' => $_POST[$admin_api]['api_secret']
		),
		array(
		    'id' => $_POST[$admin_api]['api_id']
		)
	);
    } elseif (isset($_POST[$admin_api]['delete_item']) && $_POST[$admin_api]['delete_item'] === 'true') {
	$wpdb->delete(
	    $table_name_api,
		array(
		    'id' => $_POST[$admin_api]['delete_this_item']
		)
	);
    }

    if ($_POST['admin_social_valid'] === 'true') {
	foreach($_POST[$admin_social] as $source) {
	    $pos = strpos($source['social_url'], 'rss');

	    if ($pos !== false)
	    {
		$source['social_content_type'] = 'application/xml+rss';
	    } else
	    {
		$source['social_content_type'] = 'application/json';
	    }

	    $wpdb->insert(
		$table_name_social,
		    array(
			'time' => current_time('mysql'),
			    'social_source' => $source['social_source'],
			    'social_title' => $source['social_title'],
			    'social_url' => $source['social_url'],
			    'social_content_type' => $source['social_content_type'],
			    'social_geo' => $admin_geo_tag
		    )
	    );
	}
    } elseif (
	!isset($_POST['admin_social_valid'])
	    && $_POST[$admin_social]['social_source']
	&& $_POST[$admin_social]['social_url']
	&& $_POST[$admin_social]['social_title']
    ) {
	$pos = strpos($_POST[$admin_social]['social_url'], 'rss');

	if ($pos !== false)
	{
	    $_POST[$admin_social]['social_content_type'] = 'application/xml+rss';
	} else
	{
	    $_POST[$admin_social]['social_content_type'] = 'application/json';
	}
	
	$wpdb->update(
	    $table_name_social,
		array(
		    'time' => current_time('mysql'),
			'social_source' => $_POST[$admin_social]['social_source'],
			'social_url' => $_POST[$admin_social]['social_url'],
			'social_title' => $_POST[$admin_social]['social_title'],
			'social_content_type' => $_POST[$admin_social]['social_content_type'],
		),
		array(
		    'id' => $_POST[$admin_social]['social_id']
		)
	);
    } elseif (isset($_POST[$admin_social]['delete_item']) && $_POST[$admin_social]['delete_item'] === 'true') {
	$wpdb->delete(
	    $table_name_social,
		array(
		    'id' => $_POST[$admin_social]['delete_this_item']
		)
	);
    }


    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
    break;

    case 'GET':
    break;
}
