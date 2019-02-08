<?php
	header('Content-type: application/json');
	mb_language("uni");
	mb_internal_encoding("utf-8");
	mb_http_input("auto");
	mb_http_output("utf-8");
	
	//main (Rooting end Exception Handling)
	if(isset($_REQUEST['user'])){
		get($_REQUEST['user']);
	}else{
		exception_noParameter();
	}

	/*---------- Read from MongoDB----------
	int get($userName)
	---------------------------------------*/
	function get($userName){
		//meta infomation
		$meta = metaInformation();

		// mongo Instance
		$mongo = new Mongo();
		// select DB and Collection
		$db = $mongo->selectDB("cassa-yaku");
		$coll = $db->selectCollection("user-Tweets");

		// read all Oject
		$userQuery = array('user_name' => $userName);
		$tweetList = $coll->find($userQuery);
		// sort by "timestamp"
		$tweetList -> sort(array('timestamp' => -1));

		// reponse
		foreach ($tweetList as $tweet){
			$response[]=$tweet;
		}

		//to json
		$result = array(
			'meta'=>$meta,
			'response'=>$response
		);
    	echo json_encode($result);
	}

	/*---------- Exception [noParameter]----------
	void exception_noParameter()
	---------------------------------------------*/
	function exception_noParameter(){
		//meta infomation
		$meta = metaInformation();

		//error message
		$error = array(
			'message' => 'Error. user data is NULL'
		);

		//to json
		$result = array(
			'meta'=>$meta,
			'error'=>$error
		);
    	echo json_encode($result);
	}

	/*---------- Infotmation of meta ------------
	Array metaInformation()
	---------------------------------------------*/
	function metaInformation(){
		$meta = array(
			'url' => 'api/get.json',
			'method' => 'get',
			'user' => $_REQUEST['user']
		);
		return($meta);
	}


?>