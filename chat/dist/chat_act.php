<?php
 require 'encript/lib.php';

 session_start();
 
$tokn = $_POST['token'];
$mail = $_POST['mail'];
$name = $_POST['name'];

$_SESSION['tokenOn'] = $tokn;
$_SESSION['mailOn'] = $mail;
$_SESSION['nameOn'] = $name;

$msg = "";
$response = array();
$response["data"] = array();


$h['msg']  = "success";
$h['type'] = "1";

array_push($response["data"], $h);
$msg .= json_encode($response);

echo $msg;

?>