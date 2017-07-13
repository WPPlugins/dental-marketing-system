<?php
if (class_exists("GFForms")) {
    GFForms::include_addon_framework();

    class GFSimpleAddOn extends GFAddOn {

        protected $_version = "1.1";
        protected $_min_gravityforms_version = "1.7.9999";
        protected $_slug = "dentistfind-patient-lead";
        protected $_path = "gravity-forms-dentistfind-patient_lead/patient_lead.php";
        protected $_full_path = __FILE__;
        protected $_title = "Gravity Forms DentistFind Patient Lead";
        protected $_short_title = "DentistFind Patient Lead";

        public function init(){
            parent::init();
            //add_filter("gform_submit_button", array($this, "form_submit_button"), 10, 2);
            add_action("gform_field_css_class", array(&$this,'custom_css_class'), 10, 3);
            add_action('gform_field_input', array(&$this,'patient_lead_field_input'), 10, 5);
            add_filter("gform_field_type_title", array(&$this,'assign_title'), 10, 2);
            add_action('gform_after_submission', array(&$this,'post_lead_to_df'), 10, 2);
        }

        public function init_admin(){
            parent::init_admin();
            add_filter("gform_add_field_buttons", array(&$this,"add_field_buttons"));
            add_action('gform_editor_js_set_default_values', array(&$this,'dentistfind_patient_lead_add_default_values'));
            add_filter("gform_entry_field_value", array(&$this,'gravity_form_custom_field_entry_output'), 10, 4);
            add_action('gform_editor_js', array(&$this,'patient_lead_gform_editor_js'));
            
            
        }

        public function init_frontend(){
            parent::init_frontend();
            //add_action('gform_field_input', array(&$this,'patient_lead_field_input'), 10, 5);
            // add tasks or filters here that you want to perform only in the front end
        }

        public function init_ajax(){
            parent::init_ajax();
            // add tasks or filters here that you want to perform only during ajax requests
        }

        // Add the text in the plugin settings to the bottom of the form if enabled for this form
        function form_submit_button($button, $form){
            $settings = $this->get_form_settings($form);
            if(isset($settings["enabled"]) && true == $settings["enabled"]){
                $text = $this->get_plugin_setting("mytextbox");
                $button = "<div>{$text}</div>" . $button;
            }
            return $button;
        }


        /*public function plugin_page() {
            ?>
            This page appears in the Forms menu
        <?php
        }*/

        public function form_settings_fields($form) {
            return array(
                array(
                    "title"  => "Dentist Find Patient Lead API Settings",
                    "fields" => array(
                        array(
                            "label"   => "API Key",
                            "type"    => "text",
                            "name"    => "df_profile_api_key",
                            "tooltip" => "Put the API key from DentistFind Clinic profile",
                        ),
                        array(
                            "label"   => "Profile Permalink",
                            "type"    => "text",
                            "name"    => "df_profile_permalink",
                            "tooltip" => "Put the permalink for your profile. For example: PERMALINK for the url dentistfind.com/dentistry/PERMALINK",
                        ),
                    )
                )
            );
        }

        public function plugin_settings_fields() {
            return array(
                array(
                    "title"  => "Dentist Find Patient Lead API Settings",
                    "fields" => array(
                        array(
                            "name"    => "df_profile_api_key",
                            "tooltip" => "Put the API key from DentistFind Clinic profile",
                            "label"   => "API Key",
                            "type"    => "text",
                            /*"class"   => "small",
                            "feedback_callback" => array($this, "is_valid_setting")*/
                        ),
                        array(
                            "name"    => "df_profile_permalink",
                            "tooltip" => "Put the permalink for your profile. For example: PERMALINK for the url dentistfind.com/dentistry/PERMALINK",
                            "label"   => "Profile Permalink",
                            "type"    => "text",
                            /*"class"   => "small",
                            "feedback_callback" => array($this, "is_valid_setting")*/
                        )
                    )
                )
            );
        }

        public function is_valid_setting($value){
            return strlen($value) < 10;
        }

        public function scripts() {
            $scripts = array(
                array("handle"  => "my_script_js",
                      "src"     => $this->get_base_url() . "/js/my_script.js",
                      "version" => $this->_version,
                      "deps"    => array("jquery"),
                      "strings" => array(
                          'first'  => __("First Choice", "simpleaddon"),
                          'second' => __("Second Choice", "simpleaddon"),
                          'third'  => __("Third Choice", "simpleaddon")
                      ),
                      "enqueue" => array(
                          array(
                              "admin_page" => array("form_settings"),
                              "tab"        => "simpleaddon"
                          )
                      )
                ),

            );

            return array_merge(parent::scripts(), $scripts);
        }

        public function styles() {

            $styles = array(
                array("handle"  => "my_styles_css",
                      "src"     => $this->get_base_url() . "/css/my_styles.css",
                      "version" => $this->_version,
                      "enqueue" => array(
                          array("field_types" => array("poll"))
                      )
                )
            );

            return array_merge(parent::styles(), $styles);
        }

        public function get_results_page_config() {
            return array(
                "title"        => "Poll Results",
                "capabilities" => array("gravityforms_polls_results"),
                "callbacks"    => array(
                    "fields" => array($this, "results_fields")
                )
            );
        }

        public function results_fields($form) {
            return GFCommon::get_fields_by_type($form, array("poll"));
        }

        function add_field_buttons($field_groups){
            $dentistfind_patient_lead_fields = array(
                'name' => 'dentistfind_patient_lead_fields',
                'label' => 'Patient Lead Fields',
                'fields' => array(
                    array(
                        'class' => 'button',
                        'value' => esc_attr__('Patient Name', 'gravity-forms-addons'),
                        'onclick' => "StartAddField('df_patient_lead_name');"
                    ),
                    array(
                        'class' => 'button',
                        'value' => esc_attr__('Patient Email', 'gravity-forms-addons'),
                        'onclick' => "StartAddField('df_patient_lead_email');"
                    ),
                    array(
                        'class' => 'button',
                        'value' => esc_attr__('Patient phone', 'gravity-forms-addons'),
                        'onclick' => "StartAddField('df_patient_lead_phone');"
                    ),
                   /* array(
                        'class' => 'button',
                        'value' => esc_attr__('Patient info', 'gravity-forms-addons'),
                        'onclick' => "StartAddField('df_patient_lead');"
                    ),*/
                    array(
                        'class' => 'button',
                        'value' => esc_attr__('Patient Message', 'gravity-forms-addons'),
                        'onclick' => "StartAddField('df_patient_message');"
                    ),
                )
            );
            array_push($field_groups, $dentistfind_patient_lead_fields);

            return $field_groups;
        }

        public function assign_title($title, $field_type) {
          if($field_type == "df_patient_lead_name") {
            return "Patient Name";
          }

          if($field_type == "df_patient_lead") {
            return "Patient Lead";
          }

          if($field_type == "df_patient_message") {
            return "Message:";
          }
          if($field_type == "df_patient_lead_email") {
            return "Email:";
          }
          if($field_type == "df_patient_lead_phone") {
            return "Phone:";
          }

        }

        public function patient_lead_field_input($input, $field, $value, $lead_id, $form_id) {
          
          $id = $field["id"];
          //echo $field["type"];
          $field_id = IS_ADMIN || $form_id == 0 ? "input_$id" : "input_" . $form_id . "_$id";
          $form_id = IS_ADMIN && empty($form_id) ? rgget("id") : $form_id;

          $size = rgar($field, "size");
          $disabled_text = (IS_ADMIN && RG_CURRENT_VIEW != "entry") ? "disabled='disabled'" : "";
          $class_suffix = RG_CURRENT_VIEW == "entry" ? "_admin" : "";
          $class = $size . $class_suffix;
          //print_r($field);

          //$form = GFAPI::get_form($form_id);
          //print_r($form);
          //$settings = $this->get_form_settings($form);
          //$settings = $this->get_plugin_settings();
          //print_r($settings);

          if($field["type"] == 'df_patient_message') {
            $max_chars = "";
            //$logic_event = GFCommon::get_logic_event($field, "keyup");

            $tabindex = GFCommon::get_tabindex();
            //return sprintf("<div class='ginput_container'><textarea name='input_%d' id='%s' class='textarea %s' {$tabindex} {$logic_event} %s rows='10' cols='50'>%s</textarea></div>{$max_chars}", $id, $field_id, esc_attr($class), $disabled_text, esc_html($value));
            return sprintf("<div class='ginput_container'><textarea name='input_%d' id='%s' class='textarea %s' {$tabindex} %s rows='10' cols='50'>%s</textarea></div>{$max_chars}", $id, $field_id, esc_attr($class), $disabled_text, esc_html($value));
          }


          if($field["type"] == 'df_patient_lead_email') {
            $max_chars = "";
            //$logic_event = GFCommon::get_logic_event($field, "keyup");

            $tabindex = GFCommon::get_tabindex();
            //$html_input_type = RGFormsModel::is_html5_enabled() ? "email" : "text";
            $html_input_type = 'text';
            return sprintf("<div class='ginput_container'><input name='input_%d' id='%s' type='%s' value='%s' class='%s' {$max_length} {$tabindex} {$html5_attributes} {$logic_event} %s/></div>", $id, $field_id, $html_input_type, esc_attr($value), esc_attr($class), $disabled_text);
          }


          if($field["type"] == 'df_patient_lead_phone') {
            $max_chars = "";
            //$logic_event = GFCommon::get_logic_event($field, "keyup");

            $tabindex = GFCommon::get_tabindex();
            //$instruction = $field["phoneFormat"] == "standard" ? __("Phone format:", "gravityforms") . " (###)###-####" : "";
            //$instruction_div = rgget("failed_validation", $field) && !empty($instruction) ? "<div class='instruction validation_message'>$instruction</div>" : "";
            $instruction_div = '';
            //$html_input_type = RGFormsModel::is_html5_enabled() ? "tel" : "text";
            $html_input_type = 'text';
            return sprintf("<div class='ginput_container'><input name='input_%d' id='%s' type='{$html_input_type}' value='%s' class='%s' {$tabindex} {$logic_event} %s/>{$instruction_div}</div>", $id, $field_id, esc_attr($value), esc_attr($class), $disabled_text);
          }

          if($field["type"] == 'df_patient_lead') {

            $street_value ="";
            $street2_value ="";
            $city_value ="";
            $state_value ="";
            $zip_value ="";
            $country_value ="";
            $patient_first_name = "";
            $patient_last_name = "";
            $patient_email = "";
            $patient_phone = "";

            if(is_array($value)){
                $street_value = esc_attr(rgget($field["id"] . ".1",$value));
                $street2_value = esc_attr(rgget($field["id"] . ".2",$value));
                $city_value = esc_attr(rgget($field["id"] . ".3",$value));
                $state_value = esc_attr(rgget($field["id"] . ".4",$value));
                $zip_value = esc_attr(rgget($field["id"] . ".5",$value));
                $country_value = esc_attr(rgget($field["id"] . ".6",$value));
                $patient_first_name = esc_attr(rgget($field["id"] . ".7",$value));
                $patient_last_name = esc_attr(rgget($field["id"] . ".8",$value));
                $patient_email = esc_attr(rgget($field["id"] . ".9",$value));
                $patient_phone = esc_attr(rgget($field["id"] . ".11",$value));
            }


            $state_label = __("Province", "gravityforms");
            $zip_label = __("Postal Code", "gravityforms");
            $hide_country = false;

            if(empty($country_value))
                $country_value = rgget("defaultCountry", $field);

            if(empty($state_value))
                $state_value = rgget("defaultState", $field);

            $country_list = GFCommon::get_country_dropdown($country_value);

            //changing css classes based on field format to ensure proper display
            $address_display_format = apply_filters("gform_address_display_format", "default");
            $city_location = $address_display_format == "zip_before_city" ? "right" : "left";
            $zip_location = $address_display_format != "zip_before_city" && rgar($field,"hideState") ? "right" : "left";
            $state_location = $address_display_format == "zip_before_city" ? "left" : "right";
            $country_location = rgar($field,"hideState") ? "left" : "right";

            //First Name and Last Name
            $first_tabindex = GFCommon::get_tabindex();
            $last_tabindex = GFCommon::get_tabindex();
            $first_last_name =  sprintf("<div class='patient_info_name ginput_complex$class_suffix ginput_container' id='$field_id'><span id='" . $field_id . "_7_container' class='ginput_left'><input type='text' name='input_%d.7' id='%s_7' value='%s' $first_tabindex %s/><label for='%s_7'>" . apply_filters("gform_name_first_{$form_id}", apply_filters("gform_name_first",__("First Name", "gravityforms"), $form_id), $form_id) . "</label></span><span id='" . $field_id . "_8_container' class='ginput_right'><input type='text' name='input_%d.8' id='%s_8' value='%s' $last_tabindex %s/><label for='%s_8'>" . apply_filters("gform_name_last_{$form_id}", apply_filters("gform_name_last",__("Last Name", "gravityforms"), $form_id), $form_id) . "</label></span><div class='gf_clear gf_clear_complex'></div></div>", $id, $field_id, $patient_first_name, $disabled_text, $field_id, $id, $field_id, $patient_last_name, $disabled_text, $field_id);


            //Email and Phone
            $email_tabindex = GFCommon::get_tabindex();
            $phone_tabindex = GFCommon::get_tabindex();
            $email_phone =  sprintf("<div class='ginput_complex$class_suffix ginput_container' id='$field_id'><span id='" . $field_id . "_9_container' class='ginput_left'><input type='text' name='input_%d.9' id='%s_9' value='%s' $email_tabindex %s/><label for='%s_9'>" . __("Email", "gravityforms") . "</label></span><span id='" . $field_id . "_11_container' class='ginput_right'><input type='text' name='input_%d.11' id='%s_11' value='%s' $phone_tabindex %s/><label for='%s_11'>" . __("Phone", "gravityforms") . "</label></span><div class='gf_clear gf_clear_complex'></div></div>", $id, $field_id, $patient_email, $disabled_text, $field_id, $id, $field_id, $patient_phone, $disabled_text, $field_id);

            //address field
            $tabindex = GFCommon::get_tabindex();
            $street_address = sprintf("<span class='ginput_full$class_suffix' id='" . $field_id . "_1_container'><input type='text' name='input_%d.1' id='%s_1' value='%s' $tabindex %s/><label for='%s_1' id='" . $field_id . "_1_label'>" . apply_filters("gform_address_street_{$form_id}", apply_filters("gform_address_street",__("Street Address", "gravityforms"), $form_id), $form_id) . "</label></span>", $id, $field_id, $street_value, $disabled_text, $field_id);

            //address line 2 field
            $street_address2 = "";
            $style = (IS_ADMIN && rgget("hideAddress2", $field)) ? "style='display:none;'" : "";
            if(IS_ADMIN || !rgget("hideAddress2", $field)){
                $tabindex = GFCommon::get_tabindex();
                $street_address2 = sprintf("<span class='ginput_full$class_suffix' id='" . $field_id . "_2_container' $style><input type='text' name='input_%d.2' id='%s_2' value='%s' $tabindex %s/><label for='%s_2' id='" . $field_id . "_2_label'>" . apply_filters("gform_address_street2_{$form_id}",apply_filters("gform_address_street2",__("Address Line 2", "gravityforms"), $form_id), $form_id) . "</label></span>", $id, $field_id, $street2_value, $disabled_text, $field_id);
            }


            if($address_display_format == "zip_before_city"){
                //zip field
                $tabindex = GFCommon::get_tabindex();
                $zip = sprintf("<span class='ginput_{$zip_location}$class_suffix' id='" . $field_id . "_5_container'><input type='text' name='input_%d.5' id='%s_5' value='%s' $tabindex %s/><label for='%s_5' id='" . $field_id . "_5_label'>" . apply_filters("gform_address_zip_{$form_id}", apply_filters("gform_address_zip", $zip_label, $form_id), $form_id) . "</label></span>", $id, $field_id, $zip_value, $disabled_text, $field_id);

                //city field
                $tabindex = GFCommon::get_tabindex();
                $city = sprintf("<span class='ginput_{$city_location}$class_suffix' id='" . $field_id . "_3_container'><input type='text' name='input_%d.3' id='%s_3' value='%s' $tabindex %s/><label for='%s_3' id='{$field_id}_3_label'>" . apply_filters("gform_address_city_{$form_id}", apply_filters("gform_address_city",__("City", "gravityforms"), $form_id), $form_id) . "</label></span>", $id, $field_id, $city_value, $disabled_text, $field_id);

                //state field
                $style = (IS_ADMIN && rgget("hideState", $field)) ? "style='display:none;'" : "";
                if(IS_ADMIN || !rgget("hideState", $field)){
                    $state_field = self::get_state_field($field, $id, $field_id, $state_value, $disabled_text, $form_id);
                    $state = sprintf("<span class='ginput_{$state_location}$class_suffix' id='" . $field_id . "_4_container' $style>$state_field<label for='%s_4' id='" . $field_id . "_4_label'>" . apply_filters("gform_address_state_{$form_id}", apply_filters("gform_address_state", $state_label, $form_id), $form_id) . "</label></span>", $field_id);
                }
                else{
                    $state = sprintf("<input type='hidden' class='gform_hidden' name='input_%d.4' id='%s_4' value='%s'/>", $id, $field_id, $state_value);
                }
            }
            else{


                //city field
                $tabindex = GFCommon::get_tabindex();
                $city = sprintf("<span class='ginput_{$city_location}$class_suffix' id='" . $field_id . "_3_container'><input type='text' name='input_%d.3' id='%s_3' value='%s' $tabindex %s/><label for='%s_3' id='$field_id.3_label'>" . apply_filters("gform_address_city_{$form_id}", apply_filters("gform_address_city",__("City", "gravityforms"), $form_id), $form_id) . "</label></span>", $id, $field_id, $city_value, $disabled_text, $field_id);

                //state field
                $style = (IS_ADMIN && rgget("hideState", $field)) ? "style='display:none;'" : "";

                if(IS_ADMIN || !rgget("hideState", $field)){

                    $state_field = self::get_state_field($field, $id, $field_id, $state_value, $disabled_text, $form_id);
                    $state = sprintf("<span class='ginput_{$state_location}$class_suffix' id='" . $field_id . "_4_container' $style>$state_field<label for='%s_4' id='" . $field_id . "_4_label'>" . apply_filters("gform_address_state_{$form_id}", apply_filters("gform_address_state", $state_label, $form_id), $form_id) . "</label></span>", $field_id);
                }
                else{
                    $state = sprintf("<input type='hidden' class='gform_hidden' name='input_%d.4' id='%s_4' value='%s'/>", $id, $field_id, $state_value);
                }

                //zip field
                $tabindex = GFCommon::get_tabindex();
                $zip = sprintf("<span class='ginput_{$zip_location}$class_suffix' id='" . $field_id . "_5_container'><input type='text' name='input_%d.5' id='%s_5' value='%s' $tabindex %s/><label for='%s_5' id='" . $field_id . "_5_label'>" . apply_filters("gform_address_zip_{$form_id}", apply_filters("gform_address_zip", $zip_label, $form_id), $form_id) . "</label></span>", $id, $field_id, $zip_value, $disabled_text, $field_id);

            }

            if(IS_ADMIN || !$hide_country){
                $style = $hide_country ? "style='display:none;'" : "";
                $tabindex = GFCommon::get_tabindex();
                $country = sprintf("<span class='ginput_{$country_location}$class_suffix' id='" . $field_id . "_6_container' $style><select name='input_%d.6' id='%s_6' $tabindex %s>%s</select><label for='%s_6' id='" . $field_id . "_6_label'>" . apply_filters("gform_address_country_{$form_id}", apply_filters("gform_address_country",__("Country", "gravityforms"), $form_id), $form_id) . "</label></span>", $id, $field_id, $disabled_text, $country_list, $field_id);
            }
            else{
                $country = sprintf("<input type='hidden' class='gform_hidden' name='input_%d.6' id='%s_6' value='%s'/>", $id, $field_id, $country_value);
            }

            $inputs = $first_last_name.$email_phone.($address_display_format == "zip_before_city" ? $street_address . $street_address2 . $zip . $city . $state . $country : $street_address . $street_address2 . $city . $state . $zip . $country);

            return "<div class='ginput_complex$class_suffix ginput_container' id='$field_id'>" . $inputs . "<div class='gf_clear gf_clear_complex'></div></div>";
          }


          if($field["type"] == "df_patient_lead_name"){
            $first_tabindex = GFCommon::get_tabindex();
            $last_tabindex = GFCommon::get_tabindex();

            $field_id       = $field['id'];
            $input_id       = 'df_patinet_lead_name_'.$field['id'];
            $input_name     = $form_id.'_'.$field['id'];
            $tabindex       = GFCommon::get_tabindex();
            $css            = isset($field['cssClass']) ? $field['cssClass'] : '';
            $is_multiselect = isset($field['mailpoet_multiselect']) ? $field['mailpoet_multiselect'] : '';
            $checkbox_label = isset($field['mailpoet_checkbox_label']) ? $field['mailpoet_checkbox_label'] : __('Yes, please subscribe me to your newsletter.', 'mailpoet-gravityforms-addon');

            //$field_id, $input_id, $first, $disabled_text, $input_id, $field_id, $input_id, $last, $disabled_text, $input_id

            $html = "<div class='ginput_complex$class_suffix ginput_container' id='$field_id'>";
              $html .= "<span id='" . $field_id . "_3_container' class='ginput_left'>";
                $html .= "<label for='$input_id_3'><b>" . __("First Name", "gravityforms") . "</b></label>";
                $html .= "<input type='text' name='input_$field_id.3' id='{$input_id}_3' value='{$first}' $first_tabindex $disabled_text/>";
              $html .= "</span>";
              $html .= "<span id='" . $field_id . "_6_container' class='ginput_right'>";
                $html .= "<label for='$input_id_6'><b>" . __("Last Name", "gravityforms") . "</b></label>";
                $html .= "<input type='text' name='input_$field_id.6' id='{$input_id}_6' value='{$last}' $last_tabindex $disabled_text/>";
              $html .= "</span>";
              $html .= "<div class='gf_clear gf_clear_complex'></div>";
            $html .= "</div>";
            //$html .= "";
            return $html;
          }

        }




        public function gravity_form_custom_field_entry_output($value, $field, $lead, $form) {

          if ($field["type"] == "df_patient_lead_name"){
              $value = '';
              $value .= "<b>First Name".':</b> '.$lead[$field["id"].'.3']."<br />";
              $value .= "<b>Last Name".':</b> '.$lead[$field["id"].'.6']."<br />";
          }

          if ($field["type"] == "df_patient_lead"){
              $value = '';
              $value .= "<b>First Name".':</b> '.$lead[$field["id"].'.7']."<br />";
              $value .= "<b>Last Name".':</b> '.$lead[$field["id"].'.8']."<br />";
              $value .= "<b>Email".':</b> '.$lead[$field["id"].'.9']."<br />";
              $value .= "<b>Phone".':</b> '.$lead[$field["id"].'.11']."<br />";
              $value .= "<b>Street Address".':</b> '.$lead[$field["id"].'.1']."<br />";
              $value .= "<b>Address 2".':</b> '.$lead[$field["id"].'.2']."<br />";
              $value .= "<b>Postal Code".':</b> '.$lead[$field["id"].'.5']."<br />";
              $value .= "<b>City".':</b> '.$lead[$field["id"].'.3']."<br />";
              $value .= "<b>Province".':</b> '.$lead[$field["id"].'.4']."<br />";
              $value .= "<b>Country".':</b> '.$lead[$field["id"].'.6']."<br />";
          }

          return $value;
        }

        public function post_lead_to_df($entry, $form) {
            //$df_patient_lead_form_field = self::find_field_type('df_patient_lead', $form);
            $df_patient_lead_name = self::find_field_type('df_patient_lead_name', $form);
            $df_patient_lead_email = self::find_field_type('df_patient_lead_email', $form);
            $df_patient_lead_phone = self::find_field_type('df_patient_lead_phone', $form);
            $df_patient_message = self::find_field_type('df_patient_message', $form);

            if(!$df_patient_lead_name) return;

            $patient_lead = array(
                'first_name' => trim($entry[$df_patient_lead_name['id'].'.3']), 
                'last_name' => trim($entry[$df_patient_lead_name['id'].'.6']), 
                'email' => trim($entry[$df_patient_lead_email['id']]),
                'phone' => trim($entry[$df_patient_lead_phone['id']]),
                'message' => trim($entry[$df_patient_message['id']]), 
            );

          $api_key = "";
          $profile_permalink = "";
          $dentistfind_url = "https://dentistfind.com";
          //$dentistfind_url = "http://127.0.0.1:4000";
          //$dentistfind_url = "http://staging.dentistfind.com";

          $form_settings = $this->get_form_settings($form);
          $plugin_settings = $this->get_plugin_settings();

          if(!is_null($form_settings) && is_array($form_settings)) {
            if(!empty($form_settings['df_profile_api_key']) && !empty($form_settings['df_profile_permalink'])) {
                $api_key = $form_settings['df_profile_api_key'];
                $profile_permalink = $form_settings['df_profile_permalink'];
            }
          }
          
          if((empty($api_key) || empty($profile_permalink)) && !is_null($plugin_settings) && is_array($plugin_settings)) {
            if(!empty($plugin_settings['df_profile_api_key']) && !empty($plugin_settings['df_profile_permalink'])) {
                $api_key = $plugin_settings['df_profile_api_key'];
                $profile_permalink = $plugin_settings['df_profile_permalink'];
            }
          }

          if(empty($api_key) && empty($profile_permalink)) {
            return;
          }
          
          $url = $dentistfind_url.'/api/v1/dentistry/'.$profile_permalink.'/create_patient';
          $response = wp_remote_post(
                  $url,
                  array(
                      'timeout' => 45,
                      'body' => array(
                          'patient_lead' => $patient_lead,
                          'site_url' => home_url(),
                          //'ip_address' => $this->get_the_user_ip(),
                          'api_token' => $api_key
                      ),

                      'headers' => array(
                        'API_TOKEN' => $api_key
                        )
                    )
                  );
        }

        public function dentistfind_patient_lead_add_default_values() {
            ?>
            case "df_patient_lead_name":
              field.id = parseFloat(field.id);
              field.label = false;
              field.inputs = [new Input(field.id + 0.3, '<?php echo __("First Name", "gravityforms"); ?>'), new Input(field.id + 0.6, '<?php echo __("Last Name", "gravityforms"); ?>')];

            break;

            case "df_patient_message":
              field.inputs = null;
              if(!field.label)
                field.label = "<?php _e("Message", "gravityforms"); ?>";

            break;

            case "df_patient_lead_email":
              field.inputs = null;
              if(!field.label)
                field.label = "<?php _e("Email", "gravityforms"); ?>";

            break;

            case "df_patient_lead_phone":
              field.inputs = null;
              if(!field.label)
                field.label = "<?php _e("Phone", "gravityforms"); ?>";

            break;

            case "df_patient_lead" :

              if(!field.label)
                field.label = "<?php _e("Patient Info", "gravityforms"); ?>";
              field.inputs = [new Input(field.id + 0.7, '<?php echo __("First Name", "gravityforms"); ?>'),new Input(field.id + 0.8, '<?php echo __("Last Name", "gravityforms"); ?>'),
                            new Input(field.id + 0.9, '<?php echo __("Email", "gravityforms"); ?>'),new Input(field.id + 0.11, '<?php echo __("Phone", "gravityforms"); ?>'),
                            new Input(field.id + 0.1, '<?php echo __("Street Address", "gravityforms"); ?>'), new Input(field.id + 0.2, '<?php echo __("Address Line 2", "gravityforms"); ?>'), new Input(field.id + 0.3, '<?php echo __("City", "gravityforms"); ?>'),
                            new Input(field.id + 0.4, '<?php echo __("State / Province", "gravityforms"); ?>'), new Input(field.id + 0.5, '<?php echo __("ZIP / Postal Code", "gravityforms"); ?>'), new Input(field.id + 0.6, '<?php echo __("Country", "gravityforms"); ?>')];
            break;
        <?php
        }

        public function patient_lead_gform_editor_js() {
        ?>
            <script type="text/javascript">
                jQuery(document).ready(function($){
                    //fieldSettings["df_patient_lead"] = ".conditional_logic_field_setting, .prepopulate_field_setting, .error_message_setting, .label_setting, .admin_label_setting, .address_setting, .rules_setting, .description_setting, .visibility_setting, .css_class_setting";
                    fieldSettings["df_patient_lead_name"] = ".conditional_logic_field_setting, .prepopulate_field_setting, .error_message_setting, .label_setting, .admin_label_setting, .name_format_setting, .rules_setting, .visibility_setting, .description_setting, .css_class_setting";
                    fieldSettings["df_patient_message"] = ".conditional_logic_field_setting, .prepopulate_field_setting, .error_message_setting, .label_setting, .admin_label_setting, .maxlen_setting, .size_setting, .rules_setting, .visibility_setting, .duplicate_setting, .default_value_textarea_setting, .description_setting, .css_class_setting";
                    fieldSettings["df_patient_lead_phone"] =  ".conditional_logic_field_setting, .prepopulate_field_setting, .error_message_setting, .label_setting, .admin_label_setting, .size_setting, .rules_setting, .duplicate_setting, .visibility_setting, .default_value_setting, .description_setting, .phone_format_setting, .css_class_setting";
                    fieldSettings["df_patient_lead_email"] = ".conditional_logic_field_setting, .prepopulate_field_setting, .error_message_setting, .label_setting, .email_confirm_setting, .admin_label_setting, .size_setting, .rules_setting, .visibility_setting, .duplicate_setting, .default_value_setting, .description_setting, .css_class_setting";
                    jQuery(".patient_info_name").each(function(){
                        var field_address_type
                        field_address_type = jQuery(this).closest('td.pad_top').find('#field_address_type').first()
                        console.log(field_address_type)
                        field_address_type.removeAttr('onchange')
                        field_address_type.change(function(e){
                            field = GetSelectedField();

                            if(field["type"] == "address") {
                                SetAddressType();
                            }
                            else {
                                SetAddressProperties();
                                jQuery(this).closest('.address_setting').find(".gfield_address_type_container").first().hide();
                                var speed = isInit ? "" : "slow";
                                jQuery(this).closest('.address_setting').find("#address_type_container_" + jQuery(this).val()).first().show(speed);
                            }   

                            
                        })
                    });
                });
            </script>
        <?php
        }

        public function custom_css_class($classes, $field, $form){
            if($field["type"] == "text"){
                $classes .= " patient_first_name";
            }
            return $classes;
        }

        public static function find_field_type($type, $form){
            foreach($form['fields'] as $field){
                if($field['type'] == $type) return $field;
            }
            return false;
        }


        public static function get_state_field($field, $id, $field_id, $state_value, $disabled_text, $form_id){
          $state_dropdown_class = $state_text_class = $state_style = $text_style = $state_field_id = "";
          
          if(empty($state_value)){
              $state_value = rgget("defaultState", $field);

              //for backwards compatibility (canadian address type used to store the default state into the defaultProvince property)
              if (rgget("addressType", $field) == "canadian" && !rgempty("defaultProvince", $field))
                  $state_value = $field["defaultProvince"];
          }

          $address_type = rgempty("addressType", $field) ? "international" : $field["addressType"];
          $address_types = GFCommon::get_address_types($form_id);
          $has_state_drop_down = isset($address_types[$address_type]["states"]) && is_array($address_types[$address_type]["states"]);

          if(IS_ADMIN && RG_CURRENT_VIEW != "entry"){
              $state_dropdown_class = "class='state_dropdown'";
              $state_text_class = "class='state_text'";
              $state_style = !$has_state_drop_down ? "style='display:none;'" : "";
              $text_style = $has_state_drop_down  ? "style='display:none;'" : "";
              $state_field_id = "";
          }
          else{
              //id only displayed on front end
              $state_field_id = "id='" . $field_id . "_4'";
          }

          $tabindex = GFCommon::get_tabindex();
          $states = empty($address_types[$address_type]["states"]) ? array() : $address_types[$address_type]["states"];
          $state_dropdown = sprintf("<select name='input_%d.4' %s $tabindex %s $state_dropdown_class $state_style>%s</select>", $id, $state_field_id, $disabled_text, GFCommon::get_state_dropdown($states, $state_value));

          $tabindex = GFCommon::get_tabindex();
          $state_text = sprintf("<input type='text' name='input_%d.4' %s value='%s' $tabindex %s $state_text_class $text_style/>", $id, $state_field_id, $state_value, $disabled_text);

          if(IS_ADMIN && RG_CURRENT_VIEW != "entry")
              return $state_dropdown . $state_text;
          else if($has_state_drop_down)
              return $state_dropdown;
          else
              return $state_text;
      }


        /*public function get_entry_meta($entry_meta, $form_id) {
            $entry_meta['simpleentrymeta']   = array(
                'label'                      => 'Simple Entry Meta',
                'is_numeric'                 => true,
                'is_default_column'          => true,
                'update_entry_meta_callback' => array($this, 'update_entry_meta'),
                'filter'         => array(
                    'operators' => array("is", "isnot", ">", "<")
                            )
                        );
            return $entry_meta;
        }

        public function update_entry_meta($key, $lead, $form) {
            return ""; // return the value of the entry meta
        }

        public function get_locking_config(){
            $strings = array(
                "currently_locked"  => __('This contact is currently locked. Click on the "Request Control" button to let %s know you\'d like to take over.', "gravityforms"),
                "currently_editing" => "%s is currently editing this contact",
                "taken_over"        => "%s has taken over and is currently editing this contact.",
                "lock_requested"    => __("%s has requested permission to take over control of this contact.", "gravityforms")
            );
            $contact_id = $this->get_object_id();
            $config = array(
                "object_type" => "contact",
                "capabilities" => array("gravityforms_contacts_edit_contacts"),
                "redirect_url" => admin_url(sprintf("admin.php?page=gf_contacts&view=contact&subview=profile&id=%d&edit=0", $contact_id)),
                "edit_url" => admin_url(sprintf("admin.php?page=gf_contacts&view=contact&subview=profile&id=%d&edit=1", $contact_id)),
                "strings" => $strings
                );
            return $config;
        }

        public function get_locking_object_id(){
            return rgget("id");
        }

        public function is_locking_edit_page(){
            $is_edit_page = rgget("page") == "gf_contacts" && rgget("view") == "contact" && rgget("subview") == "profile" && rgget("edit")== 1;
            return $is_edit_page;
        }

        public function is_locking_view_page(){
            $is_view_page = rgget("page") == "gf_contacts" && rgget("view") == "contact" && rgget("subview") == "profile" && rgget("edit")== 0;
            return $is_view_page;
        }

        public function is_locking_list_page(){
            $is_list_page = rgget("page") == "gf_contacts" && rgempty("view", $_GET);
            return $is_list_page;
        }

        public function render_uninstall(){
            // an empty function will remove the uninstall section on the settings page
        }*/


    }

    new GFSimpleAddOn();
}