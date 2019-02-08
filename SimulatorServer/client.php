<?php

	//初期設定
	$client = new GearmanClient();
	$client->addServer();

	//前処理
	$problemID = $argv[1];
	$time = time();
	print $time."\n";
	$content = $time."+".$problemID;

	//ジョブ登録
	$client->doBackground("test", $content);
	if ($client->returnCode() != GEARMAN_SUCCESS) {
    	    print "faild add job!\n";
	}