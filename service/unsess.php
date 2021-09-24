<?php
require 'conf.php';
require '../assets/encript/lib.php';
session_start();
$arr = array();

if(isset($_POST['parms'])) {
    
    $parms = $_POST['parms'];
    $enc = cryptoJsAesDecrypt($parms);
    $exp = explode('|*|', $enc);

    $ctroom = $_SESSION['ctroom'];
    $ctfromid = $_SESSION['ctfromid'];
    $cttoid = $_SESSION['cttoid'];

    if($ctroom == $exp[0] && $ctfromid == $exp[1] && $cttoid == $exp[3]) {
        $ch_now = date('H:i');
        $dt_now = date('Y-m-d H:i:s');

        $sqlh = " UPDATE cc_chat_history SET  
            end_time ='".$dt_now."',  
            status ='1'  
            WHERE session_id = '".$ctroom."' AND contact_id = '".$ctfromid."' 
            AND agent_id = '".$cttoid."'  ";
        $resh = mysqli_query($conn, $sqlh);
        if($resh) {
            session_unset();
            session_destroy(); 
            $arr['status'] = "1";
            $arr['message'] = "Success!";
        } else {
            $arr['status'] = "0";
            $arr['message'] = "Failed!";
        }
    } else {
        $arr['status'] = "0";
        $arr['message'] = "Failed!";
    }
} else {
    $arr['status'] = "0";
    $arr['message'] = "Failed!";
}

$myJSON = json_encode($arr);
echo $myJSON;
?>