<?php
$f3 = require('f3.php');
$f3->route('GET /',
    function() use ($f3) {
        $f3->set('name','world');
        echo Template::instance()->render('test.htm');
    }
);
$f3->run();
