<?php
	header('Content-type: application/json');
	mb_language("uni");
	mb_internal_encoding("utf-8");
	mb_http_input("auto");
	mb_http_output("utf-8");
	
	//main (Rooting end Exception Handling)
	if(isset($_REQUEST['user']) and isset($_REQUEST['problemID']) ){
		sellection_Roulette();
	}else{
		exception_noParameter();
	}


	/*---------- sellection by Roulette ----------------------
	void sellection_Roulette()
	---------------------------------------------------------*/
	function sellection_Roulette(){
		//meta infomation
		$meta = metaInformation();

		//データベースから評価値を計算済みの個体を読み出し
		$individuals = get();

		//fitness_valueの総和を計算
 		$roulette_base = 0;
 		foreach ($individuals as $individual){
 			$roulette_base += $individual['fitness_value'];
 		}
 	
 		//親個体1を選択
 		$random = randomFloat(0,$roulette_base);
		$roulette_sum= 0;
 		foreach ($individuals as $individual){
			$roulette_sum += $individual['fitness_value'];
 			if($roulette_sum > $random) {
 				$parent1 = $individual;
 				break;
 			}
 		}

 		//親個体2を選択
 		$random = randomFloat(0,$roulette_base);
		$roulette_sum= 0;
 		foreach ($individuals as $individual){
			$roulette_sum += $individual['fitness_value'];
 			if($roulette_sum > $random) {
 				$parent2 = $individual;
 				break;
 			}
 		}
 	
		//json形式で出力
		$result = array(
			'meta'=>$meta,
			'parent1'=>$parent1,
			'parent2'=>$parent2
		);
		echo json_encode($result);
	}

	/*---------- Get from MongoDB----------
	MongoCursol get()
	---------------------------------------*/
	function get(){
		// mongo Instance
		$mongo = new Mongo();
		// select DB and Collection
		$db = $mongo->selectDB("LSDDEF");
		$coll = $db->selectCollection($_REQUEST[problemID]);
		//$coll = $db->selectCollection("problem1");

		// set userQuery and get individuals
		$userQuery = array(
			"fitness_value"=> array('$exists' => true),
			"parameter1"=> array('$exists' => true),
			"parameter2"=> array('$exists' => true)
		);
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
			'url' => 'api/getIndividualsRoulette.json',
			'method' => 'getIndividualsRoulette',
			'user' => $_REQUEST['user'],
			'problemID' => $_REQUEST['problemID'],
		);
		return($meta);
	}

/*--------------------------------------
$min〜$maxまでの小数の乱数を計算
----------------------------------------*/
function randomFloat($min = 0, $max = 1) {
    return $min + mt_rand() / mt_getrandmax() * ($max - $min);
} 

?>