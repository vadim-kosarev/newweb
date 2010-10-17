<?php
    include_once("class_event.php");
    $event = new Event();
    $event->init($_GET);
    $event->store();
?>OK
