<?php
	header('Content-type: application/json');
	mb_language("uni");
	mb_internal_encoding("utf-8");
	mb_http_input("auto");
	mb_http_output("utf-8");
	
	//main (Rooting end Exception Handling)
	if(isset($_REQUEST['user']) and isset($_REQUEST['problemID']) ){
		get_Top_Priority();
	}else{
		exception_noParameter();
	}



	/*---------- sellection by Roulette ----------------------
	void sellection_Roulette()
	---------------------------------------------------------*/
	function get_Top_Priority(){
		//meta infomation
		$meta = metaInformation();

		//"priority = -1"の個体があればそれを選択
		$userQuery = array(
			"fitness_value"=> array('$exists' => false),
			"priority" => -1
			);
		$individuals = get($userQuery);

		if($individuals -> count() > 0){
			$individuals -> limit(1);
			$skip_number = rand(0,$individuals -> count());
			$individuals -> skip($skip_number);
			$individual = $individuals->getNext();


		//なければpriorityが最も高い個体を選択
		}else{
			$userQuery = array(
				"fitness_value"=> array('$exists' => false)
				);
			$individuals = get($userQuery);
			$individuals -> sort(array("priority" => -1));

			$individuals -> limit(5);
			$individual = $individuals->getNext();
		}

		
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
			'url' => 'api/getTopPriority.json',
			'method' => 'getTopPriority',
			'user' => $_REQUEST['user'],
			'problemID' => $_REQUEST['problemID'],
		);
		return($meta);
	}

?>