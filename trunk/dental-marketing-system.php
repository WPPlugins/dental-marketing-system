<?php
/*
Plugin Name: Dental Marketing System
Plugin URI: http://dentistfind.com
Description: A plugin to show reviews and submit reviews to a cliinc profile in dentistfind.com
Version: 0.0.1
Author: DentistFind.com
Author URI: http://dentistfind.com
License: GPL2
*/
/*
Copyright 2014  Sharky Liu  (email : jing@jingco.ca)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class DentalMarketingSystem {

  public function __construct(){
    include_once(plugin_dir_path( __FILE__ ) .'/dentistfind_profile_review.php');
    include_once(plugin_dir_path( __FILE__ ) .'/patient_lead.php');
  }

  /**
   * Activate the plugin
   */
  public static function activate()
  {
    // Do nothing
  } // END public static function activate

  /**
   * Deactivate the plugin
   */
  public static function deactivate()
  {
    // Do nothing
  } // END public static function deactivate
}

if(class_exists('DentalMarketingSystem'))
{
  register_activation_hook(__FILE__, array('DentalMarketingSystem', 'activate'));
  register_deactivation_hook(__FILE__, array('DentalMarketingSystem', 'deactivate'));

  $dental_marketting_system = new DentalMarketingSystem();

}
