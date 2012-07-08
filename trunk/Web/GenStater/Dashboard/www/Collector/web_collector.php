<?php
    include_once("class_event.php");
    
    $event = new Event();
    $event->initDefault();
    
    if ($event->store()) echo "OK";
    else echo "ERROR";
?>
