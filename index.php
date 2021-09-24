<?php
require 'service/conf.php';
require 'assets/encript/lib.php';
session_start();

$ctroom = ""; $uniqkeys = "";
if(isset($_SESSION['ctroom'])) {
  $ctroom   = $_SESSION['ctroom'];
  $ctfromid = $_SESSION['ctfromid'];
  $ctfromnm = $_SESSION['ctfromnm'];
  $cttoid   = $_SESSION['cttoid'];
  $cttonm   = $_SESSION['cttonm'];
  $ctdate   = date('YmdHis');

  $parms = $ctroom."|*|".$ctfromid."|*|".$ctfromnm."|*|".$cttoid."|*|".$cttonm."|*|".$ctdate;
	$uniqkeys = base64_encode(cryptoJsAesEncrypt($parms));
}
// echo "ctroom $ctroom";
?>
<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Elyphsoft - Chat</title>

  <link rel="stylesheet" href="assets/reset.min.css">
  <link rel="stylesheet" href="assets/bootstrap.min.css" >
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="assets/font-awesome.min.css">
  <link rel="stylesheet" href="assets/icons/css/material-design-iconic-font.min.css">
  <link rel="stylesheet" href="./node_modules/emojionearea/dist/emojionearea.min.css">
  <!-- <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script> -->
</head>
<body>
<!-- partial:index.partial.html -->
<head>
  <meta charset="UTF-8">
  <title>Chat</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    .font-12 {
      font-size:12px;
    }
    .on_block {
      display: block !important;
    }
  </style>
  <style>
.chat_dropbtn {
  background-color: #007bff;
  color: white;
  padding: 16px;
  font-size: 20px;
  border: none;
  cursor: pointer;
}

.chat_dropbtt {
  color: #bbb;
  font-size: 20px;
  border: none;
  cursor: pointer;
  background-color: #fff;
}

.chat_dropbott {
  float: left;
  z-index: 9999;
  position: fixed;
  left: 9px;
  margin-top: 10px;
}

.chat_dropbott_content {
  display: none;
  position: absolute;
  background-color: #fff;
  min-width: 160px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 9999;
  top: -92px;
}

.chat_dropbott_content a {
  color: black;
  padding: 10px 0px 10px 20px;
  text-decoration: none;
  display: block;
}

.chat_dropbott_content a:hover {
  background-color: #f1f1f1
}

.chat_dropbott:hover .chat_dropbott_content {
  display: block;
}

.chat_dropbott:hover .chat_dropbtn {
  background-color: #007bff;
}

.zmdi {
  font-size: 20px;
}

.chat_dropdown {
  position: relative;
  display: block;
  float: right;
  margin: -22px -15px;
  right: 20px;
  top: -10px;
}

.chat_dropdown_content {
  display: none;
  position: absolute;
  background-color: #fff;
  min-width: 210px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 9999;
  right: 20px;
  top: 38px;
}

.chat_dropdown_content a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
}

