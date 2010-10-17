<pre>
<?php
include_once("class_event.php");
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

// =====================================================================================================================
$e = new Event();

$e->selfTest();

$e->init($_GET);

$e->createTable("data_events_ProcessEvent", array("id", "d_int_pid", "d_str_path"));

?>
</pre>