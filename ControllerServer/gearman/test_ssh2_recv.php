<?php
$host = '10.1.0.175';   
$port = 22;     
$user = 'isdl';
$pass = 'media-system'; 

$srcf = 'test.txt';   
$dstf = 'test.txt'; 

$connection = ssh2_connect($host, $port);
if($connection!==false){
        ssh2_auth_password($connection, $user, $pass);
        $bret = ssh2_scp_recv($connection, $srcf, $dstf);
        if($bret){
                if(file_exists($dstf)){
                        echo "OK\n";
                } else {
                        echo "NG#1\n";
                }
        } else {
                echo "NG#2\n";
        }
} else {
        echo "NG#3\n";
}
?>