.chat_dropdown_content a:hover {background-color: #f1f1f1}

.chat_dropdown:hover .chat_dropdown_content {
  display: block;
}

.chat_dropdown:hover .chat_dropbtn {
  background-color: #007bff;
}
</style>
</head>

<body>
<input type="hidden" class="form-control" id="cht_token" autocomplete="off" value="<?=$uniqkeys;?>">
  <div class="fabs">
  <div class="chat is-visible">
    <?php if($ctroom == "") { ?>
    <div class="chat_body chat_login">
      <div class="container">
        <div class="chat_status" id="chat_status">
          <img class="chat_connecting_img" src="assets/images/antenna.png" />
         <sub class="chat_connecting_sub">Connecting...</sub>
        </div>
        <p>Kami siap membantu! Untuk membantu kami melayani Anda dengan lebih baik,
                berikan beberapa informasi sebelum memulai obrolan.</p>
                
        <div class="mb-3">
          <label for="cht_usermail" class="form-label">Alamat email</label>
          <input type="email" class="form-control font-12" id="cht_usermail" value="aji_cahya@elyphsoft" autocomplete="off" required>
        </div>
        <div class="mb-3">
          <label for="cht_usermail" class="form-label">Phone No.</label>
          <input type="text" class="form-control font-12" id="cht_userphone" value="082189102820" autocomplete="off" required>
        </div>
        <div class="mb-3">
          <label for="cht_username" class="form-label">Nama Anda</label>
          <input type="text" class="form-control font-12" id="cht_username" value="Aji Cahya" autocomplete="off" required>
        </div>
        <div class="mb-3">
          <label for="cht_username" class="form-label">Pertanyaan</label>
          <select class="form-control font-12" id="cht_userques" name="cht_userques" required>
            <!-- <option value= "">--Pilih Pertanyaan--</option> -->
            <option value= "Registrasi">Registrasi</option>
            <option value= "Akun Terblokir">Akun Terblokir</option>
            <option value= "Others">Others</option>
          </select>   
        </div>
        <button id="chat_first_screen" type="button" class="btn btn-primary btn-md btn-block">Mulai obrolan</button>
      </div>
    </div>
    <div class="fab_comp_head"></div>
    <div id="chat_converse" class="chat_conversion chat_converse">
      <!-- isi chat one -->
      <div id="chat_hist"></div>
      <div id="chat_hits"></div>
    </div>
    <div class="fab_comp_chat"></div>
    <?php } else { //jika sudah ada ?>
      <div class="chat_header">
          <div class="chat_status" id="chat_status" style="display:none;"></div>
        <div class="chat_option">
          <div class="header_img">
            <img class="agent" src="assets/images/agent-avatar.jpg"/>
            <div class="status_in" id="status_in">
              <img class="chat_inconnecting_img" src="assets/images/antenna.png" />
            </div>
          </div>
          <span id="chat_head">Live Chat</span>
          <br> <span id="chat_title" class="agent">Layanan Pelanggan kami tersedia.</span>
          <div class="chat_menu_speaker" id="chat_menu_speaker" onClick="onSpeaker();">
            <i class="zmdi zmdi-volume-up"></i>
          </div>
          <div class="chat_dropdown">
            <button class="chat_dropbtn"><i class="fullscreen zmdi zmdi-widgets"></i></button>
              <div class="chat_dropdown_content">
              <a href="#" onClick="onEndSession();"><i class="zmdi zmdi-close-circle-o"></i> End the Conversation</a>
              </div>
          </div>
        </div>
      </div>
    <!-- <div class="fab_field">
      <a id="fab_send" class="fab is-visible"><i class="zmdi zmdi-mail-send"></i></a>
    </div> -->
    <div id="chat_converse" class="chat_conversion chat_converse on_block chat_converse2">
      <?php
        $sql = " SELECT a.id, a.direction, a.message_type, a.message_content,
        DATE_FORMAT(a.insert_time, '%H:%i') AS message_time FROM cc_chat_messages a
        WHERE a.session_id = '".$ctroom."' ORDER BY a.id ASC ";
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_assoc($result)) {
          $dir = $row['direction'];
          $msg = $row['message_content'];
          $time = $row['message_time'];
          if($dir == "1") {
            echo '<span class="chat_msg_item chat_msg_item_user">
            '.$msg.'</span>
            <span class="status">'.$time.'</span>';
          } else {
            echo '<span class="chat_msg_item chat_msg_item_admin">
            <div class="chat_avatar">
               <img src="assets/images/agent-avatar.jpg"/>
            </div>'.$msg.'</span>';
          }
        }
      ?>
      <div id="chat_hist"></div>
      <div id="chat_hits"></div>
      <!-- isi chat onblock -->
    </div>
    <div class="fab_field fab_field2">
    <div class="chat_dropbott">
            <button class="chat_dropbtt"><i class="zmdi zmdi-camera"></i></button>
              <div class="chat_dropbott_content">
              <a href="#" onClick="onEndSession();">
              <i class="zmdi zmdi-collection-image-o"></i> &nbsp;Galery</a>
              <a href="#" onClick="onEndSession();">
                <i class="zmdi zmdi-folder-outline"></i> &nbsp;Document</a>
              </div>
          </div>
      <!-- <a id="fab_camera" class="fab is-visible"><i class="zmdi zmdi-camera"></i></a> -->
      <a id="fab_send" class="fab is-visible" onClick="sendChat();" ><i class="zmdi zmdi-mail-send"></i></a>
      <!-- <a id="fab_emoji" class="fab is-visible"><i class="zmdi zmdi-mood"></i></a> -->
      <textarea id="chat_message" name="chat_message" placeholder="Send a message" class="chat_field chat_message"></textarea>
    </div>
    <?php } ?>
  </div>
    <!-- <a id="prime" class="fab"><i class="prime zmdi zmdi-comment-outline"></i></a> -->
</div>
  <script src='assets/jquery-1.11.3.min.js'></script>

</body>
<!-- partial -->
  <script src="assets/jquery.min.js"></script>
  <script src="assets/script.js"></script>
  <script src="assets/encript/cryptojs-aes.min.js"></script>
  <script src="assets/encript/cryptojs-aes-format.js"></script>
  
  <!-- <script src="./node_modules/jquery/dist/jquery.js"></script> -->
  <script src="./node_modules/socket.io/client-dist/socket.io.js"></script>
  <script src="./node_modules/date-and-time/date-and-time.min.js"></script>
  <script src="./node_modules/sweetalert/dist/sweetalert.min.js"></script>
  <script src="./node_modules/emojionearea/dist/emojionearea.min.js"></script>
  <script src="./node_modules/lib/custome.js"></script>
  <script src="./node_modules/base-64/base64.js"></script>
  <script src="nodeClient.js"></script>

  <script type="text/javascript">  
  // Creating object of current date and time 
// by using Date() 
//const now  =  new Date();
  
  // Formating the date and time
  // by using date.format() method
  //const value = date.format(now,'YYYY/MM/DD HH:mm:ss');
    
  // Display the result
  //console.log("current date and time : " + value)

</script>
</body>
</html>
