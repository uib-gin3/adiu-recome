<?php
// Connection settings for the MySQL database and the source of the application
// Change the host to localhost when the project runs on XAMPP

return [
    'host' => 'db',
    'name' => 'recome',
    'user' => 'recome',
    'pass' => 'recome',
    'source' => getenv('RECOME_SOURCE_URL') ?: 'https://github.com/uib-gin3/adiu-recome'
];
