<?php
/**
 * @package Cricket-Litzscore
 * @version 1.0.0
 */
/*
Plugin Name: Cricket Scorecard from Litzscore
Plugin URI: http://static.litzscore.com/plugins/wordpress/cricket-litzscore
Description: Show live cricket score, recent matches and schedules. Litzscore provides live cricket score for ICC, IPL, CL and CPL. 
Author: Litzscore Developers
Version: 1.0
Author URI: http://developers.litzscore.com/
*/

require_once 'lzconfig.php';
require_once 'lz.php';

class LitzscoreAdmin{

  private $fields = array(
    'appid'=>'App ID',
    'access_key'=>'Access Key', 
    'secret_key'=>'Secret Key');
    /**
     * Start up
     */ 
  public function __construct(){
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
  }

    /**
     * Add options page
     */
  public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'Litzscore', 
            'manage_options', 
            'litzscore-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    } 

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'litzscore_app_options_info' );
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>Litzscore App Details</h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'litzscore_app_options' );   
                do_settings_sections( 'litzscore-setting-admin' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }


    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'litzscore_app_options', // Option group
            'litzscore_app_options_info', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'lz_setting_section_id', // ID
            '', // Title
            array( $this, 'print_section_info' ), // Callback
            'litzscore-setting-admin' // Page
        );  

        foreach ($this->fields as $field => $name) {
          add_settings_field(
              $field, // ID
              $name, // Title 
              array( $this, 'field_callback'), // Callback
              'litzscore-setting-admin', // Page
              'lz_setting_section_id', // Section
              array($field)           
          );
        }
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();

        foreach ($this->fields as $field => $name) {
          if( isset( $input[$field] ) )
              $new_input[$field] = sanitize_text_field( $input[$field] );
      }
        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your litzscore app details here. For more information check <a href="https://developers.litzscore.com/">developers.litzscore.com</a>';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function field_callback($args)
    {
      $name = $args[0];
        printf(
            '<input type="text" id="'.$name.'" name="litzscore_app_options_info['.$name.']" value="%s" />',
            isset( $this->options[$name] ) ? esc_attr( $this->options[$name]) : ''
        );
    }

}


// function loadMatch($key){
//   echo '
//           <div ng-app="lzCricket">
//             <div lz-cricket-match="'.$key.'"></div>
//           </div>
//         ';
// }

// function loadRecentMatches(){
//   include('static/html/litzscore-recent_matches-view.php');
// }

// function set_access_token_cookie(){
//   printf('<script type="text/javascript">
//         window.lzAPIPrefix = "'.plugin_dir_url( __FILE__ ).'";
//         window.lzTemplatePrefix = "'.plugin_dir_url( __FILE__ ).'views/";
//         </script>');

//   if(isset($_COOKIE['lz_at'])){
//   }else{
//       $response = auth();
//       $accessToken=$response['auth']['access_token'];
//       $expiresIn=$response['auth']['expires'];
//       printf('<script type="text/javascript">
//         window.lzTemplatePrefix = "'.plugin_dir_url( __FILE__ ).'views/";
//         var expires = new Date();
//         expires.setTime(expires.getTime() + parseInt('.$expiresIn.'));
//         document.cookie ="lz_at='. $accessToken. ';expires=" + expires.toUTCString() + ";path=/";
//         </script>');
//   }
// }



function insert_footer_script(){  
  wp_enqueue_script('angular');
  wp_enqueue_script('angular-animate');
  wp_enqueue_script('moment');
  wp_enqueue_script('lz-js');

}

function insert_header_script(){
  wp_enqueue_style('roboto-font-css');
  wp_enqueue_style('bootsrap-css');
  wp_enqueue_style('lz-css');
}

function insert_script_src() {
  $plugin_url = plugin_dir_url( __FILE__ );
  // $nonce_value = wp_create_nonce( 'lzapiactions' );

  wp_localize_script( 'lz-js', 'LZCONFIG', array(
    'ajaxUrl' => admin_url( 'admin-ajax.php' ),
    'templateUrl' => $plugin_url.'views/',
    // 'nonce' => $nonce_value,
    'flags' => array(
      'stz' => 'http://www.sknpatriots.com/wp-content/uploads/2015/05/stlucia_zouks.png',
      'snp' => 'http://www.sknpatriots.com/wp-content/uploads/2015/05/SKN-Patriots.png',
      'bt' => 'http://www.sknpatriots.com/wp-content/uploads/2015/05/barbados_tridents.png',
      'jt' => 'http://www.sknpatriots.com/wp-content/uploads/2015/05/jamaica_tallawahs.png',
      'gaw' => 'http://www.sknpatriots.com/wp-content/uploads/2015/05/guyana_amazon_warriors.png',
    ),
  ));
}


function lzmatch_request(){
  $ak = getAccessToken();
  if($ak){
    $matchKey = $_REQUEST['key'];
    $aa = getMatch($ak, $matchKey, 'full_card');
    // wp_send_json(array('a'=>'a'));
    exit();
  }else{
    setAccessToken();
    $ak = getAccessToken();
    if($ak){
      lzmatch_request();
    }else{
      die('Error');
    }    
  }
}

function lzInit(){
    insert_header_script();
    insert_script_src();
    insert_footer_script();  
}

function lzMatch($attrs){
  lzInit();
  $attrs = shortcode_atts(array(
                'key' => 'null',
                'card_type' =>'null',
                'theme' => 'lz-theme-green-red'), 
                $attrs, 'lzmatch' );

  $matchKey = $attrs['key'];
  $nonceValue = wp_create_nonce( 'lzapiactionsmatch' );
  echo '
          <div ng-app="lzCricket">
            <div class="lz-outter-box '. $attrs['theme'] .'" lz-cricket-match="'.$matchKey.'" sec="'.$nonceValue.'"></div>
          </div>
        ';
}

add_shortcode('lzmatch', 'lzMatch');
add_action( 'wp_ajax_lzmatch', 'lzmatch_request' );
add_action( 'wp_ajax_nopriv_lzmatch', 'lzmatch_request' );
wp_register_script('angular', 'https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular.min.js');  
wp_register_script('angular-animate', 'https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular-animate.min.js');  
wp_register_script('moment', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.min.js');  
wp_register_script('lz-js', 'http://static.litzscore.com/release/sknpatriots/js/lz-cricket-angular.js'); 

wp_register_style('roboto-font-css', 'https://fonts.googleapis.com/css?family=RobotoDraft:300,400,500,700,400italic');
wp_register_style('bootsrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css');
wp_register_style('lz-css', 'http://static.litzscore.com/release/sknpatriots/css/lz-cricket.css');


if (is_admin())
  $litzscore_admin = new LitzscoreAdmin();
?>