const mysql = require('mysql');

const connection = mysql.createConnection({
    host: '192.168.0.69',
    user: 'es',
    password: '0218Galunggung',
    database: 'db_cwbank_new'
});

connection.connect((err) => {
    if (err) throw err;
    console.log('Connected!');
});

module.exports= connection;