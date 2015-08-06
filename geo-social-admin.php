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

// trigger plugin to create table on activation
register_activation_hook( __FILE__, array($geo_social_admin, 'tableCreate'));
register_activation_hook( __FILE__, array($geo_social_admin, 'dbInsert'));
