<?php
	header('Content-type: application/json');
	mb_language("uni");
	mb_internal_encoding("utf-8");
	mb_http_input("auto");
	mb_http_output("utf-8");
	
	//main (Rooting end Exception Handling)
	if(isset($_REQUEST['user']) and isset($_REQUEST['problemID']) and isset($_REQUEST['content'])){
		garbageCollection();
	}else{
		exception_noParameter();
	}


	/*---------- Parse contnent and remove garbage----------
	void garbageCollection($userName, $problemID, $content)
	---------------------------------------------------------*/
	function garbageCollection(){
		//meta infomation
		$meta = metaInformation();

		//contentから集団サイズを取得
		$content = mb_convert_encoding($_REQUEST[content], 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
		$content = json_decode($content, true);
		if ($content === NULL) {
			return;
		}
		$populationSize = $content["populationSize"];
		$isEvaluated = $content["isEvaluated"];

		//カーソルを設定
		$individuals = get($isEvaluated);

		//データベースにある計算済み個体の数を取得
		$numberOfIndividual = $individuals -> count();

		//計算済み個体の数が集団サイズを上回っていたら上回っている分だけ削除
		$numberOfRemoving = $numberOfIndividual - $populationSize;
		if($numberOfRemoving > 0){			
			//fitness_value(priority)が小さい順に、上回っている分だけ個体を取得
			if($isEvaluated==1){
				$individuals -> sort(array("fitness_value" => 1));
			}else if($isEvaluated==0){
				$individuals -> sort(array("priority" => 1));
			}
			$individuals -> limit($numberOfRemoving);

			//その個体を削除
			foreach ($individuals as $individual) {
    			remove($individual);
			}
		}

		//to json
		$result = array(
			'meta'=>$meta,
			'numberOfRemoving'=>$numberOfRemoving,
			'isEvaluated'=>$isEvaluated
		);
		echo json_encode($result);
	}


	/*---------- Get from MongoDB----------
	MongoCursol get()
	---------------------------------------*/
	function get($isEvaluated){
		// mongo Instance
		$mongo = new Mongo();
		// select DB and Collection
		$db = $mongo->selectDB("LSDDEF");
		//$coll = $db->selectCollection($_REQUEST[problemID]);
		$coll = $db->selectCollection("problem1");

		// set userQuery and get individuals
		if($isEvaluated==1){
			$userQuery = array("fitness_value"=> array('$exists' => true));
		}else if($isEvaluated==0){
			$userQuery = array("fitness_value"=> array('$exists' => false));
		}
		$individuals=$coll->find($userQuery);

		return($individuals);
	}

	/*---------- Get from MongoDB----------
	void remove(MongoCursol)
	---------------------------------------*/
	function remove($cursol){
		// mongo Instance
		$mongo = new Mongo();
		// select DB and Collection
		$db = $mongo->selectDB("LSDDEF");
		//$coll = $db->selectCollection($_REQUEST[problemID]);
		$coll = $db->selectCollection("problem1");

		// remove
		$coll->remove($cursol);

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
			'url' => 'api/garbageCollection.json',
			'method' => 'garbageCollection',
			'user' => $_REQUEST['user'],
			'problemID' => $_REQUEST['problemID'],
			'content' => $_REQUEST['content'],
		);
		return($meta);
	}


?>