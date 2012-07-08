<?php
include_once("../Common/sys_config.php");
include_once("../Common/sys_db.php");

$dataStr = base64_decode($_POST["id"]);
$data = json_decode($dataStr);
$value = $_POST["value"];
$proc = $_POST["proc"];
$size = $_POST["size"];
$user = $_SERVER['REMOTE_USER'];

{
	// log this event:
	include_once("../Collector/class_event.php");
	$event = new Event();
	$event->setValue("event", "sysLog");
	$event->setValue("d_ext_sysRemoteAddr", $_SERVER["REMOTE_ADDR"]);
	$event->setValue("d_ext_user", $user);
	$event->setUseTransaction(false);
	// end of log event
}


$sql = "CALL proc_$proc" . "_Submit (:VALUE,";
for ($i = 0 ; $i < $size ; $i++ ) {
	if ($i>0) {
		$sql .= ",";
	}
	$sql .= ":ARG" . $i;
}
$sql .= ")";

$stmt = $dbh->prepare($sql);

$stmt->bindValue(":VALUE", $value);

for ($i = 0 ; $i < $size ; $i++ ) {
	$stmt->bindValue(":ARG".$i, $data->{$i});
}

if ($stmt->execute()) {
	$arr = $stmt->fetch();
	echo $arr[0];

	{
		// log this event:
		$event->setValue("d_ext_operation", "saveValue");
		$event->setValue("d_ext_value", $value);
		$event->setValue("d_text_data", $dataStr);
		$event->store();
		// end of log event
	}

} else {
	echo "<pre>ERROR executing query \n$sql\n : \n";
	print_r($stmt->errorInfo());
	echo "</pre>";
}
/*
 echo "<pre>";
 echo "$sql\n";
 echo sizeof($data->{0}) . "\n";
 echo "value: $value\n";
 echo "proc: $proc\n";
 echo "size: $size\n";
 print_r($data);
 echo "</pre>";
 */
?>