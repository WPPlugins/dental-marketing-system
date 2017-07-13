<?php
if(!class_exists('Dentistfind_Profile_Review_Shortcodes'))
{
  class Dentistfind_Profile_Review_Shortcodes
  {
    /**
     * Construct the plugin object
     */
    public function __construct()
    {
      // register actions
          //$this->dentistfind_url = 'http://127.0.0.1:4000';
          //$this->dentistfind_url = 'http://staging.dentistfind.com';
          $this->dentistfind_url = 'http://dentistfind.com';
          $this->api_key = get_option('dpr_api_key');
          $this->profile_permalink = get_option('dpr_profile_permalink');

          add_shortcode( 'dentistfind-profile-review', array($this,'review_listing') );
          add_action('wp_ajax_post_dp_review', array(&$this, 'ajax_post_review'));
          add_action('wp_ajax_nopriv_post_dp_review', array(&$this, 'ajax_post_review'));
    } // END public function __construct
    
    // [bartag foo="foo-value"]
    function review_listing( $atts ) {
        $a = shortcode_atts( array(
            'dpr_minimum_rating' => -1
        ), $atts );

        ob_start();
        $this->add_style_scripts();

        $url = $this->dentistfind_url.'/api/v1/dentistry/'.$this->profile_permalink.'/reviews.json';
        $result = wp_remote_get($url, 
                  array(
                      'timeout' => 45, 
                      'headers' => 
                        array(
                          'REVIEW_API_TOKEN' => $this->api_key
                          ),
                      'body' => array(
                          'min_rating_score' => get_option('dpr_minimum_rating'),
                          'show_total_reviews' => get_option('dpr_number_review'),
                          'api_token' => $this->api_key
                        )
                      )
                  );
        //echo get_option('dpr_api_key');
        //echo '<br>';
        //print_r($result);
        if($result['response']['code'] == '200') {
          $result = json_decode($result['body']);
          $reviews = $result->reviews;
          $number_reviews = sizeof($reviews);
          $average_rating = $this->rating_average($reviews);
          include(sprintf("%s/templates/short_codes/review_listing.php", dirname(__FILE__)));
        }
        else {
          echo 'Please check api key';
        }
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    function rating_average($reviews) {
      $number_reviews = sizeof($reviews);
      $total_review_score = 0;
      $average_rating  = 0;
      if($number_reviews > 0) {
        foreach($reviews as $review) {
          $total_review_score += $review->rating;
        }

        $average_rating = $total_review_score/$number_reviews;
        $average_rating = round($average_rating, 1);
      }
      
      return $average_rating;
    }

    function ajax_post_review() {
      header("Pragma: no-cache");
      header("Cache-Control: no-cache, must-revalidate");
      header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
   
      header("Content-type: plain/text");
      $url = $this->dentistfind_url.'/api/v1/dentistry/'.$this->profile_permalink.'/create_review';
      $response = wp_remote_post(
            $url,
            array(
                'timeout' => 45,
                'body' => array(
                    'review' => $_POST['review'],
                    'site_url' => home_url(),
                    'ip_address' => $this->get_the_user_ip(),
                    'api_token' => $this->api_key
                ),

                'headers' => array(
                  'REVIEW_API_TOKEN' => $this->api_key
                  )
              )
            );
         
      
      //print_r($response);
      if($response['response']['code'] == '200') {
        $output = array(
                        'status' => 'ok',
                        'msg' => 'Review Created Successfully'
                      );
      }
      else if($response['response']['code'] == '403') {
        $output = array(
                        'status' => 'no',
                        'errors' => 'Invalid Api Key'
                      );
      }

      else if($response['response']['code'] == '409') {
        $output = array(
                        'status' => 'no',
                        'errors' => 'You have already posted a review'
                      );
      }
      


      wp_send_json($output);
    }

    function get_the_user_ip() {
      if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
        //check ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
      } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
        //to check ip is pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
      } else {
        $ip = $_SERVER['REMOTE_ADDR'];
      }

      return apply_filters( 'wpb_get_ip', $ip );
    }

    function add_style_scripts() {
      wp_enqueue_style('jquery-ui-css');
      wp_enqueue_style('jquery-validation-engine-css');
      wp_enqueue_style('dpr-reviews-css');
      wp_enqueue_style('dpr-reviews-rateit-css');
      //wp_enqueue_style('jquery-validation-engine-template-css');
      wp_enqueue_script('jquery-validation-engine');
      wp_enqueue_script('jquery-validation-engine-en');
      wp_enqueue_script('dpr-reviews-rateit');
      wp_enqueue_script('dpr-plugin-script');
    }

  } // END class Dentistfind_Profile_Review_Shortcodes
} // END if(!class_exists('Dentistfind_Profile_Review_Shortcodes'))
