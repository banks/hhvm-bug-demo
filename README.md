= HHVM 3.0.1 FastCGI Error Demo =

To support a ticket I'm about to file for https://github.com/facebook/hhvm

=== Instructions ===

To see the working case first run php-fpm locally from this directory with port 9000.

You should get:

```
$ php test_hhvm_fastcgi.php
PHP message: Saying Hello to World
X-Powered-By: PHP/5.4.24
Content-type: text/html

Hello World!

PHP message: Saying Hello to World
X-Powered-By: PHP/5.4.24
Content-type: text/html

Hello World!

```

Now run HHVM 3.0.1 with following params (kill php-fpm if you ran it):

`hhvm --mode server -vServer.Type=fastcgi -vServer.Port=9000`

I see:

```
$ php ./test_hhvm_fastcgi.php
X-Powered-By: HHVM/3.0.1
Content-Type: text/html; charset=utf-8

Hello World!


Fatal error: Uncaught exception 'Adoy\FastCGI\ForbiddenException' with message 'Not in white list. Check listen.allowed_clients.' in /www/hhvmtest/FastCGI.php on line 554

Adoy\FastCGI\ForbiddenException: Not in white list. Check listen.allowed_clients. in /www/hhvmtest/FastCGI.php on line 554

Call Stack:
    0.0007     634896   1. {main}() /www/hhvmtest/test_hhvm_fastcgi.php:0
    0.0057     885744   2. Adoy\FastCGI\Client->request() /www/hhvmtest/test_hhvm_fastcgi.php:27
    0.0058     886672   3. Adoy\FastCGI\Client->wait_for_response() /www/hhvmtest/FastCGI.php:420
```

Note that the obscure error message is due to a bad assumption in the client lib about why we can't read any data from the socket after second request.

If you catch that and go on to make a 3rd request you will find Exception on `fwrite()` because HHVm has now closed the socket.

This code worked on HHVM 2.x
