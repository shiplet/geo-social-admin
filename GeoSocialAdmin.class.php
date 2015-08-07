<?php

class Geo_Social_Admin {

    protected $table_name_api = 'geo_social_admin_api';
    protected $table_name_social = 'geo_social_admin_social';

    /*
       /////////////////////
       /// CREATE TABLE ///
       ////////////////////
     */
    
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

	error_log('tableCreate finished running.');
    }

    /*
       ///////////////////////////
       /// PRE-POPULATE TABLE ///
       //////////////////////////
     */

    public function dbInsert()
    {
	error_log('dbInsert is running');
	global $wpdb;

	$default_check_api = $wpdb->get_row("SELECT * FROM $this->table_name_api WHERE id=1");
	$default_check_social = $wpdb->get_row("SELECT * FROM $this->table_name_social WHERE id=1");

	if (!$default_check_api) {
	    $wpdb->insert(
		$this->table_name_api,
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
		$this->table_name_social,
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
	
	error_log('dbInsert finished');
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
	

	// API Fields

        add_settings_section(
            'api_fields', // Attribute to appear in id tags
		'API Keys & Secrets', // Name of the section
		array($this, 'API_fields_section'), // Function that outputs stuff to the page
		'geo_social_admin' // Which page to attach to, should be slug of add_options_page
        );

	/* add_settings_field(
	   'api_source',
	   'API Source',
	   array($this, 'api_source_field'),
	   'geo_social_admin',
	   'api_fields'
	   );

	   add_settings_field(
	   'api_key',
	   'API Key',
	   array($this, 'api_key_field'),
	   'geo_social_admin',
	   'api_fields'
	   );

	   add_settings_field(
	   'api_secret',
	   'API Secret',
	   array($this, 'api_secret_field'),
	   'geo_social_admin',
	   'api_fields'
	   ); */
	

	// SOCIAL fields

	add_settings_section(
	    'social_fields',
		'Social Platforms',
		array($this, 'social_platforms_section'),
		'geo_social_admin'
	);

	add_settings_field(
	    'social_source',
		'Social Source',
		array($this, 'social_source_field'),
		'geo_social_admin',
		'social_fields'
	);

	add_settings_field(
	    'social_content_type',
		'',
		array($this, 'social_content_type_field'),
		'geo_social_admin',
		'social_fields'
	);

	add_settings_field(
	    'social_title',
		'Title',
		array($this, 'social_title_field'),
		'geo_social_admin',
		'social_fields'
	);

	add_settings_field(
	    'social_geo',
		'Geo Tag',
		array($this, 'social_geo_field'),
		'geo_social_admin',
		'social_fields'
	);

	add_settings_field(
	    'social_url',
		'URL',
		array($this, 'social_url_field'),
		'geo_social_admin',
		'social_fields'
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
    <input type="submit" action="" value="Save Changes"/>
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
    echo '<p> Some test text to see what\'s going on </p>';
    echo '
<section>
<div class="api-box">
</div>
<div class="add-api-button" onclick="addApiField()">Add API</div>
</section>
';
}

public function social_platforms_section()
{
    echo '<p> Some additional test text, you know for good measure</p>';
}

/*
   ////////////////////
   ////// FIELDS //////
   ///////////////////
 */


/*////// API ////////*/

public function api_source_field()
{
    $options_api = $this->get_api_fields();
    echo '<input id="input_api_source" name="api_source" size="40" type="text" value=""/>';
}

public function api_key_field()
{
    $options_api = $this->get_api_fields();
    echo '<input id="input_api_key" name="api_key" size="80" type="text" value=""/>';
}

public function api_secret_field()
{
    $options_api = $this->get_api_fields();
    echo '<input id="input_api_secret" name="api_secret" size="80" type="text" value=""/>';
}


/*////// SOCIAL ////////*/

public function social_source_field()
{
    $options_social = $this->get_social_fields();
    echo '<input id="input_facebook_url" name="social_source" size="40" type="text" value="" />';
}

public function social_content_type_field()
{
    $options_social = $this->get_social_fields();
    echo '<input type="hidden" id="input_content_type" name="social_content_type" size="40" value=""/>';
}

public function social_title_field()
{
    $options_social = $this->get_social_fields();
    echo '<input type="text" id="input_social_title" name="social_title" size="40" value=""/>';
}

public function social_geo_field()
{
    $options_social = $this->get_social_fields();
    echo '<input type="text" id="input_social_geo" name="social_geo" size="40" value=""/>';
}

public function social_url_field()
{
    $options_social = $this->get_social_fields();
    echo '<input type="text" id="input_social_url" name="social_url" size="80" value=""/>';
}

/*
   //////////////////////////
   /// PRIVATE FUNCTIONS ///
   /////////////////////////
 */

private function get_api_fields()
{
    global $wpdb;
    return $wpdb->get_row("SELECT * FROM $this->table_name_api WHERE ID = 1", ARRAY_A);
}

private function get_social_fields()
{
    global $wpdb;
    return $wpdb->get_row("SELECT * FROM $this->table_name_social WHERE ID = 1", ARRAY_A);
}

}

