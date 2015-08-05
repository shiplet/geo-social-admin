<?php

class Geo_Social_Admin {

    public function __construct()
    {

    }

    public function success()
    {
        echo 'success';
    }

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
    $options = get_option('api_key');
    echo '<input id="input_api_key" name="geo_social_admin[api_key]" size="40" type="text" value="' . $options['api_key'] . '"/>';
}

public function facebook_field()
{
    $options = get_option('facebook_url');
    echo '<input id="input_facebook_url" name="geo_social_admin[facebook_url]" size="40" type="text" value="' . $options['facebook_url'] . '" />';
}

}
