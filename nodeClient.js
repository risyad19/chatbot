const socket = io.connect("http://192.168.0.107:8080");

let timer;              // Timer identifier
const waitTime = 500;   // Wait time in milliseconds 

var clid = "";
var clconn = "0";

$(".chat_field.chat_message").each(function () {
	this.setAttribute("style", "height:" + (this.scrollHeight) + "px;overflow-y:hidden;");
  }).on("input", function () {
	this.style.height = "auto";
	this.style.height = (this.scrollHeight) + "px";
  });

function sendChat() {
	var newmsg = document.getElementById("chat_message").value;
	if(newmsg != "" && newmsg != "\n" && clconn == "1") {
		var ctunikeys = localStorage.getItem('ctunikeys');
		socket.emit('senMessage', { chatmsg: newmsg, ctunikeys: ctunikeys });
		document.getElementById("chat_message").value = "";
        $(".emojionearea-editor").html('');
	} else {
		//isi chat dikirim kosong!
	}
}

let speak = localStorage.getItem('ctspeaker');

var stspk = document.getElementById("chat_menu_speaker");
if(typeof(stspk) != 'undefined' && stspk != null){
	if(speak == "") {
		document.getElementById("chat_menu_speaker").innerHTML = "<i class='zmdi zmdi-volume-up'></i>";
	} else if(speak == "0") {
		document.getElementById("chat_menu_speaker").innerHTML = "<i class='zmdi zmdi-volume-off'></i>";
	} else if(speak == "1") {
		document.getElementById("chat_menu_speaker").innerHTML = "<i class='zmdi zmdi-volume-up'></i>";
	}
}

var stin = document.getElementById("status_in");
  if(typeof(stin) != 'undefined' && stin != null){
    $('div.emojionearea-editor').attr('id', 'emojionearea-editor');
    $("#chat_message").emojioneArea({
      search: false,
      autocomplete: false,
		events: {
			keypress: function (editor, event) {
				$('div.emojionearea-editor').attr('id', 'emojionearea-editor');
				if(event.which == 13){
					// console.log(this.getText());
					// console.log(editor.html());
					// $(".emojionearea").removeClass("focused");
					var newmsg = editor.html(); //this.getText();
					
					if(newmsg != "" && newmsg != "\n" && clconn == "1") {
						var ctunikeys = localStorage.getItem('ctunikeys');
						socket.emit('senMessage', { chatmsg: newmsg, ctunikeys: ctunikeys });
						
						setTimeout(function() {
							$("div.emojionearea-editor").html('');
							$("#chat_message").val(editor.html(''));
							$("#emojionearea-editor").data("emojioneArea").setText('');
						}, 100);
					}
				} else {
					socket.emit('isTyping', {
						typing: true
					});
					// Clear timer
					clearTimeout(timer);
	
					// Wait for X ms and then process the request
					timer = setTimeout(() => {
						socket.emit('isTyping', {
							typing: false
						});
					}, waitTime);
				}
			}
		}
    });
  }

socket.on('connect', function(data) {
    
	const active = onId();
	if(active != "") {
		const deactive = base64.decode(active);
		let decract = CryptoJSAesJson.decrypt(deactive, '');
		socket.emit('joinRoom', {
			sto: decract
		});
	}
	
	clconn = "1";
	var stin = document.getElementById("status_in");
	if(typeof(stin) != 'undefined' && stin != null){
		var comStatus = "<img class='chat_inconnected_img' src='assets/images/wifi.png' />";
		document.getElementById("status_in").innerHTML = comStatus;
	} else {
		var comStatus = "<img class='chat_connected_img' src='assets/images/wifi.png' />"+
		" <sub class='chat_connected_sub'>Connected...</sub>";
		document.getElementById("chat_status").innerHTML = comStatus;
	}
    
	clid = socket.id;
});

