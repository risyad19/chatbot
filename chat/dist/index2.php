<?php
    require 'encript/lib.php';
?>
<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>CodePen - Chat</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" >
<link rel="stylesheet" href="./style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="icons/css/material-design-iconic-font.min.css">
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
</head>

<body>
<?php
  $token = openssl_random_pseudo_bytes(16);
  $token = bin2hex($token);

session_start();

$mailOn = "";
if(isset($_SESSION['mailOn'])) {
  $mailOn = $_SESSION['mailOn'];
}

$nameOn = "";
if(isset($_SESSION['nameOn'])) {
  $nameOn = $_SESSION['nameOn'];
}

$tokenOn = "";
if(isset($_SESSION['tokenOn'])) {
  $tokenOn = $_SESSION['tokenOn'];
}
?>

  <div class="fabs">
  <div class="chat">
    <?php if($mailOn == "") { ?>
    <div class="chat_header">
      <div class="chat_option">
        <div class="header_img">
          <img src="live-chat.png"/>
        </div>
        <span id="chat_head">Live Chat</span> <br> <span class="agent">Layanan Pelanggan kami tersedia.</span>
        <span id="chat_fullscreen_loader" class="chat_fullscreen_loader"><i class="fullscreen zmdi zmdi-window-maximize"></i></span>
      </div>
    </div>
    <div class="chat_body chat_login">
      <div class="container" style="padding-bottom:20px">
          <input type="text" class="form-control" id="frm_ustoken" autocomplete="off" value="<?=$token;?>">
              <p>Kami siap membantu! Untuk membantu kami melayani Anda dengan lebih baik,
                berikan beberapa informasi sebelum memulai obrolan.</p>
                
        <div class="mb-3">
          <label for="frm_usermail" class="form-label">Alamat email</label>
          <input type="email" class="form-control font-12" id="frm_usermail" autocomplete="off" required>
        </div>
        <div class="mb-3">
          <label for="frm_username" class="form-label">Nama Anda</label>
          <input type="text" class="form-control font-12" id="frm_username" autocomplete="off" required>
        </div>
        <button id="chat_first_screen" type="button" class="btn btn-primary btn-md btn-block">Mulai obrolan</button>
      </div>
    </div>
    <div id="chat_converse" class="chat_conversion chat_converse">
      <span class="chat_msg_item chat_msg_item_admin">
            <div class="chat_avatar">
               <img src="agent-avatar.jpg"/>
            </div>Hey there! Any question?</span>
      <span class="chat_msg_item chat_msg_item_user">
            Hello!</span>
            <span class="status">20m ago</span>
      <span class="chat_msg_item chat_msg_item_admin">
            <div class="chat_avatar">
               <img src="agent-avatar.jpg"/>
            </div>Hey! Would you like to talk sales, support, or anyone?</span>
      <span class="chat_msg_item chat_msg_item_user">
            Lorem Ipsum is simply dummy text of the printing and typesetting industry.</span>
             <span class="status2">Just now. Not seen yet</span>
    </div>
    <div class="fab_comp_chat"></div>
    <?php } else { ?>
    <div class="chat_header">
      <div class="chat_option">
        <div class="header_img">
          <img class="agent" src="agent1.png"/>
        </div>
        <span id="chat_head">Dika Ananda</span> <br> <span class="agent">Agent Customer Service</span>
        <span id="chat_fullscreen_loader" class="chat_fullscreen_loader"><i class="fullscreen zmdi zmdi-window-maximize"></i></span>
      </div>
    </div>
    <div id="chat_converse" class="chat_conversion chat_converse on_block">
      <span class="chat_msg_item chat_msg_item_admin">
            <div class="chat_avatar">
               <img src="agent1.png"/>
            </div>Hey there! Any question?</span>
      <span class="chat_msg_item chat_msg_item_user">
            Hello!</span>
            <span class="status">20m ago</span>
      <span class="chat_msg_item chat_msg_item_admin">
            <div class="chat_avatar">
               <img src="agent1.png"/>
            </div>Hey! Would you like to talk sales, support, or anyone?</span>
      <span class="chat_msg_item chat_msg_item_user">
            Lorem Ipsum is simply dummy text of the printing and typesetting industry.</span>
             <span class="status2">Just now. Not seen yet</span>
    </div>
    <div class="fab_field">
      <a id="fab_camera" class="fab"><i class="zmdi zmdi-camera"></i></a>
      <a id="fab_send" class="fab"><i class="zmdi zmdi-mail-send"></i></a>
      <textarea id="chatSend" name="chat_message" placeholder="Send a message" class="chat_field chat_message"></textarea>
    </div>
    <?php } ?>
  </div>
    <a id="prime" class="fab"><i class="prime zmdi zmdi-comment-outline"></i></a>
</div>
  <script src='jquery-1.11.3.min.js'></script>

</body>
<!-- partial -->
  <script src='jquery.min.js'></script>
  <script  src="./script.js"></script>
  <script  src="encript/cryptojs-aes.min.js"></script>
  <script  src="encript/cryptojs-aes-format.js"></script>

  <script>
 $('#chat_first_screen').click(function(e) {
        let ustokn = $("#frm_ustoken" ).val();
        let usmail = $("#frm_usermail" ).val();
        let usname = $("#frm_username" ).val();

        let formData = {token: ustokn, mail: usmail, name: usname};

        $.ajax({
              url : "chat_act.php",
              type: "POST",
              data : formData,
              success: function(data, textStatus, jqXHR) {
                var js  = JSON.parse(data);
                var stt = js['data'][0]['msg'];
                var ty  = js['data'][0]['type'];
                if(ty == "1") {

                  hideChat(1);
                  var comChat = "<div class='fab_field'>"+
                    "<a id='fab_camera' class='fab is-visible'>"+"<i class='zmdi zmdi-camera'>"+"</i>"+"</a>"+
                    "<a id='fab_send' class='fab is-visible'>"+"<i class='zmdi zmdi-mail-send'>"+"</i>"+"</a>"+
                    "<textarea id='chatSend' name='chat_message' placeholder='Send a message' class='chat_field chat_message'>"+"</textarea>"+
                    "</div>";
                  $( "div.fab_comp_chat" ).replaceWith(comChat);

                }

              },
              error: function (jqXHR, textStatus, errorThrown) {
        
              }
        });
  });   
  </script>
</body>
</html>
