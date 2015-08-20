<?php

class Geo_Social_Admin {

    protected $table_api = 'geo_social_admin_api';
    protected $table_social = 'geo_social_admin_social';

    /*
       ///////////////////////////
       ///  UTILITY FUNCTIONS ///
       //////////////////////////
     */

    /*
       ///////////////
       /// PRIVATE //
       //////////////
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

    /*
       ///////////////
       /// PUBLIC //
       //////////////
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
            api_name text NOT NULL,
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
            social_api_key mediumtext NOT NULL,
            social_api_secret mediumtext NOT NULL,
            social_api_name text NOT NULL,
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

        $default_check_api = $wpdb->get_row("SELECT * FROM $table_name_api WHERE id=1");
        $default_check_social = $wpdb->get_row("SELECT * FROM $table_name_social WHERE id=1");

        if (!$default_check_api) {
            $wpdb->insert(
                $table_name_api,
                array(
                    'time' => current_time('mysql'),
                    'api_name' => 'A unique name for the API info',
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
                    'social_url' => 'The api reference URL or RSS feed',
                    'social_api_key' => 'The associated API key',
                    'social_api_secret' => 'The associated API secret',
                    'social_api_name' => 'The associated API name'
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

        add_settings_field(
            'add_api', // Attribute to appear in id tags
        'Add an API or Social Stream', // Title of the field
        array($this, 'add_api_field'), // Function to display the field
        'geo_social_admin', // The name/slug of the plugin
        'api_fields' // Which section to appear under
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
            <input id="geo-social-admin-submit" class="bump-top add-api-button" type="submit" action="" value="Save Changes"/>
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
    // echo '<section><div class="add-api-button" onclick="addApiField()">Add an API</div><div class="api-box"></div></section>';
}

public function social_platforms_section()
{
    // echo '<section><div class="add-api-button" onclick="addSocialField()">Add a Social Feed</div><div class="social-box"></div></section>';
}

/*
   /////////////////////
   ////// FIELDS //////
   ///////////////////
 */

public function  add_api_field()
{
    global $wpdb;
    $api = $this->get_api_fields();
    echo '<section>';
        echo '<div class="api-item" id="geo_social_admin_entry">';
            echo '<label class="geo-social-admin-label" for="input_social_title">Title (eg: Converse Tumblr)</label>';
            echo '<input id="input_social_title" name="admin_social[social_title]" size="40" type="text" value=""/>';
            echo '<label class="geo-social-admin-label" for="input_social_url">URL</label>';
            echo '<input id="input_social_url" name="admin_social[social_url]" size="40" type="text" value=""/>';
            echo '<label class="geo-social-admin-label" for="input_social_source">Social Source</label>';
            echo '<input id="input_social_source" name="admin_social[social_source]" size="40" type="text" value=""/>';
            echo '<label class="geo-social-admin-label" for="input_add_api"><span class="bold">API</span></label>';
            echo '<select class="styled-select" name="admin_social[api]" id="add-api-select">';
                echo '<option value="init" selected>- Select or Add an API - </option>';
                echo '<option value="add_an_api">Add New</option>';
                echo '<option value="null"></option>';
                if ($api) {
                    foreach ($api as $i ) {
                        echo '<option value="' . $i['id'] . '">' . $i['api_name'] . '</option>';
                    };
                }
                echo '</select>';
        echo '</div>';
    echo '</section>';
}

public function api_list()
{
    global $wpdb;
    $api = $this->get_api_fields();
    $table_name_api = $this->get_api_table();
    echo '<div class="api-results-box">';
    echo '<h3 class="geo-admin-section-header">APIs</h3>';
        echo '<div id="api-list">';
        foreach($api as $i) {
            $key = substr($i['api_key'], -4);
            $secret = substr($i['api_secret'], -4);
            echo '<div class="geo-admin-section-body">';
            echo '<p class="api-names"><span>Name:</span> ' . $i['api_name'] . '</p>';
            echo '<p><span>Key:</span> *** ' . $key . '</p>';
            echo '<p><span>Secret:</span> *** ' . $secret . '</p>';
            echo '<input type="hidden" data-model="api" data-index="' . $i['id'] . '"/>';
            echo '<a href="#" class="apiEdit">Edit</a> | <a class="apiDelete" href="#">Delete</a>';
            echo '</div>';
        }
        echo '</div>';
    echo '</div>';

    $social_feed = $this->get_social_fields();
    echo '<div id="social-list" class="api-results-box">';
    echo '<h3 class="geo-admin-section-header">Social Feeds</h3>';
    foreach($social_feed as $j) {
        echo '<div class="geo-admin-section-body">';
        echo '<p><span>Name:</span> ' . $j['social_title'] . '</p>';
        echo '<p><span>URL:</span> ' . $j['social_url'] . '</p>';
        echo '<p><span>Source:</span> ' . $j['social_source'] . '</p>';
        echo '<p><span>Associated API: </span> ' . $j['social_api_name'] . '</p>';
        echo '<input type="hidden" data-model="social" data-index="' . $j['id'] . '"/>';
        echo '<a href="#" class="apiEdit">Edit</a> | <a href="#" class="apiDelete">Delete</a>';
        echo '</div>';
    }
    echo '</div>';
    echo '</div>';
}


}