socket.on("connect_error", (err) => {  
	 //console.log(`connect_error due to ${err.message}`);
	clconn = "0";
	var stin = document.getElementById("status_in");
	if(typeof(stin) != 'undefined' && stin != null){
		var comStatus = "<img class='chat_inconnecting_img' src='assets/images/antenna.png' />";
		document.getElementById("status_in").innerHTML = comStatus;
	} else {
		var comStatus = "<img class='chat_connecting_img' src='assets/images/antenna.png' />"+
	 " <sub class='chat_connecting_sub'>Connecting...</sub>";
	//  $("span.chat_status").replaceWith(comStatus);
		document.getElementById("chat_status").innerHTML = comStatus;
	}
});

// socket.on("disconnecting", () => {
// 	//console.log(socket.rooms);
// });

socket.on("disconnect", (reason) => {
	clconn = "0";
	var stin = document.getElementById("status_in");
	if(typeof(stin) != 'undefined' && stin != null){
		var comStatus = "<img class='chat_inconnecting_img' src='assets/images/antenna.png' />";
		document.getElementById("status_in").innerHTML = comStatus;
	} else {
		var comStatus = "<img class='chat_connecting_img' src='assets/images/antenna.png' />"+
		" <sub class='chat_connecting_sub'>Connecting...</sub>";
		document.getElementById("chat_status").innerHTML = comStatus;
	}
});

socket.on('savSession', function(data) {
	
	let value = data.ctroom+"|*|"+data.ctfromid+"|*|"+data.ctfromnm+"|*|"+
	data.cttoid+"|*|"+data.cttonm+"|*|"+data.ctmsg+"|*|"+data.cttime; 
	let password = '';
	let encr = CryptoJSAesJson.encrypt(value, password);
	let parm = { parms : encr }

	let keys = data.ctroom+"|*|"+data.ctfromid+"|*|"+data.ctfromnm+"|*|"+
	data.cttoid+"|*|"+data.cttonm;
	let keysencr = CryptoJSAesJson.encrypt(keys, password);
	localStorage.setItem('ctunikeys', keysencr);
	
	$.ajax({
			url : "service/sess.php",
			type: "POST",
			data : parm,
			success: function(data, textStatus, jqXHR) {
			var js  = JSON.parse(data);
			var stt = js['status'];
			var ctchat = js['ctchat'];
			var cttime = js['cttime'];
			if(stt == "1") {
				//masuk ruangan
				// socket.emit('masukRoom', { username, room });
				//openChat
				openChat(ctchat, cttime);
			}

			},
			error: function (jqXHR, textStatus, errorThrown) {
	
			}
	});

	// console.log(data);
});

$('#chat_first_screen').click(function(e) {
	e.preventDefault();
	let usmail = $("#cht_usermail").val();
	let usphno = $("#cht_userphone").val();
	let usname = $("#cht_username").val();
	let usques = $("#cht_userques").val();
	socket.emit('crtSession', { 
		usmail: usmail, 
		usphno: usphno, 
		usname: usname, 
		usques: usques 
	} );
});

// Message from server
socket.on('message', (message) => {

	const ctchat = message.ctchat;
	const cttime = message.cttime;
	const ctdirc = message.ctdirc;
	if(ctdirc == "1") {
		var msg = '<span class="chat_msg_item chat_msg_item_user">'+
		ctchat+'</span>'+
		'<span class="status">'+cttime+'</span>';
		$("#chat_hist").append(msg);
	} else {
		var msg = '<span class="chat_msg_item chat_msg_item_admin">'+
		'<div class="chat_avatar">'+
		'<img src="assets/images/agent-avatar.jpg"/>'+
		'</div>'+$ctchat+'</span>';
		$("#chat_hist").append(msg);
	}
	var elmt = document.getElementById("chat_converse");
	elmt.scrollTop = elmt.scrollHeight;
});

socket.on('styping', (message) => {
	const ctid = message.ctid;
	const clstat = message.clstat;
	if(clstat == true) {
		if(socket.id !== ctid) {
			var msg = ctid+' is Typing......';
			$("#chat_title").text(msg);
		}
	} else {
		$("#chat_title").text('Layanan Pelanggan kami tersedia.');
	}
});

socket.on('onsocket', function (data) {
	// console.log('client onsocket !');
});
