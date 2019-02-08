<?php
		//clientの設定
        $client = new GearmanClient();
        $client->addServer();

        //前処理
        $number = $argv[1];

        //ジョブの登録
        $client->doBackground("test", $number);

        //エラー処理
        if ($client->returnCode() != GEARMAN_SUCCESS) {
            print "faild add job!\n";
        }else{
        	print "complete registing job -> number=".$number."\n";
        }