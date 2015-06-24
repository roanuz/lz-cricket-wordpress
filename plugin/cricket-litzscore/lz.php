<?php

require_once 'lzconfig.php';

function getAccessToken(){
  if(!session_id()){
  	session_start();
  }

  if($_SESSION['lzak']){
	  // $cipher = new token( 'LSDXXSSSL2li342#@$f;;aso8f;ahf;akdhj3sfl_');
	  // return mc_decrypt( $_COOKIE['lzak'] );  	
  	return $_SESSION['lzak'];
  }
  return false;
}

function setAccessToken(){
    if(!session_id()){
      session_start();
    }

	$dk = $_COOKIE['lzdk'];
	if(!$dk){
		$dk = 'xx';
		setcookie('lzdk', $dk, 60*60*24*10, '/');
	}

    $response = auth(session_id());
    $accessToken = $response['auth']['access_token'];
    $expiresIn = intval($response['auth']['expires']);
    // set_at($accessToken, $expiresIn);

    $_SESSION['lzak'] = $accessToken;
    $_SESSION['lzake'] = $expiresIn;
    return $accessToken;
}

function auth($deviceId) {

	$lz_appconfig = get_option( 'litzscore_app_options_info' );	
	$fields = array(
		'access_key' => $lz_appconfig['access_key'],
		'secret_key' => $lz_appconfig['secret_key'],
		'app_id' => $lz_appconfig['appid'],
		'device_id' => $deviceId,
	);	

	$fields_string = '';
	
	foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
	$fields_string=rtrim($fields_string, '&');
	 
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, LZ_url.'auth/');
	curl_setopt($ch, CURLOPT_POST, true);
	 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	$response = json_decode($response, true);

	curl_close($ch);
	
	return $response;
}

function getData($req_url, $fields){
	$url = LZ_url. $req_url. '/?' . http_build_query($fields);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_ENCODING, '');
	 
	$response = curl_exec($ch);
	$response = json_decode($response, true);
	curl_close($ch);
	return $response['data'];
}

function getMatch($access_token, $match_key, $card_type){
	$fields = array(
	    'access_token' => $access_token,
	    'card_type' => $card_type,
	);

	$url = 'match/' .$match_key;
	$response = getData($url, $fields);
	return $response;
}

function getRecentMatch($access_token, $card_type){

	$fields = array(
	    'access_token' => $access_token,
	    'card_type' => $card_type,
	);

	$url = 'recent_matches';
	$response = getData($url, $fields);
	return $response;
}

function getRecentSeasonMatch($access_token, $season_key, $card_type){

	$fields = array(
	    'access_token' => $access_token,
	    'card_type' => $card_type,
	);

	$url = 'season/' .$season_key .'/recent_matches';
	$response = getData($url, $fields);
	return $response;
}

function getRecentSeason($access_token){
	$fields = array(
	    'access_token' => $access_token,
	);
	$url = 'recent_seasons';
	$response = getData($url, $fields);
	return $response;
}

function getSchedule($access_token, $date){
	$fields = array(
	    'access_token' => $access_token,
	    'date' => $date,
	);
	$url = 'schedule';
	$response = getData($url, $fields);
	return $response;
}

function getSeasonSchedule($access_token, $season_key, $formate){
	$fields = array(
	    'access_token' => $access_token,
	    'formate' => $formate
	);
	$url = 'season/' .$season_key. '/schedule';
	$response = getData($url, $fields);
	return $response;
}


function getSeason($access_token, $season_key, $card_type){
	$fields = array(
	    'access_token' => $access_token,
	    'card_type' => $card_type,
	);
	$url = 'season/' .$season_key;
	$response = getData($url, $fields);
	return $response;
}


function getSeasonStats($access_token, $season_key){
	$fields = array(
	    'access_token' => $access_token,
	);
	$url = 'season/' .$season_key. '/stats';
	$response = getData($url, $fields);
	return $response;
}

function getSeasonPoints($access_token, $season_key){
	$fields = array(
	    'access_token' => $access_token,
	);
	$url = 'season/' .$season_key. '/points';
	$response = getData($url, $fields);
	return $response;
}

function getSeasonPlayerStats($access_token, $season_key, $player_key){
	$fields = array(
	    'access_token' => $access_token,
	);

	$url = 'season/' .$season_key. '/player/'.$player_key .'/stats';
	$response = getData($url, $fields);
	return $response;
}


function getMatchSummary($access_token, $match_key){
	$fields = array(
	    'access_token' => $access_token,
	);
	$url = 'match/' .$match_key. '/overs_summary';
	$response = getData($url, $fields);
	return $response;
}

function getNewsAaggregation($access_token){
	$fields = array(
	    'access_token' => $access_token,
	);
	$url = 'news_aggregation';
	$response = getData($url, $fields);
	return $response;
}

function getBallByBall($access_token, $match_key, $over_key){
	$fields = array(
	    'access_token' => $access_token,
	);
	$url = 'match/' .$match_key. '/balls/' .$over_key;
	$response = getData($url, $fields);
	return $response;
}

?>