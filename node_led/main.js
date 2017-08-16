var express = require('express'),
app = express()
, http = require('http')
, server = http.createServer(app)
, io = require('socket.io').listen(server);


var SerialPort = require("serialport").SerialPort
var serialPort = new SerialPort("/dev/ttyACM0", { baudrate: 115200 });

server.listen(8080);
app.use(express.static('public'));		

var brightness = 0;

io.sockets.on('connection', function (socket) {
	socket.on('led', function (data) {
		brightness = data.value;
		
		var buf = new Buffer(1);
		buf.writeUInt8(brightness, 0);
		serialPort.write(buf);
		
		io.sockets.emit('led', {value: brightness});	
	});
	
	socket.emit('led', {value: brightness});
});

console.log("running");
