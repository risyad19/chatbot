// app.js
var date = require('date-and-time');
var express = require('express');
var app = express();
var { exec } = require('child_process');
const kill = require('kill-port');
var server = require('http').createServer(app);
var io = require('socket.io')(server, {
	cors: {
	   origin: "*",
	   methods: ["GET", "POST"]
	}
 });
var port = 8080; // npx kill-port 3001
var connections = [];
// const { curly } = require('node-libcurl');
// const config = require('./include/conn');
const now  =  new Date();
const request = require('request');

app.use(express.static(__dirname + '/node_modules'));
app.use(express.static(__dirname + '/service'));

io.on('connection', function(client) {
	
	client.emit('onsocket', { clientid : client.id });
	connections.push(client);
	// client.join("some room");
	// console.log(client.rooms);
	
    //console.log('---------------------------------------------------------');
    console.log('Klien %s Terhubung : ' +client.id , connections.length);
    console.log('---------------------------------------------------------');

	// connections.push(client);
	
	// request('http://www.google.com', function (error, response, body) {
	// 	console.error('error:', error); // Print the error if one occurred
	// 	console.log('statusCode:', response && response.statusCode); // Print the response status code if a response was received
	// 	console.log('body:', body); // Print the HTML for the Google homepage.
	// });

	// console.log('Terhubung: %s sockets sedang terhubung. client id: '+client.id, connections.length);
	// console.log('Terhubung: %s sockets sedang terhubung.');

		//disconnected
	client.on('disconnect', function(data) {
		connections.splice(connections.indexOf(client), 1);

		//console.log('---------------------------------------------------------');
		console.log('Klien '+client.id+' Terputus.');
		console.log('Total Klien Terhubung %s.', connections.length);
		console.log('---------------------------------------------------------');
	});

    client.on('message_inb', function(data) {
        //    client.emit('broad', data);
        //    client.broadcast.emit('broad',data);
		console.log(data);

		if (data.chatmsg != "") {
			const clmsg = data.chatmsg;
			const clkey = data.ctunikeys;

			request.post({url:'http://localhost/chatbot/service/msgs.php', form: {
				usmsg:clmsg,
				uskey:clkey
			}}, function(err, httpResponse, body){ 
				if(!err) {
					const prms = JSON.parse(body);
						const status = prms.status;
						if(status == "1") {
							const ctchat = prms.ctchat;
							const cttime = prms.cttime;
							client.emit('newmsg', { ctchat : ctchat, cttime : cttime  });
							// io.sockets.in(ctroom).emit('message', "You are in room no. "+ctroom);

						}

					console.log("Kiriman dari Customer : "+body);
					console.log('---------------------------------------------------------');
					// console.log(httpResponse);
				} else {
					console.log(err);
				}
			});
		}
    });
	
    client.on('buatsesi', function(data) { //session new
		const ussoid = client.id;
		const ustokn = data.ustokn;
		const usmail = data.usmail;
		const usphno = data.usphno;
		const usname = data.usname;
		const usques = data.usques;
		// request.post('apps.php', {form:{key:'value'}});
		request.post({url:'http://localhost/chatbot/service/apps.php', form: {
			ussoid: ussoid,
			ustokn:ustokn,
			usmail:usmail,
			usphno:usphno,
			usname:usname,
			usques:usques
		}}, function(err, httpResponse, body){ 
		if(!err) {
			const prms = JSON.parse(body);
				const status = prms.status;
				if(status == "1") {
					const ctmsg = prms.ctmsg;
					const ctroom = prms.ctroom;
					const ctfromid = prms.ctfromid;
					const ctfromnm = prms.ctfromnm;
					const cttoid = prms.cttoid;
					const cttonm = prms.cttonm;
					const cttime = prms.cttime;

					client.emit('simpansesi', { ctroom : ctroom, ctfromid : ctfromid, ctfromnm : ctfromnm,
					 cttoid : cttoid, cttonm : cttonm, ctmsg : ctmsg, cttime : cttime  });
					// io.sockets.in(ctroom).emit('message', "You are in room no. "+ctroom);

				}
			console.log("Registrasi : "+body);
			console.log('---------------------------------------------------------');
			// console.log(httpResponse);
		} else {
			console.log(err);

		}
			
		 });
		
 });

});

