<?php
	header('Content-type: application/json');
	mb_language("uni");
	mb_internal_encoding("utf-8");
	mb_http_input("auto");
	mb_http_output("utf-8");
	
	//main (Rooting end Exception Handling)
	if(isset($_REQUEST['user']) and isset($_REQUEST['problemID']) and isset($_REQUEST['content'])){
		parse_and_post();
	}else{
		exception_noParameter();
	}

	/*---------- Parse content and post---------------------
	void parse_and_post($userName, $problemID, $content)
	---------------------------------------------------------*/
	function parse_and_post(){
		//meta infomation
		$meta = metaInformation();

		//contentをパース
		$content = mb_convert_encoding($_REQUEST[content], 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
		$content = json_decode($content, true);
		
		//データベースに書き込み
		$status = post($content["id"],$content["fitness_value"]);

		//to json
		$result = array(
			'meta'=>$meta,
			'status'=>$status
		);
		echo json_encode($result);
	}


	/*---------- Post to MongoDB----------
	void post($id,$fitness_value)
	---------------------------------------*/
	function post($id,$fitness_value){
		// mongo Instance
		$mongo = new Mongo();
		// select DB and Collection
		$db = $mongo->selectDB("LSDDEF");
		$coll = $db->selectCollection($_REQUEST[problemID]);
		//$coll = $db->selectCollection("problem1");

		$successOrNot=$coll->update(
			array('_id' => new MongoId($id)),
			array('$set' => array('fitness_value' => intval($fitness_value))), 
    		array('upsert' => true) 
		);

		//check status
		if($successOrNot){
			//success message
			$status = array(
				'message' => 'successfully completed'
			);
		}else{
			//failure message
			$status = array(
				'message' => 'unccessfully failed over'
			);
		}
		return $status;


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
			'url' => 'api/postFitnessValue.json',
			'method' => 'postFitnessValue',
			'user' => $_REQUEST['user'],
			'problemID' => $_REQUEST['problemID'],
			'content' => $_REQUEST['content'],
		);
		return($meta);
	}


?>