<?php
require_once '../../app/init.php';
header('Content-Type: text/html; charset=utf-8');

	# include the libraries needed to make the REST API requests
	include 'OAuthResponder.php';
	include 'responder_sdk.php';
	
	
	################################################################################
	# Tokens; should fill with the tokens acquired from the responder support team #
	################################################################################
	
	# represents you as a client (the connection to responder)
	$client_key = 'C2B7A5F3ACF333A7B631879CF8480F41';
	$client_secret = '2672575EDC51407CEEBD08C374F4CFF5';
	
	# represents the user in responder
	$user_key = '9DFA3C26476C7B808B8E190283668FA3';
	$user_secret = '45EA7A859F5C188944033A346C249D8C';
	
	
	# create the responder request instance
	$responder = new ResponderOAuth($client_key, $client_secret, $user_key, $user_secret);
	
	 $list_id = 495283;
	
	 $offset = 0;
	 for ($i=0; $i < 1; $i++) { 
	 $response = json_decode($responder->http_request("lists/{$list_id}/subscribers?offset=".$offset, 'get'));
	 if (count($response) <= '500' && count($response) != '0') {
		 foreach ($response as $key) {
			$ID = $key->ID;
			$NAME = $key->NAME;
			$EMAIL = $key->EMAIL;
			$PHONE = $key->PHONE;
			AddNewLead($PHONE,$EMAIL,$NAME);
		 }
	 }
	 else {
		 break;
	 }
	 $offset = $offset+500;
	 }
	
echo $offset;
?>