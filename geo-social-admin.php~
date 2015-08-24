<?php
/*
   Plugin Name: Geo Social Admin
   Plugin URI: http://github.com/shiplet/geo-social-admin
   Description: Global social admin settings
   Version: 1.1.0
   Author: Michael Shiplet / Super Top Secret
   Author URI: http://wearetopsecret.com
   License: The MIT License (MIT)
 */

/*
   Copyright: 2015 Michael Shiplet (email: mnshiplet@gmail.com)
   All Rights Reserved
 */

ini_set("log_errors", 1);
ini_set("date.timezone", "America/Denver");

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

			///////////////////////////////
			// BEGIN POST INTENT CHECKS //
			/////////////////////////////

			// INTENT: Add either a new social stream AND a new API - OR - just a new API //

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
				if ($social['social_title']) {
					$wpdb->insert(
						$table_name_social,
						array(
							'time' => current_time('mysql'),
							'social_source' => $social['social_source'] ? strtolower($social['social_source']) : null,
							'social_title' => $social['social_title'] ? $social['social_title'] : null,
							'social_url' => $social['social_url'] ? $social['social_url'] : null,
							'social_geo' => $admin_geo_tag ? $admin_geo_tag : null,
							'social_content_type' => $social['social_url'] ? $social['social_content_type'] : null,
							'social_api_key' => $api['api_key'] ? $api['api_key'] : null,
							'social_api_secret' => $api['api_secret'] ? $api['api_secret'] : null,
							'social_api_name' => $api['api_name'] ? $api['api_name'] : null
							)
						);
				}
	    	}


	    	// Intent: Add just a new social stream with pre-existing API //

		    else if (!isset($_POST['admin_api_valid']) && !isset($api['edit_api']) && !isset($social['edit_social']) && !isset($api['delete_item']) && !isset($social['delete_item'])) {
		    	$social_api = $wpdb->get_row('SELECT * FROM ' . $table_name_api . ' WHERE id = ' . $social['api'], ARRAY_A);

		    	$wpdb->insert(
		    		$table_name_social,
		    		array(
		    			'time' => current_time('mysql'),
		    			'social_source' => strtolower($social['social_source']),
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


		    // Intent: edit a pre-existing API //

		    elseif (isset($api['edit_api']) && $api['edit_api'] === 'true') {
		    	$default_api = $wpdb->get_row('SELECT * FROM ' . $table_name_api . ' WHERE id = ' . $api['api_id'], ARRAY_A);
				$wpdb->update(
				    $table_name_api,
					array(
					    'time' => current_time('mysql'),
						'api_name' => $api['api_name'] ? $api['api_name'] : $default_api['api_name'],
						'api_key' => $api['api_key'] ? $api['api_key'] : $default_api['api_key'],
						'api_secret' => $api['api_secret'] ? $api['api_secret'] : $default_api['api_secret']
						),
					array(
					    'id' => $api['api_id']
						)
					);
				$wpdb->update(
					$table_name_social,
					array(
						'time' => current_time('mysql'),
						'social_api_name' => $api['api_name'] ? $api['api_name'] : $default_api['api_name'],
						'social_api_key' => $api['api_key'] ? $api['api_key'] : $default_api['api_key'],
						'social_api_secret' => $api['api_secret'] ? $api['api_secret'] : $default_api['api_secret']
						),
					array(
						'social_api_name' => $api['api_name_orig']
						)
					);
		    }


		    // Intent: Edit a pre-existing social stream and associated API //

		    elseif (isset($social['edit_social']) && $social['edit_social'] === 'true') {
		    	$default_social = $wpdb->get_row('SELECT * FROM ' . $table_name_social . ' WHERE id = ' . $social['social_id'], ARRAY_A);
		    	if ($social['api_name']) {
		    		$newApi = $wpdb->get_row('SELECT * FROM ' . $table_name_api . ' WHERE api_name = "' . $social['api_name'] . '"', ARRAY_A);
		    	}
		    	$wpdb->update(
		    		$table_name_social,
		    		array(
		    			'time' => current_time('mysql'),
		    			'social_source' => $social['social_source'] ? strtolower($social['social_source']) : strtolower($default_social['social_source']),
		    			'social_url' => $social['social_url'] ? $social['social_url'] : $default_social['social_url'],
		    			'social_content_type' => $social['social_url'] ? $social['social_content_type'] : $default_social['social_content_type'],
		    			'social_title' => $social['social_title'] ? $social['social_title'] : $default_social['social_title'],
		    			'social_geo' => $admin_geo_tag ? $admin_geo_tag : $default_social['social_geo'],
		    			'social_api_key' => $social['api_name'] ? $newApi['api_key'] : $default_social['social_api_key'],
		    			'social_api_secret' => $social['api_name'] ? $newApi['api_secret'] : $default_social['social_api_secret'],
		    			'social_api_name' => $social['api_name'] ? $social['api_name'] : $default_social['social_api_name']
		    			),
		    			array(
		    				'id' => $social['social_id']
		    				)
		    		);
		    }


		    // Intent: Delete an api item //

		    elseif (isset($api['delete_item']) && $api['delete_item'] === 'true') {
			$wpdb->delete(
			    $table_name_api,
				array(
				    'api_name' => $api['delete_this_item']

					)
				);
			$wpdb->update(
				$table_name_social,
				array(
					'social_api_name' => null,
					'social_api_key' => null,
					'social_api_secret' => null,
					),
				array(
					'social_api_name' => $api['delete_this_item']
					)
				);
		    }

		    elseif(isset($social['delete_item']) && $social['delete_item'] === 'true') {
		    	$wpdb->delete(
		    		$table_name_social,
		    		array(
		    			'id' => $social['delete_this_item']
		    			)
		    		);
		    }


		    // Redirect the page //

    		header("Location: " . $_SERVER['REQUEST_URI']);
 		    exit;
		    break;

	} else {
		break;
	}

    case 'GET':
    break;
}
