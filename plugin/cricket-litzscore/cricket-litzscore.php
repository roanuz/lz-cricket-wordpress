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
require_once 'cricket-litzscore-admin.php';

if(!session_id()){
  session_start();
}


$LZ_FLAGS_MAPPING = array(
  'stz' => 'http://www.sknpatriots.com/wp-content/uploads/2015/05/stlucia_zouks.png',
  'snp' => 'http://www.sknpatriots.com/wp-content/uploads/2015/05/SKN-Patriots.png',
  'bt' => 'http://www.sknpatriots.com/wp-content/uploads/2015/05/barbados_tridents.png',
  'jt' => 'http://www.sknpatriots.com/wp-content/uploads/2015/05/jamaica_tallawahs.png',
  'gaw' => 'http://www.sknpatriots.com/wp-content/uploads/2015/05/guyana_amazon_warriors.png',
);

$LZ_IMAGE_URL = 'http://img.litzscore.com/flags/%s_s.png';

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
  global $LZ_FLAGS_MAPPING;
  $plugin_url = plugin_dir_url( __FILE__ );
  // $nonce_value = wp_create_nonce( 'lzapiactions' );

  wp_localize_script( 'lz-js', 'LZCONFIG', array(
    'ajaxUrl' => admin_url( 'admin-ajax.php' ),
    'templateUrl' => $plugin_url.'views/',
    // 'nonce' => $nonce_value,
    'flags' => $LZ_FLAGS_MAPPING,
  ));
}


function get_season_data($seasonKey){
  $ak = getAccessToken();
  if($ak){    
    $season = getSeason($ak, $seasonKey, 'micro_card');
    return $season;
  }else{
    setAccessToken();
    $ak = getAccessToken();
    if($ak){
      return get_season_data($seasonKey);
    }else{
      die('Error while getting season information');
    }    
  }
}

function lzmatch_request(){
  $ak = getAccessToken();
  if($ak){
    $matchKey = $_REQUEST['key'];
    $matchData = getMatch($ak, $matchKey, 'full_card');

    wp_send_json(array('data'=>$matchData));
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
                'key' => null,
                'card_type' =>'null',
                'theme' => 'lz-theme-green-red'), 
                $attrs, 'lzmatch' );

  if($attrs['key'] && !is_null($attrs['key'])){
    $matchKey = $attrs['key'];
  }else{
    $matchKey = get_query_var('lz_matchkey');
  }

  $nonceValue = wp_create_nonce( 'lzapiactionsmatch' );
  echo '
          <div ng-app="lzCricket">
            <div class="lz-outter-box '. $attrs['theme'] .'" lz-cricket-match="'.$matchKey.'" sec="'.$nonceValue.'"></div>
          </div>
        ';
}

function lzSeason($attrs){
  lzInit();
  $attrs = shortcode_atts(array(
                'key' => 'null',
                'card_type' =>'micro_card',
                'theme' => 'lz-theme-green-red',
                'match_page_id'=>null), 
                $attrs, 'lzseasons');

  $seasonKey = $attrs['key'];
  $seasonData = get_season_data($seasonKey);
  $matchUrlPrefix = get_site_url().'/matches/';

  if(!is_null($attrs['match_page_id'])){
    $matchUrlPrefix = $matchUrlPrefix . $attrs['match_page_id'] . '/';
  }

  include_once 'views/lz-cricket-season.php'; 
}


function lzGetTeamLogoUrl($key){
  global $LZ_FLAGS_MAPPING;
  global $LZ_IMAGE_URL;

  if(array_key_exists($key, $LZ_FLAGS_MAPPING)){
    return $LZ_FLAGS_MAPPING[$key];
  }
  return sprintf($LZ_IMAGE_URL, $key);
}


function add_lz_query_vars( $qvars ) {
  $qvars[] = 'lz_matchkey';
  return $qvars;
}
function custom_lz_rewrite_rule() {

  add_rewrite_rule('^matches/([0-9]+)/([^/]*)/?','index.php?page_id=$matches[1]&lz_matchkey=$matches[2]','top');

  $page = get_page_by_title( 'Matches' );
  $pageId = null;
  if($page){
    $pageId = $page->ID;
  }

  add_rewrite_rule('^matches/([^/]*)/?','index.php?page_id='.$pageId.'&lz_matchkey=$matches[1]','top');
  
}

add_action('query_vars', 'add_lz_query_vars');
add_action('init', 'custom_lz_rewrite_rule', 10, 0);
add_shortcode('lzmatch', 'lzMatch');
add_shortcode('lzseason', 'lzSeason');

add_action( 'wp_ajax_lzmatch', 'lzmatch_request' );
add_action( 'wp_ajax_nopriv_lzmatch', 'lzmatch_request' );

wp_register_script('angular', 'https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular.min.js');  
wp_register_script('angular-animate', 'https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular-animate.min.js');  
wp_register_script('moment', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.min.js');  


wp_register_style('roboto-font-css', 'https://fonts.googleapis.com/css?family=RobotoDraft:300,400,500,700,400italic');
wp_register_style('bootsrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css');

$plugin_url = plugin_dir_url( __FILE__ );
wp_register_script('lz-js', $plugin_url. '/views/lz-cricket-angular.js'); 

wp_register_style('lz-css', $plugin_url . '/views/lz-cricket.css');

function use_less_css() {
  $plugin_url = plugin_dir_url( __FILE__ );
  echo '
    <link rel="stylesheet/less" type="text/css" href="'.$plugin_url.'/less/lz-cricket.less">
    <script src="//cdnjs.cloudflare.com/ajax/libs/less.js/2.5.1/less.min.js" type="text/javascript"></script>
  ';
}
// add_action( 'wp_head' , 'use_less_css' );


if (is_admin())
  $litzscore_admin = new LitzscoreAdmin();
?>