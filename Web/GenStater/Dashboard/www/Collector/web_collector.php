<?php
    include_once("class_event.php");
    
    $event = new Event();
    $dArray = array_merge($_GET, $_POST);
    $dArray["d_ext_sysRemoteAddr"] = $_SERVER["REMOTE_ADDR"];
    $event->init($dArray);
    
    if ($event->store()) echo "OK";
    else echo "ERROR";
?>