// app.get('/', function(req, res,next) {
//     res.sendFile(__dirname + '/index.html');
// });

// server.listen(port);
// const ls = exec('npx kill-port 8080', function (error, stdout, stderr) {
	setTimeout(() => {
		server.listen(port, () => {
			console.log(`Server started port ${port}`);
		 });
	  }, 1000);
	
    
	// Currently you can kill ports running on TCP or UDP protocols
	kill(port, 'tcp')
		.then(console.log)
		.catch(console.log);
		
// });
// var express = require('express');
// var http = require('http');
// // const config = require('./include/conn');
// var port = 8080;
// var app = express();
// var server = http.createServer(app);
// var io = require('socket.io')(server);

// // var io = socket.listen(server); // version old

// // var io = require('socket.io')(port);

// var connections = [];

// io.sockets.on('connection', function(client) {
// 	client.emit('onsocket', { clientid : client.id });
// 	connections.push(client);
// 	console.log('Terhubung: %s sockets sedang terhubung. client id: '+client.id, connections.length);

// 	//disconnected
// 	client.on('disconnect', function(data) {
// 		connections.splice(connections.indexOf(client), 1);
// 		console.log('Terputus: %s sockets sedang terhubung. client id: '+client.id, connections.length);
// 	});
	
// 	//send message
// 	client.on('send message', function(data) { // masuk dari index.php client
// 		//client.join(data.room); //daftarkan room
// 		io.sockets.to(data.room).emit('new message', {user: data.username, msg: data.message, now: data.time}); //only room //ngirim ke client js
// 		// io.sockets.emit('new message', {user: data.username, msg: data.message}); //all user
// 	});

// 	client.on('register', function(data) { // masuk dari index.php client
// 		client.join(data.room); //daftarkan room
// 	});

// 	//send typing
// 	client.on('typing', function(data) { // masuk dari index.php client
// 		client.join(data.room); //daftarkan room
// 		if(data.typing == "on") {
// 			io.sockets.to(data.room).emit('is typing', {user: data.username, status: data.typing}); //only room //ngirim ke client js
// 		} else if(data.typing == "off") {
// 			io.sockets.to(data.room).emit('is typing', {user: data.username, status: data.typing}); //only room //ngirim ke client js
// 		}
// 		// io.sockets.emit('new message', {user: data.username, msg: data.message}); //all user
// 	});

// 	// client.on('message', function(data) {
// 	// 	console.log('Message received ' + data.name + " to " + data.nameto + ":" + data.message);
		
// 	// 	//client.broadcast.emit('message', { name: data.name, message: data.message });
// 	// 	//io.sockets.emit('message', { name: data.name, message: data.message });
// 	// 	//io.sockets.emit('message', { name: data.name, nameto: data.nameto, message: data.message });

// 	// 	client.join("xxx");
//  //    	io.in("xxx").emit("message",  { name: data.name, nameto: data.nameto, message: data.message });

// 	// 	//io.sockets.to(data.nameto).emit('message', { name: data.name, nameto: data.nameto, message: data.message });
// 	// 	//client.broadcast.to(data.nameto).emit('message', { name: data.name, nameto: data.nameto, message: data.message });
// 	// 	//io.sockets.in(data.nameto).emit('message', {name: data.name, nameto: data.nameto, message: data.message });
// 	//     //io.sockets.sockets[data.nameto].emit("message", { name: data.name, nameto: data.nameto, message: data.message });
// 	// 	//client.emit("message", { name: data.name, nameto: data.nameto, message: data.message });
	
// 	// });
// });


// server.listen(8080);
