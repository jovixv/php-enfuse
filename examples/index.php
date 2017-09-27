<?php

require "../vendor/autoload.php";

use Enfuse\Enfuse;

    $enfuse = new Enfuse([
         'version'=>false,
         'exposure-sigma'=>0.25,
         '-g' => true,
         '-b' => 2048
    ]);


    $enfuse->setDownloadPath('C:\Users\Ленин\Desktop\enfuse\bin');
    $enfuse->setBinPath('C:\Users\Ленин\Desktop\enfuse\bin\enfuse');
    $enfuse->debugOn();

    $enfuse->startEnfuse();

?>