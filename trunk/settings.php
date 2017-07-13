<?php
if(!class_exists('Dentistfind_Profile_Review_Settings'))
{
	class Dentistfind_Profile_Review_Settings
	{
		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{
			// register actions
            add_action('admin_init', array(&$this, 'admin_init'));
        	add_action('admin_menu', array(&$this, 'add_menu'));
		} // END public function __construct
		
        /**
         * hook into WP's admin_init action hook
         */
        public function admin_init()
        {
        	// register your plugin's settings
        	register_setting('dentistfind_profile_review-group', 'dpr_api_key');
        	register_setting('dentistfind_profile_review-group', 'dpr_profile_permalink');
            register_setting('dentistfind_profile_review-group', 'dpr_minimum_rating');
            register_setting('dentistfind_profile_review-group', 'dpr_number_review');

        	// add your settings section
        	add_settings_section(
        	    'dentistfind_profile_review-section', 
        	    'DentistFind Profile Review Settings', 
        	    array(&$this, 'settings_section_dentistfind_profile_review'), 
        	    'dentistfind_profile_review'
        	);
        	
        	// add your setting's fields
            add_settings_field(
                'dentistfind_profile_review-dpr_api_key', 
                'API Key', 
                array(&$this, 'settings_field_input_text'), 
                'dentistfind_profile_review', 
                'dentistfind_profile_review-section',
                array(
                    'field' => 'dpr_api_key'
                )
            );
            add_settings_field(
                'dentistfind_profile_review_dpr_profile_permalink', 
                'Profile Permalink', 
                array(&$this, 'settings_field_input_text'), 
                'dentistfind_profile_review', 
                'dentistfind_profile_review-section',
                array(
                    'field' => 'dpr_profile_permalink'
                )
            );

            add_settings_field(
                'dentistfind_profile_review_dpr_minimum_rating', 
                'Minimum Rating Score', 
                array(&$this, 'settings_field_input_text'), 
                'dentistfind_profile_review', 
                'dentistfind_profile_review-section',
                array(
                    'field' => 'dpr_minimum_rating'
                )
            );

            add_settings_field(
                'dentistfind_profile_review_dpr_number_review', 
                'Show Number of Reviews:', 
                array(&$this, 'settings_field_input_text'), 
                'dentistfind_profile_review', 
                'dentistfind_profile_review-section',
                array(
                    'field' => 'dpr_number_review'
                )
            );
            // Possibly do additional admin_init tasks
        } // END public static function activate
        
        public function settings_section_dentistfind_profile_review()
        {
            // Think of this as help text for the section.
            echo 'Please set the API key of the profile in dentistfind.com for which you are trying to show reviews.';
            echo '<br><hr>';
            echo 'Place this short code to show reviews: <b>[dentistfind-profile-review]</b>';

            echo '<br><hr><br>';
            echo '<b>Notice:</b> It may take up to 10 mins for changes to take effect';
        }
        
        /**
         * This function provides text inputs for settings fields
         */
        public function settings_field_input_text($args)
        {
            // Get the field name from the $args array
            $field = $args['field'];
            // Get the value of this setting
            $value = get_option($field);
            // echo a proper input type="text"
            echo sprintf('<input type="text" name="%s" id="%s" value="%s" />', $field, $field, $value);
        } // END public function settings_field_input_text($args)
        
        /**
         * add a menu
         */		
        public function add_menu()
        {
            // Add a page to manage this plugin's settings
        	add_options_page(
        	    'DentistFind Profile Review Settings', 
        	    'DentistFind Profile Review', 
        	    'manage_options', 
        	    'dentistfind_profile_review', 
        	    array(&$this, 'plugin_settings_page')
        	);
        } // END public function add_menu()
    
        /**
         * Menu Callback
         */		
        public function plugin_settings_page()
        {
        	if(!current_user_can('manage_options'))
        	{
        		wp_die(__('You do not have sufficient permissions to access this page.'));
        	}
	
        	// Render the settings template
        	include(sprintf("%s/templates/settings.php", dirname(__FILE__)));
        } // END public function plugin_settings_page()
    } // END class Dentistfind_Profile_Review_Settings
} // END if(!class_exists('Dentistfind_Profile_Review_Settings'))
