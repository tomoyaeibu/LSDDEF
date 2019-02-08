<?php

	/*---------------------------------------------*/
	$API_URL = "http://10.1.0.3/api/";
	/*---------------------------------------------*/

function access_webAPI($api_name,$post_data){

	global $API_URL; 	
	$url = $API_URL.$api_name;
	$curl = curl_init($url);

	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);

    $result = curl_exec( $curl );
    curl_close( $curl );
    
	//print
	return $result;
}
 
?>