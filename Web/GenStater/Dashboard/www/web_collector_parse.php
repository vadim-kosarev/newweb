<?php
// http://localhost:8080/web_collector_parse.php?a=event:fileEvent;d_str_name:aaaabbbbccccc;d_int_pid:444
    include_once("class_event.php");

    $arg = $_GET["a"];
    $pArrayStr = preg_split("/;/", $arg);
    $dArray = array();
    foreach($pArrayStr as $sArg) {
        $keyValue = preg_split("/:/", $sArg);
        $dArray[$keyValue[0]] = $keyValue[1];
    }
    
    $event = new Event();    
    $dArray["d_ext_sysRemoteAddr"] = $_SERVER["REMOTE_ADDR"];
    $event->init($dArray);
    
    if ($event->store()) echo "OK";
    else echo "ERROR";
?>