<?php
		
		
		//workerの設定
        $worker = new GearmanWorker();
        $worker->addServer();
        $worker->addFunction('test', 'my_job'); 
        while ($worker->work());

        //workerの処理内容
        function my_job($job) {
        	//---------接続先の設定----------
			$host = '10.1.0.175';   
			$port = 22;     
			$user = 'isdl';
			$pass = 'media-system'; 
			$connection = ssh2_connect($host, $port);
			$number = $job->workload();
			//-----------------------------
			$send_local = 'data/input'.$number.'.txt';
			$send_remote = 'input.txt';
			$recv_remote = 'output.txt';
			$recv_local = 'data/output'.$number.'.txt';

			echo "Start job -> number=".$number."\n";

            if($connection!==false){
		        //ログイン
		        ssh2_auth_password($connection, $user, $pass);

		        //残存ファイルの除去
		        $sftp = ssh2_sftp($connection);
		        $status = ssh2_sftp_unlink($sftp, 'output.txt');
		        if($status){ echo ">[remove] OK\n";
		        } else { echo ">>[remove] NG\n";
		        }


		        //入力ファイル送信
		        $status = ssh2_scp_send($connection, $send_local, $send_remote);
		        if($status){ echo ">>[send] OK\n";
		        } else { echo ">>[send] NG\n";
		        }

		        //シミュレーションプログラム実行
		        $stream = ssh2_exec($connection, 'java problem1;');
		        if($stream){ 
		                echo ">>>[excute] OK\n";
		                stream_set_blocking($stream, true);
		                echo stream_get_contents($stream);
		        } else { 
		                echo ">>>[excute] NG\n";
		        }

		        //出力ファイル受信
		        $status = ssh2_scp_recv($connection, $recv_remote, $recv_local);
		        if($status){ echo ">>>>[recv] OK\n";
		        } else { echo ">>>>[recv] NG\n";
		        }

			} else {
				echo "NG#3\n";
			}

			echo "Finish job \n\n";
        }