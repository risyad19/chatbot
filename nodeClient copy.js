const socket = io.connect("http://localhost:8080");
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
		socket.emit('message_inb', { chatmsg: newmsg, ctunikeys: ctunikeys });
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

function onSpeaker() {
	let onspeak = localStorage.getItem('ctspeaker');
	if(onspeak == "1") {
		document.getElementById("chat_menu_speaker").innerHTML = "<i class='zmdi zmdi-volume-off'></i>";
		localStorage.setItem('ctspeaker', "0");
	} else {
		document.getElementById("chat_menu_speaker").innerHTML = "<i class='zmdi zmdi-volume-up'></i>";
		localStorage.setItem('ctspeaker', "1");
	}
}

function onEndSession() {
	// alert('end session');
	swal({
		title: "Are you sure?",
		text: "Are you sure you want to end this conversation?",
		icon: "warning",
		dangerMode: true,
	  })
	  .then(willYes => {
		if (willYes) {
		let encr = localStorage.getItem('ctunikeys');
	
		let parm = { parms : encr }
		  $.ajax({
			url : "service/unsess.php",
			type: "POST",
			data : parm,
			success: function(data, textStatus, jqXHR) {
			var js  = JSON.parse(data);
			var stt = js['status'];
			if(stt == "1") {
				swal("end!", "You have successfully ended the conversation!", "success");
		  		localStorage.removeItem("ctunikeys");
				localStorage.removeItem("ctspeaker");
				location.reload();
			}

			},
			error: function (jqXHR, textStatus, errorThrown) {
	
			}
	});
		}
	  });
}

var stin = document.getElementById("status_in");
  if(typeof(stin) != 'undefined' && stin != null){
    $('div.emojionearea-editor').attr('id', 'emojionearea-editor');
    $("#chat_message").emojioneArea({
      search: false,
      autocomplete: false,
		events: {
			keypress: function (editor, event) {
				console.log("is tyiping... "+event.which);
				$('div.emojionearea-editor').attr('id', 'emojionearea-editor');
				if(event.which == 13){
					// console.log(this.getText());
					// console.log(editor.html());
					// $(".emojionearea").removeClass("focused");
					var newmsg = editor.html(); //this.getText();
					
					if(newmsg != "" && newmsg != "\n" && clconn == "1") {
						var ctunikeys = localStorage.getItem('ctunikeys');
						socket.emit('message_inb', { chatmsg: newmsg, ctunikeys: ctunikeys });
						
						setTimeout(function() {
							$("div.emojionearea-editor").html('');
							$("#chat_message").val(editor.html(''));
							$("#emojionearea-editor").data("emojioneArea").setText('');
						}, 100);
					}
				}
			}
		}
    });
  }

socket.on('connect', function(data) {
    // socket.emit('join', 'Hello World from client');
	// console.log(data);
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

socket.on('svSession', function(data) {
	
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
				openChat();
			}

			},
			error: function (jqXHR, textStatus, errorThrown) {
	
			}
	});

	// console.log(data);
});

socket.on('newmsg', function(data) {
	const now  =  new Date();

	let ctchat = data.ctchat;
	let cttime = data.cttime;
	
	var msg = '<span class="chat_msg_item chat_msg_item_user">'+
	ctchat+'</span>'+
	'<span class="status">'+cttime+'</span>';
		// document.getElementById("chat_hist").innerHTML = msg;
		$("#chat_hist").append(msg);
	var elmt = document.getElementById("chat_converse");
	elmt.scrollTop = elmt.scrollHeight;
});

$('#chat_first_screen').click(function(e) {
	e.preventDefault();
	// let lusmail = $("#cht_usermail" ).val();
	// let lusphno = $("#cht_userphone" ).val();
	// let lusname = $("#cht_username" ).val();
	// let lusques = $("#cht_userques" ).val();
	// socket.emit('crtSession', {
	// 	usmail: lusmail, 
	// 	usphno: lusphno, 
	// 	usname: lusname, 
	// 	usques: lusques 
	// });
	// let lusmail = $("#cht_usermail" ).val();
	// let lusphno = $("#cht_userphone" ).val();
	// let lusname = $("#cht_username" ).val();
	// let lusques = $("#cht_userques" ).val();
	socket.emit('creatSession', {
		usmail: "Ssss"
	});
});

socket.on('onsocket', function (data) {
	// console.log('client onsocket !');
	// document.getElementById('chat_id').value = socket.id;
	// document.getElementById('chat_status').value = "1";
    // document.getElementById('view_status').innerHTML = "Anda Terhubung ke <b style='color: #007bff;'>Agent</b>.";
    // document.getElementById("chat_pict").style.backgroundImage = "url('customer-service.png')";
    // document.getElementById("chat_menu").style.display = "block";

    // var chatsesi = document.getElementById('chat_session').value;
    // socket.emit('register', {room: "A"}); //ini ke server untuk registrasi
});
