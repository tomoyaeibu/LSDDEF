<?php
	header('Content-type: application/json');
	mb_language("uni");
	mb_internal_encoding("utf-8");
	mb_http_input("auto");
	mb_http_output("utf-8");
	
	//main (Rooting end Exception Handling)
	if(isset($_REQUEST['user']) and isset($_REQUEST['problemID']) ){
		get_MAX_Fitnessvalue();
	}else{
		exception_noParameter();
	}


	/*---------- sellection by Roulette ----------------------
	void get_MAX_Fitnessvalue();
	---------------------------------------------------------*/
	function get_MAX_Fitnessvalue(){
		//meta infomation
		$meta = metaInformation();

		//fitness_valueが最も高い個体を検索
		$userQuery = array(
			"fitness_value"=> array('$exists' => true)
		);
		$individuals = get($userQuery);
		$individuals -> sort(array("fitness_value" => -1));
		$individual = $individuals->getNext();
			
		//json形式で出力
		$result = array(
			'meta'=>$meta,
			'individual'=>$individual
		);
		echo json_encode($result);
	}

	/*---------- Get from MongoDB----------
	MongoCursol get($userQuery)
	---------------------------------------*/
	function get($userQuery){
		// mongo Instance
		$mongo = new Mongo();
		// select DB and Collection
		$db = $mongo->selectDB("LSDDEF");
		$coll = $db->selectCollection($_REQUEST[problemID]);
		//$coll = $db->selectCollection("problem1");

		// set userQuery and get individuals
		$individuals=$coll->find($userQuery);

		return($individuals);
	}


	/*---------- Exception [noParameter]----------
	void exception_noParameter()
	---------------------------------------------*/
	function exception_noParameter(){
		//meta infomation
		$meta = metaInformation();

		//error message
		$error = array(
			'message' => 'Error. user data or content data is NULL'
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
			'url' => 'api/getMaxFitnessvalue.json',
			'method' => 'getMaxFitnessvalue',
			'user' => $_REQUEST['user'],
			'problemID' => $_REQUEST['problemID'],
		);
		return($meta);
	}

?>