<?php

class Geo_Social_Admin {

    protected $table_api = 'geo_social_admin_api';
    protected $table_social = 'geo_social_admin_social';

    /*
       ///////////////////////////
       ///  UTILITY FUNCTIONS ///
       //////////////////////////
     */

    public function get_api_table() {
	global $wpdb;
	$table = $wpdb->prefix . $this->table_api; 
	return $table;
    }

    public function get_social_table() {
	global $wpdb;
	$table = $wpdb->prefix . $this->table_social;
	return $table;
    }

    public function get_geo_id_num() {
	global $wpdb;
	return explode('_',$wpdb->prefix)[1];
    }

    public function get_base_wp_prefix() {
	global $wpdb;
	$hold = explode('_', $wpdb->prefix)[0];
	return $hold . '_';
    }

    public function get_geo_tag() {
	global $wpdb;
	$geo = $this->get_geo_id_num();
	if (!$geo) {
	    $geo = 1;
	}
	$prefix = $this->get_base_wp_prefix();

	$hold = $wpdb->get_row('SELECT path FROM ' . $prefix . 'blogs WHERE blog_id = ' . $geo);
	$path = $hold->path;

	$fixedPath = explode('/', $path)[1];

	return $fixedPath;
    }

    /*
       /////////////////////
       /// CREATE TABLE ///
       ////////////////////
     */

    public function tableCreate()
    {
	$path = get_home_path();

	global $wpdb;

	require_once( $path . '/wp-admin/includes/upgrade.php');

	$table_name_api = $this->get_api_table();
	$table_name_social = $this->get_social_table();
	
	$charset_collate = $wpdb->get_charset_collate();

	$api = "CREATE TABLE $table_name_api (
id mediumint(9) NOT NULL AUTO_INCREMENT,
time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
api_source text NOT NULL,
api_key mediumtext NOT NULL,
api_secret mediumtext NOT NULL,
UNIQUE KEY id (id)
) $charset_collate;";

	$social = "CREATE TABLE $table_name_social (
id mediumint(9) NOT NULL AUTO_INCREMENT,
time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
social_source text NOT NULL,
social_content_type text NOT NULL,
social_title text NOT NULL,
social_geo text NOT NULL,
social_url longtext NOT NULL,
UNIQUE KEY id (id)
) $charset_collate;";

	dbDelta( $api );
	dbDelta( $social );
    }

    /*
       ///////////////////////////
       /// PRE-POPULATE TABLE ///
       //////////////////////////
     */

    public function dbInsert()
    {
	global $wpdb;

	$table_name_api = $this->get_api_table();
	$table_name_social = $this->get_social_table();


	$default_check_social = $wpdb->get_row("SELECT * FROM $table_name_social WHERE id=1");

	if (!$default_check_api) {
	    $wpdb->insert(
		$table_name_api,
		    array(
			'time' => current_time('mysql'),
			    'api_source' => 'Which social media source you\'re using',
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
			    'social_source' => 'Which social media source you\'re using',
			    'social_content_type' => 'application/json',
			    'social_title' => 'The title that will appear on blog posts',
			    'social_geo' => 'general_test',
			    'social_url' => 'The api reference URL or RSS feed'
		    )


	    );
	};
    }

    /*
       //////////////////////////////
       /// LOAD CUSTOM CSS & JS ///
       ////////////////////////////
     */

    public function adminHead()
    {
	$siteurl = get_option('siteurl');
	$css_url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) .  '/css/main.css';

	echo '<link rel="stylesheet" type="text/css" href="' . $css_url . '" />';
    }

    /*
       ////////////////////////////////
       /// REGISTER ADMIN OPTIONS ///
       ///////////////////////////////
     */

    public function adminOptions()
    {

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


	// API Settings Section

        add_settings_section(
            'api_fields', // Attribute to appear in id tags
		'API Keys & Secrets', // Name of the section
		array($this, 'API_fields_section'), // Function that outputs stuff to the page
		'geo_social_admin' // Which page to attach to, should be slug of add_options_page
        );


	// SOCIAL Settings Section

	add_settings_section(
	    'social_fields',
		'Social Platforms',
		array($this, 'social_platforms_section'),
		'geo_social_admin'
	);

	// Current Settings
	add_settings_section(
	    'current_settings',
		'Currently registered APIs and Social Feeds',
		array($this, 'api_list'),
		'geo_social_admin'
	);

    }

    /*
       /////////////////////////////////
       ////// RENDER ADMIN PAGE //////
       ///////////////////////////////
     */

    public function render_social_options()
    {
?>
  <form method="post">
    <?php settings_fields('geo_social_admin'); ?>
    <?php
    do_settings_sections('geo_social_admin');
    ?>
    <input class="bump-top" type="submit" action="" value="Save Changes"/>
  </form>
<?php
    }

/*
   ///////////////////////
   ////// SECTIONS //////
   //////////////////////
 */

public function API_fields_section()
{
    echo '<section><div class="add-api-button" onclick="addApiField()">Add an API</div><div class="api-box"></div></section>';
}

public function social_platforms_section()
{
    echo '<section><div class="add-api-button" onclick="addSocialField()">Add a Social Feed</div><div class="social-box"></div></section>';
}

public function api_list()
{
    $api = $this->get_api_fields();
    echo '<div class="api-results">';
    echo '<div class="api-results-box">';
    echo '<h3 class="geo-admin-section-header">APIs</h3>';
    foreach($api as $i) {
	$key = substr($i['api_key'], -4);
	$secret = substr($i['api_secret'], -4);
	echo '<div class="geo-admin-section-body">';
	echo '<p><span>Source:</span> ' . $i['api_source'] . '</p>';
	echo '<p><span>Key:</span> *** ' . $key . '</p>';
	echo '<p><span>Secret:</span> *** ' . $secret . '</p>';
	echo '<input type="hidden" data-model="api" data-index="' . $i['id'] . '"/>';
	echo '<a href="#" class="apiEdit">Edit</a> | <a class="apiDelete" href="#">Delete</a>';
	echo '</div>';
    }
    echo '</div>';

    $social_feed = $this->get_social_fields();
    echo '<div class="api-results-box">';
    echo '<h3 class="geo-admin-section-header">Social Feeds</h3>';
    foreach($social_feed as $i) {
	echo '<div class="geo-admin-section-body">';
	echo '<p><span>Source:</span> ' . $i['social_source'] . '</p>';
	echo '<p><span>URL:</span> ' . $i['social_url'] . '</p>';
	echo '<p><span>Name:</span> ' . $i['social_title'] . '</p>';
	echo '<input type="hidden" data-model="social" data-index="' . $i['id'] . '"/>';
	echo '<a href="#" class="apiEdit">Edit</a> | <a href="#" class="apiDelete">Delete</a>';
	echo '</div>';
    }
    echo '</div>';
    echo '</div>';
}

/*
   //////////////////////////
   /// PRIVATE FUNCTIONS ///
   /////////////////////////
 */

private function get_api_fields()
{
    global $wpdb;
    $table_name_api = $this->get_api_table();
    return $wpdb->get_results("SELECT * FROM $table_name_api WHERE ID > 1", ARRAY_A);
}

private function get_social_fields()
{
    global $wpdb;
    $table_name_social = $this->get_social_table();
    return $wpdb->get_results("SELECT * FROM $table_name_social WHERE ID > 1", ARRAY_A);
}

}
