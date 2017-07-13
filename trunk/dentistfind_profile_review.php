<?php
if(!class_exists('DentalMarketingSystemProfileReview'))
{
	class DentalMarketingSystemProfileReview
	{
		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{
			// Initialize Settings

			$this->assets();
			require_once(sprintf("%s/settings.php", dirname(__FILE__)));
			$Dentistfind_Profile_Review_Settings = new Dentistfind_Profile_Review_Settings();

			//Register ShortCodes
			require_once(sprintf("%s/short_codes.php", dirname(__FILE__)));
			$Dentistfind_Profile_Review_Shortcodes = new Dentistfind_Profile_Review_Shortcodes();

			$plugin = plugin_basename(__FILE__);
			add_filter("plugin_action_links_$plugin", array( $this, 'plugin_settings_link' ));


		} // END public function __construct


		public function assets() {

      wp_register_style('jquery-ui-css', plugins_url('css/jquery-ui.css', __FILE__), false, true);
      wp_register_style('jquery-validation-engine-css', plugins_url('css/validationEngine.jquery.css', __FILE__), false, true);
      wp_register_style('jquery-validation-engine-template-css', plugins_url('css/template.css', __FILE__), false, true);
      wp_register_style('dpr-reviews-css', plugins_url('css/reviews.css', __FILE__), false, true);
      wp_register_style('dpr-reviews-rateit-css', plugins_url('css/rateit.css', __FILE__), false, true);

      wp_register_script('jquery-validation-engine', plugins_url('js/jquery.validationEngine.js', __FILE__), array('jquery'), '1.0', true);
      wp_register_script('jquery-validation-engine-en', plugins_url('js/jquery.validationEngine-en.js', __FILE__), array('jquery'), '1.0', true);
      wp_register_script('dpr-reviews-rateit', plugins_url('js/jquery.rateit.min.js', __FILE__), array('jquery'), '1.0', true);
      wp_register_script('dpr-plugin-script', plugins_url('js/plugin_script.js', __FILE__), array('jquery','jquery-ui-core','jquery-ui-dialog'), '1.0', true);
      
		}
		// Add the settings link to the plugins page
		function plugin_settings_link($links)
		{
			$settings_link = '<a href="options-general.php?page=dentistfind_profile_review">Settings</a>';
			array_unshift($links, $settings_link);
			return $links;
		}


	} // END class DentalMarketingSystemProfileReview
} // END if(!class_exists('DentalMarketingSystemProfileReview'))

if(class_exists('DentalMarketingSystemProfileReview'))
{
	// instantiate the plugin class
	$dental_marketing_system_profile_review = new DentalMarketingSystemProfileReview();

}
