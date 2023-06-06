import { Server } from 'socket.io';
import express from 'express';
import { createServer } from 'http';

const app = express();
const server = createServer(app);
const io = new Server(server, {
    cors: {
        origin: '*',
    }
});

const port = process.env.PORT || 8890;
server.listen(port, () => {
    console.log(`Server running at http://localhost:${port}`);
});

io.on('connection', function (socket) {

    console.log("client connected");

    socket.on('message', (message) => {
        io.emit('new-message', message);
    });

    socket.on('disconnect', function() {
        console.log('disconnect');
    });
});


