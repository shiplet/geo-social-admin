<?php

class Geo_Social_Admin {

    private $table_name_api = 'geo_social_admin_api';
    private $table_name_social = 'geo_social_admin_social';
    
    public function tableCreate()
    {

	error_log('tableCreate is running');

	$path = get_home_path();

	global $wpdb;
	
	require_once( $path . '/wp-admin/includes/upgrade.php');

	$table_name_api = 'geo_social_admin_api';
	$table_name_social = 'geo_social_admin_social';

	$charset_collate = $wpdb->get_charset_collate();
	
	$api = "CREATE TABLE $table_name_api (
id mediumint(9) NOT NULL AUTO_INCREMENT,
time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
source text NOT NULL,
api_key mediumtext NOT NULL,
api_secret mediumtext NOT NULL,
UNIQUE KEY id (id)
) $charset_collate;";

	$social = "CREATE TABLE $table_name_social (
id mediumint(9) NOT NULL AUTO_INCREMENT,
time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
source text NOT NULL,
content_type text NOT NULL,
title text NOT NULL,
geo text NOT NULL,
url longtext NOT NULL,
UNIQUE KEY id (id)
) $charset_collate;";

	dbDelta( $api );
	dbDelta( $social );

	error_log('tableCreate finished running.');
    }

    public function dbInsert()
    {
	error_log('dbInsert is running');
	global $wpdb;
	
	$default_check_api = $wpdb->get_row("SELECT * FROM $this->table_name_api WHERE id=1");
	$default_check_social = $wpdb->get_row("SELECT * FROM $this->table_name_social WHERE id=1");

	if (!$default_check_api) {
	    $wpdb->insert(
		$table_name_api,
		    array(
			'time' => current_time('mysql'),
			    'source' => 'Which social media source you\'re using',
			    'api_key' => 'The API key',
			    'api_secret' => 'The API secret'
		    )
	    );
	};
	
	if (!$default_check_social) {
	    $wpdb->insert(
		$table_name_social,
		    array(
			'time' => current_time('mysql'),
			    'source' => 'Which social media source you\'re using',
			    'content_type' => 'application/json',
			    'title' => 'The title that will appear on blog posts',
			    'geo' => 'general_test',
			    'url' => 'The api reference URL or RSS feed'
		    )

			
	    );
	};

	
	error_log('dbInsert finished');
    }

    public function adminOptions()
    {
	
	error_log('the adminOptions function');	
	
        // register the page with WP backend
        register_setting(
	    'geo_social_admin', 
		'geo_social_admin'/*,
				     $sanitize_callback */
	);

        // add page to WP menu
        add_options_page(
            'Geo Social Admin', // Page title
		'Geo Social Admin', // Menu title
		'manage_options', // Capability - which users can see it
		'geo_social_admin', // Menu slug
		array($this, 'render_social_options') // Function that outputs this stuff to the page
        );

        add_settings_section(
            'api_fields', // Attribute to appear in id tags
		'API Keys & Secrets', // Name of the section
		array($this, 'API_fields_section'), // Function that outputs stuff to the page
		'geo_social_admin' // Which page to attach to, should be slug of add_options_page            
        );

	add_settings_field(
	    'api_key', 
		'API Key',
		array($this, 'api_key_field'),
		'geo_social_admin',
		'api_fields'
	);

	add_settings_section(
	    'social_fields',
		'Social Platforms',
		array($this, 'social_platforms_section'),
		'geo_social_admin'
	);

	add_settings_field(
	    'new_facebook',
		'Facebook',
		array($this, 'facebook_field'),
		'geo_social_admin',
		'social_fields'
	);
    }

    public function render_social_options()
    {
?>
  <form action="options.php" method="post">
    <?php settings_fields('geo_social_admin'); ?>
    <?php
    do_settings_sections('geo_social_admin');
    ?>
    <?php submit_button(); ?>
  </form> 
<?php 
    }

public function API_fields_section()
{
    echo '<p> Some test text to see what\'s going on </p>';
}

public function social_platforms_section()
{

    echo '<p> Some additional test text, you know for good measure</p>';

}


public function api_key_field()
{
    global $wpdb;   
    $options_api = $wpdb->get_row("SELECT * FROM $this->table_name_api  WHERE ID = 1", ARRAY_A);
    echo '<input id="input_api_key" name="geo_social_admin[api_key]" size="40" type="text" value="' . $options_api['api_key'] . '"/>';
}

public function facebook_field()
{
    global $wpdb;
    $options_social = $wpdb->get_row("SELECT * FROM $this->table_name_social WHERE ID = 1", ARRAY_A);
    echo '<input id="input_facebook_url" name="geo_social_admin[facebook_url]" size="40" type="text" value="' . $options_social['source'] . '" />';
}

}
