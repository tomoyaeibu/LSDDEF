<?php
require "access_webAPI.php"; 
/****************************
MOEA-HRE用
*****************************/

//---------接続先の設定----------
$host = '10.1.0.6';   //simulator1
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
$problemID="problem3";

$interval_time = 1000000;
///----------------------------

for($i=0;$i<100;$i++){
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
        $command = "php client.php ".$problemID;
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
        echo $recv_remote;
        $status = false;
        while($status == false){
                usleep($interval_time);
                $status = ssh2_scp_recv($connection, $recv_remote, $recv_local);
                if($status){ 
                        echo ">>>>[recv] OK\n";
                } else {
                        echo ">>>>[recv] NG\n";
                }
        }

        //出力ファイルの読み込みとcontentの設定
        $content = array(
                "id" => $result["individual"]["_id"]["\$id"]
        );

        $file = fopen($recv_local, "r");
        if($file){
                while ($line = fgets($file)) {
                        $output = explode(" ",$line);
                        $content += array($output[0]=>rtrim($output[1]));
                }
        }

        //データベースに評価値を登録
        $content = json_encode($content);
        $post_data = array(
                "user"=>$userID,
                "problemID"=>$problemID,
                "content"=>$content
        );
        $result = access_webAPI('postFitnessValue',$post_data);

        print_r(json_encode($result));

}

?>