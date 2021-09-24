<?php
require 'conf.php';

$arr = array();

// Check connection
if (!$conn) {
    $arr['status'] = "0";
    $arr['message'] = "Connection failed!";
// return false;
} else {
    if(isset($_POST['ussoid'])) {
        $us_soid = mysqli_real_escape_string($conn, $_POST['ussoid']);
        $us_tokn = mysqli_real_escape_string($conn, $_POST['ustokn']);
        $us_mail = mysqli_real_escape_string($conn, $_POST['usmail']);
        $us_phno = mysqli_real_escape_string($conn, $_POST['usphno']);
        $us_name = mysqli_real_escape_string($conn, $_POST['usname']);
        $us_ques = mysqli_real_escape_string($conn, $_POST['usques']);

        $sql = " SELECT COUNT(a.id) AS cid, a.id FROM cc_customer_contact a 
            WHERE (a.email = '".$us_mail."' OR a.phone_1 = '".$us_phno."') LIMIT 1 ";
            $result = mysqli_query($conn, $sql);
            if($row = mysqli_fetch_assoc($result)) {
                $cont_id = $row['id'];
                $cid = $row['cid'];

                $ch_now = date('H:i');
                $dt_now = date('Y-m-d H:i:s');
                if($cid == "1") {
                    $ss_new = date('YmdHis').$cont_id.rand(1, 100);

                    $sqlom = " INSERT INTO cc_omni_session_history SET 
                        channel_id ='14', 
                        session_id ='".$ss_new."', 
                        call_type ='1', 
                        username ='".$us_mail."',  
                        agent_id ='".$agid."', 
                        insert_time ='".$dt_now."', 
                        omni_read_status ='0', 
                        last_message ='".$us_ques."', 
                        last_active ='".$dt_now."' "; //echo $sqlh;
                    mysqli_query($conn, $sqlom);

                    $sqlh = " INSERT INTO cc_chat_history SET 
                        session_id ='".$ss_new."', 
                        room_id ='".$ss_new."', 
                        start_time ='".$dt_now."', 
                        contact_id ='".$cont_id."', 
                        agent_id ='".$agid."', 
                        last_active ='".$dt_now."', 
                        last_sender ='1', 
                        last_message ='".$us_ques."' "; //echo $sqlh;
                    $resh = mysqli_query($conn, $sqlh);
                    if($resh) {
                        $sqlm = " INSERT INTO cc_chat_messages SET 
                            session_id ='".$ss_new."',
                            contact_id ='".$cont_id."',
                            contact_name ='".$us_name."',
                            agent_id ='".$agid."',
                            agent_name ='".$agnm."',
                            message_type ='text',
                            message_content ='".$us_ques."',
                            direction ='1',
                            insert_time ='".$dt_now."' ";
                        $resm = mysqli_query($conn, $sqlm);
                        if($resm) {

                            $arr['status'] = "1";
                            $arr['message'] = "Success!";
                            $arr['ctroom'] = $ss_new;
                            $arr['ctfromid'] = $cont_id;
                            $arr['ctfromnm'] = $us_name;
                            $arr['cttoid'] = $agid;
                            $arr['cttonm'] = $agnm;
                            $arr['ctmsg'] = $us_ques;
                            $arr['cttime'] = $ch_now;

                        } else {
                            $arr['status'] = "0";
                            $arr['message'] = "Failed!0";
                        }
                    } else {
                        $arr['status'] = "0";
                        $arr['message'] = "Failed!1";
                    }

                } else {
                    $ss_new = date('YmdHis').$cont_id.rand(1, 100);

                    $sqlc = " INSERT INTO cc_customer_contact SET 
                        contact_name ='".$us_name."',
                        phone_1 ='".$us_phno."',
                        email ='".$us_mail."',
                        channel_id ='14',
                        insert_time ='".$dt_now."' ";
                    $resc = mysqli_query($conn, $sqlc);
                    if($resc) {
                        $cont_id = mysqli_insert_id($conn);

                        $sqlh = " INSERT INTO cc_chat_history SET 
                            session_id ='".$ss_new."',
                            room_id ='".$ss_new."',
                            start_time ='".$dt_now."',
                            contact_id ='".$cont_id."',
                            agent_id ='".$agid."',
                            last_active ='".$dt_now."', 
                            last_sender ='1',
                            last_message ='".$us_ques."' ";
                        $resh = mysqli_query($conn, $sqlh);
                        if($resh) {
                            $sqlm = " INSERT INTO cc_chat_messages SET 
                                session_id ='".$ss_new."',
                                contact_id ='".$cont_id."',
                                contact_name ='".$us_name."',
                                agent_id ='".$agid."',
                                agent_name ='".$agnm."',
                                message_type ='text',
                                message_content ='".$us_ques."',
                                direction ='1',
                                insert_time ='".$dt_now."' ";
                            $resm = mysqli_query($conn, $sqlm);
                            if($resm) {
                                
                                $arr['status'] = "1";
                                $arr['message'] = "Success!";
                                $arr['ctroom'] = $ss_new;
                                $arr['ctfromid'] = $cont_id;
                                $arr['ctfromnm'] = $us_name;
                                $arr['cttoid'] = $agid;
                                $arr['cttonm'] = $agnm;
                                $arr['ctmsg'] = $us_ques;
                                $arr['cttime'] = $ch_now;
                                
                            } else {
                                $arr['status'] = "0";
                                $arr['message'] = "Failed!2";
                            }
                        } else {
                            $arr['status'] = "0";
                            $arr['message'] = "Failed!3";
                        }
                    } else {
                        $arr['status'] = "0";
                        $arr['message'] = "Failed!4";
                    }
                }
            } else {
                $arr['status'] = "0";
                $arr['message'] = "Failed!5";
            }
        
    } else {
        $arr['status'] = "0";
        $arr['message'] = "Failed!6";
    }
    
}

$myJSON = json_encode($arr);
echo $myJSON;

mysqli_close($conn);
?>