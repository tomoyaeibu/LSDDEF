<?php
require "access_webAPI.php"; 

//---------接続先の設定----------
$host = '10.1.0.7';   
$port = 22;     
$user = 'ubuntu';
$pass = 'media-system'; 
//-----------------------------


//--------その他の設定-----------
$send_local = 'data/input.txt';
$send_remote = 'input.txt';
$recv_remote = 'output.txt';
$recv_local = 'data/output.txt';

$userID="user1";
$problemID="problem1";

$interval_time = 0;
///----------------------------

for($i=0;;$i++){
        //ログイン
        $connection = ssh2_connect($host, $port);
        ssh2_auth_password($connection, $user, $pass);

        //データベースから計算する個体を取得
        $post_data = array(
                "user"=>$userID,
                "problemID"=>$problemID
        );

        $result = access_webAPI('getTopPriority',$post_data);
        $result = json_decode($result, true);
        if($result["individual"]==NULL){ 
                echo ("Nothing individual to evaluate\n");
                continue;
        }

        //入力ファイルの生成
        $content = "#parameter set\n";
        foreach ($result["individual"] as $key => $value) {
                if(!strstr($key, "priority") && !strstr($key, "_id")){
                        $content .= "$key $value\n";
                }
        }
        file_put_contents($send_local, $content);

        //入力ファイル送信
        $status = ssh2_scp_send($connection, $send_local, $send_remote);
        if($status){ 
                echo ">>[send] OK\n";
        } else { 
                echo ">>[send] NG\n";
                exit(1);
        }

        //シミュレーターにジョブを登録
        $command = "php simulator/client.php ".$problemID;
        $stream = ssh2_exec($connection, $command);
        if($stream){ 
                echo ">>>[excute] OK\n";
                stream_set_blocking($stream, true);
                $jobID = stream_get_contents($stream);
                echo stream_get_contents($stream);
        } else { 
                echo ">>>[excute] NG\n";
                exit(1);
        }

        //ジョブの終了を監視（出力ファイルの検出）
        $recv_remote = rtrim($jobID).".out";
        $status = false;
        while($status == false){
                $status = ssh2_scp_recv($connection, $recv_remote, $recv_local);
                if($status){ 
                        echo ">>>>[recv] OK\n";
                } else {
                        echo ">>>>[recv] NG\n";
                }
                usleep($interval_time);
        }

        //出力ファイルの読み込み
        $output = explode(" ",file_get_contents($recv_local, true));

        //データベースに評価値を登録
        $content = array(
                "fitness_value" => $output[1],
                "id" => $result["individual"]["_id"]["\$id"]
        );
        $content = json_encode($content);
        $post_data = array(
                "user"=>$userID,
                "problemID"=>$problemID,
                "content"=>$content
        );
        $result = access_webAPI('postFitnessValue',$post_data);


}

?>