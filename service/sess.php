<?php
require 'conf.php';
require '../assets/encript/lib.php';
session_start();
$arr = array();

if(isset($_POST['parms'])) {
    
    $parms = $_POST['parms'];
    $enc = cryptoJsAesDecrypt($parms);
    $exp = explode('|*|', $enc);
    
    $arr['status'] = "1";
    $arr['message'] = "Success!";
    $arr['ctchat'] = $exp[5];
    $arr['cttime'] = $exp[6];

    $_SESSION['ctroom'] = $exp[0];
    $_SESSION['ctfromid'] = $exp[1];
    $_SESSION['ctfromnm'] = $exp[2];
    $_SESSION['cttoid'] = $exp[3];
    $_SESSION['cttonm'] = $exp[4];
    
} else {
    $arr['status'] = "0";
    $arr['message'] = "Failed!";
}

$myJSON = json_encode($arr);
echo $myJSON;
?>