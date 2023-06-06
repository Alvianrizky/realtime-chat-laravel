// import { Server } from 'socket.io';
// import express from 'express';
// import { createServer } from 'http';
// import redis from 'redis';

// const app = express();
// const server = createServer(app);
// const io = new Server(server);

// server.listen(8890);
// io.on('connection', function (socket) {

//     console.log("client connected");
//     var redisClient = redis.createClient();
//     redisClient.subscribe('message');

//     redisClient.on("message", function(channel, data) {
//         socket.emit(channel, data);
//     });

//     socket.on('disconnect', function() {
//         redisClient.quit();
//     });
// });

import { Server } from 'socket.io';
import express from 'express';
import { createServer } from 'http';
import redis from 'redis';

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
    // var redisClient = redis.createClient();
    // redisClient.subscribe('message');
    // console.log(redisClient);

    socket.on('message', (message) => {
        io.emit('new-message', message);
    });

    // redisClient.on("message", function(channel, data) {
    //     socket.emit(channel, data);
    //     console.log(data);
    // });

    socket.on('disconnect', function() {
        // redisClient.quit();
        console.log('disconnect');
    });
});


