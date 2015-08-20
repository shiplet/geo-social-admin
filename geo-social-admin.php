<?php
/*
   Plugin Name: Geo Social Admin
   Plugin URI: http://github.com/shiplet/geo-social-admin
   Description: Global social admin settings
   Version: 1.0.0
   Author: Michael Shiplet / Super Top Secret
   Author URI: http://wearetopsecret.com
   License: The MIT License (MIT)
 */

/*
   Copyright: 2015 Michael Shiplet (email: mnshiplet@gmail.com)
   All Rights Reserved
 */

ini_set("log_errors", 1);
ini_set("error_log", "/var/log/php_error");

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
register_activation_hook(__FILE__, array($geo_social_admin, 'dbInsert'));

// register scripts
function add_jq_script() {
    wp_register_script('geo_social_admin_script', plugins_url('/geo-social-admin/js/main.js'), array('jquery'), null, true);
    wp_enqueue_script('geo_social_admin_script');
}

// Handle POST and GET requests
switch($_SERVER['REQUEST_METHOD'])
{
    case 'POST':
    	if ($_SERVER['SCRIPT_NAME'] === '/wp-admin/options-general.php') {

	    	$table_name_api = $geo_social_admin->get_api_table();
	    	$table_name_social = $geo_social_admin->get_social_table();

			$api = $_POST['admin_api'];
			$social = $_POST['admin_social'];

			$rss_pos = strpos($social['social_url'], 'rss');
			if ($rss_pos !== false) {
				$social['social_content_type'] = 'application/xml+rss';
			} else {
				$social['social_content_type'] = 'application/json';
			}


			if ($_POST['admin_api_valid'] === 'true') {

				$wpdb->insert(
					$table_name_api,
					array(
						'time' => current_time('mysql'),
						'api_name' => $api['api_name'],
						'api_key' => $api['api_key'],
						'api_secret' => $api['api_secret']
						)
					);
				$wpdb->insert(
					$table_name_social,
					array(
						'time' => current_time('mysql'),
						'social_source' => $social['social_source'] ? $social['social_source'] : null,
						'social_title' => $social['social_title'] ? $social['social_title'] : null,
						'social_url' => $social['social_url'] ? $social['social_url'] : null,
						'social_geo' => $admin_geo_tag ? $admin_geo_tag : null,
						'social_content_type' => $social['social_content_type'] ? $social['social_content_type'] : null,
						'social_api_key' => $api['api_key'] ? $api['api_key'] : null,
						'social_api_secret' => $api['api_secret'] ? $api['api_secret'] : null,
						'social_api_name' => $api['api_name'] ? $api['api_name'] : null
						)
					);
	    	}
		    else if (!isset($_POST['admin_api_valid']) && !isset($api['edit_api']) && !isset($social['edit_social'])) {

		    	$social_api = $wpdb->get_row('SELECT * FROM ' . $table_name_api . ' WHERE id = ' . $social['api'], ARRAY_A);

		    	$wpdb->insert(
		    		$table_name_social,
		    		array(
		    			'time' => current_time('mysql'),
		    			'social_source' => $social['social_source'],
		    			'social_title' => $social['social_title'],
		    			'social_url' => $social['social_url'],
		    			'social_geo' => $admin_geo_tag,
		    			'social_content_type' => $social['social_content_type'],
						'social_api_key' => $social_api['api_key'],
						'social_api_secret' => $social_api['api_secret'],
						'social_api_name' => $social_api['api_name']
		    			)
		    		);
		    }
		    elseif (isset($api['edit_api'])) {
		    	error_log(print_r($api,true));
		    	$default = $wpdb->get_row('SELECT * FROM ' . $table_name_api . ' WHERE id = ' . $api['api_id'], ARRAY_A);
		    	error_log(print_r($default,true));
				$wpdb->update(
				    $table_name_api,
					array(
					    'time' => current_time('mysql'),
						'api_name' => $api['api_name'] ? $api['api_name'] : $default['api_name'],
						'api_key' => $api['api_key'] ? $api['api_key'] : $default['api_key'],
						'api_secret' => $api['api_secret'] ? $api['api_secret'] : $default['api_secret']
						),
					array(
					    'id' => $api['api_id']
						)
					);
				$wpdb->update(
					$table_name_social,
					array(
						'time' => current_time('mysql'),
						'social_api_name' => $api['api_name'] ? $api['api_name'] : $default['api_name'],
						'social_api_key' => $api['api_key'] ? $api['api_key'] : $default['api_key'],
						'social_api_secret' => $api['api_secret'] ? $api['api_secret'] : $default['api_secret']
						),
					array(
						'social_api_name' => $api['api_name_orig']
						)
					);
		    }
		    elseif (isset($social['edit_social'])) {
		    	error_log(print_r($social,true));
		    }
    // elseif (isset($_POST[$admin_api]['delete_item']) && $_POST[$admin_api]['delete_item'] === 'true') {
	// $wpdb->delete(
	//     $table_name_api,
	// 	array(
	// 	    'id' => $_POST[$admin_api]['delete_this_item']
	// 	)
	// );
 //    }

 //    if ($_POST['admin_social_valid'] === 'true') {
	// foreach($_POST[$admin_social] as $source) {

	//     $wpdb->insert(
	// 	$table_name_social,
	// 	    array(
	// 		'time' => current_time('mysql'),
	// 		    'social_source' => $source['social_source'],
	// 		    'social_title' => $source['social_title'],
	// 		    'social_url' => $source['social_url'],
	// 		    'social_content_type' => $source['social_content_type'],
	// 		    'social_geo' => $admin_geo_tag
	// 	    )
	//     );
	// }
 //    } elseif (
	// !isset($_POST['admin_social_valid'])
	//     && $_POST[$admin_social]['social_source']
	// && $_POST[$admin_social]['social_url']
	// && $_POST[$admin_social]['social_title']
 //    ) {
	// $pos = strpos($_POST[$admin_social]['social_url'], 'rss');

	// if ($pos !== false)
	// {
	//     $_POST[$admin_social]['social_content_type'] = 'application/xml+rss';
	// } else
	// {
	//     $_POST[$admin_social]['social_content_type'] = 'application/json';
	// }

	// $wpdb->update(
	//     $table_name_social,
	// 	array(
	// 	    'time' => current_time('mysql'),
	// 		'social_source' => $_POST[$admin_social]['social_source'],
	// 		'social_url' => $_POST[$admin_social]['social_url'],
	// 		'social_title' => $_POST[$admin_social]['social_title'],
	// 		'social_content_type' => $_POST[$admin_social]['social_content_type'],
	// 	),
	// 	array(
	// 	    'id' => $_POST[$admin_social]['social_id']
	// 	)
	// );
 //    } elseif (isset($_POST[$admin_social]['delete_item']) && $_POST[$admin_social]['delete_item'] === 'true') {
	// $wpdb->delete(
	//     $table_name_social,
	// 	array(
	// 	    'id' => $_POST[$admin_social]['delete_this_item']
	// 	)
	// );
 //    }


    	header("Location: " . $_SERVER['REQUEST_URI']);
	    exit;
	    break;
	} else {
		break;
	}

    case 'GET':
    break;
}
