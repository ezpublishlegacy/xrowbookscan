<?php

$Module = array( 'name' => 'Book scan preview' );

$ViewList = array();
$ViewList['view'] = array( 'functions' => array( 'view' ),
                           'script' => 'view.php',
                           'params' => array( 'NodeID' ) );

$FunctionList = array();