<?php

require(__DIR__.'/FastCGI.php');

// This demonstrates HHVM failing to accept multple FCGI requests on same socket
// this code used to work in version 2.X but is broken for me in HHVM 3.0.1

$fcgi = new Adoy\FastCGI\Client('localhost', 9000);

$fcgi->setKeepAlive(true);
$fcgi->setPersistentSocket(true);

$params = array(
    // HHVM 3.0.1 requires absolute url regardles of Server.SourceRoot
    'SCRIPT_FILENAME' => realpath(__DIR__).'/echo.php',
    'REQUEST_METHOD'  => 'POST',
    'QUERY_STRING'    => '',
    'CONTENT_TYPE'    => 'text/plain',
    'SCRIPT_NAME'     => '/echo.php',
    'REQUEST_URI'     => '/echo.php',  
);

// This works assuming HHVM is running on localhost
echo $fcgi->request($params, "")."\n\n";

// This request fails to read response (and isn't processed by HHVM 
// - no log output) note that the actual exception thrown here is
// Not in white list. Check listen.allowed_clients. which is just the
// assumption made by this client lib about why it has no bytes of response 
// to read from socket.
echo $fcgi->request($params, "")."\n\n";