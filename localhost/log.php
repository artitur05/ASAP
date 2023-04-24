<?php

function _log($str)
{


    $file =
        fopen(__DIR__ . '/log.txt',
        'a+');

    fputs($file,
        print_r($str,1)
        . PHP_EOL);
}




