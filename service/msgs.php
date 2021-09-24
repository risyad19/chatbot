<?php
require 'conf.php';
require '../assets/encript/lib.php';

$arr = array();

if(isset($_POST['usmsg'])) {
    $us_msg = mysqli_real_escape_string($conn, $_POST['usmsg']);
    $us_key = $_POST['uskey'];
    $en_key = cryptoJsAesDecrypt($us_key);
    $ex_key = explode('|*|', $en_key);

    $ctroom = mysqli_real_escape_string($conn, $ex_key[0]);
    $ctfromid = mysqli_real_escape_string($conn, $ex_key[1]);
    $ctfromnm = mysqli_real_escape_string($conn, $ex_key[2]);
    $cttoid = mysqli_real_escape_string($conn, $ex_key[3]);
    $cttonm = mysqli_real_escape_string($conn, $ex_key[4]);

    if($us_msg != "") {
        $ch_now = date('H:i');
        $dt_now = date('Y-m-d H:i:s');

        $sqlh = " UPDATE cc_chat_history SET  
            last_active ='".$dt_now."', 
            last_sender ='1', 
            last_message ='".$us_msg."'
            WHERE session_id = '".$ctroom."' AND contact_id = '".$ctfromid."' 
            AND agent_id = '".$cttoid."'  ";
        $resh = mysqli_query($conn, $sqlh);
        if($resh) {
            $sqlm = " INSERT INTO cc_chat_messages SET 
                session_id ='".$ctroom."',
                contact_id ='".$ctfromid."',
                contact_name ='".$ctfromnm."',
                agent_id ='".$cttoid."',
                agent_name ='".$cttonm."',
                message_type ='text',
                message_content ='".$us_msg."',
                direction ='1',
                insert_time ='".$dt_now."' ";
            $resm = mysqli_query($conn, $sqlm);
            if($resm) {
                $arr['status'] = "1";
                $arr['message'] = "Success!";
                $arr['ctchat'] = $us_msg;
                $arr['cttime'] = $ch_now;
                $arr['ctroom'] = $ctroom;
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
}

$myJSON = json_encode($arr);
echo $myJSON;
?>