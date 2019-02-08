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
$recv_remote = '1447061604.out';
$recv_local = 'data/output.txt';

$userID="user1";
$problemID="problem3";

$interval_time = 0;
///----------------------------
     
        //ログイン
        $connection = ssh2_connect($host, $port);
        ssh2_auth_password($connection, $user, $pass);


                $status = ssh2_scp_recv($connection, $recv_remote, $recv_local);
                if($status){ 
                        echo ">>>>[recv] OK\n";
                } else {
                        echo ">>>>[recv] NG\n";
                }



?>