<?php

	//初期設定
	$worker = new GearmanWorker();
	$worker->addServer();
	$worker->addFunction('test', 'my_test');

	//待ち受け開始
	while ($worker->work());

	//実行コード
	function my_test($job) {
		//workloadをパース
		$content = explode("+",$job->workload());

		//problemIDをもとにプログラムを実行
		//[ジョブID].outに出力
		$command = "/home/ubuntu/simulator/".$content[1].".exe";
		$command .= " > /home/ubuntu/".$content[0].".out";
		exec($command);

		print("finish job [".$content[0]."]\n");

	}