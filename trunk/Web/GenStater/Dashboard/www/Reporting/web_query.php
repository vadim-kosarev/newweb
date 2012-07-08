<?php
include_once("../Common/sys_config.php");
include_once("../Common/sys_utils.php");

// -------------- Query ID -----------------------
$queryID = 1;
if (isset($_GET["id"]))
$queryID = $_GET["id"];

// -------------- Report Builder Class -----------------------
$reportBuilderClass = "DefaultReportBuilder";
if (isset($_GET["reportBuilderClass"]))
$reportBuilderClass = $_GET["reportBuilderClass"];

include_once("classes/" . $reportBuilderClass . ".php");

$reportBuilderObject = null;
eval("\$reportBuilderObject = new " . $reportBuilderClass . "();");

// ----------------------------- Initialize Query ------------------------------------
$stmt = $dbh->prepare("SELECT * FROM sys_queries WHERE query_id = :queryID");
$stmt->bindValue(":queryID", $queryID);

$qSQL = "";
$qName = "";
$qOrderBy = "";
$chartColumns = "";
$xAxis = "";
$dArr = array();
$whereClause = "";


if ($stmt->execute()) {
	$dArr = $stmt->fetch();
	$qSQL = $dArr["sql"];
	$qName = $dArr["name"];
	$qOrderBy = $dArr["orderby"];
	$chartColumns = $dArr["chart_columns"];
	$xAxis = $dArr["chart_x_axis"];
	$whereClause = $dArr["where"];
}

$reportBuilderObject->printPageHeader($stmt, $dArr);

// populate with GET parameters

if (isset($_GET["_orderby"])) {
	$qOrderBy = $_GET["_orderby"];
}

$whereClauseEmpty = true;

if ($whereClause) {
	// if we read it from DB Table
	$whereClause = " WHERE \n ( " . $whereClause . " ) ";
	$whereClauseEmpty = false;
}

foreach (array_keys($_GET) as $key) {
	$arr = array();
	if (preg_match("/(sql_)(.+)/i", $key, $arr)) {
		if (!isset($_GET[$key]) || $_GET[$key] == "")
		continue;
		$val = $_GET[$key];
		$p = paramGetToSQL($arr[2]);
		$p = findFieldByAlias($p, $qSQL);
		if ($whereClauseEmpty) {
			$whereClause .= " WHERE \n";
		}
		if (!$whereClauseEmpty) {
			$whereClause .= " AND \n";
		}

		$valSrc = $val;
		$vals = preg_split("/\,/", $valSrc);
		$isOrAdding = false;
		$valsWhereAdd = "";

		foreach ($vals as $val) {

			$arr = array();
			$whereAdd = "";

			$isNegative = false;
			if (startsWith($val, '!')) {
				$val = substr($val, 1);
				$isNegative = true;
			}


			$whereAdd .= $p . " = '" . $val . "'";

			if (strpos($val, "*") !== false) {
				$val = str_replace("*", "%", $val);
				$whereAdd = $p . " LIKE '" . $val . "'";
			} else if (preg_match("/(.+)\.\.\.(.+)/", $val, $arr)) {
				$whereAdd = $p . " >= '" . $arr[1] . "' AND " . $p . " <= '" . $arr[2] . "'";
			} else if (preg_match("/(.+)\.\.\./", $val, $arr)) {
				$val = $arr[1];
				$whereAdd = $p . " >= '" . $val . "'";
			} else if (preg_match("/\.\.\.(.+)/", $val, $arr)) {
				$val = $arr[1];
				$whereAdd = $p . " <= '" . $val . "'";
			}

			if ($isNegative) {
				$whereAdd = " NOT ( " . $whereAdd . " ) ";
			}

			$valsWhereAdd .= ($isOrAdding?" OR ":"") . $whereAdd;

			$isOrAdding = true;
		}


		$whereClause .= "(" . $valsWhereAdd . ")";
		$whereClauseEmpty = false;
	}
}

// ORDER BY
$orderByAdd = "";
if (strlen($qOrderBy) > 0) {
	$orderByAdd = " ORDER BY " . $qOrderBy;
}

// LIMIT
$limitAdd = "";
if (isset($_GET["limit"])) {
	if ($_GET["limit"] != "no") {
		$limitAdd = " LIMIT " . $_GET["limit"] . " ";
	}
} else {
	$limitAdd = " LIMIT 0,$reportDefaultLimit ";
}


$qSQL = $qSQL . $whereClause . "\n" . $orderByAdd . "\n" . $limitAdd;

// echo "<pre>" . $qSQL . "</pre>";

$stmt = $dbh->prepare($qSQL);

// execute and serialize results
if ($stmt->execute()) {
	$reportBuilderObject->processStmt($stmt, $dArr);
} else {
	echo "Error during execute query";
	echo "<pre>" . $qSQL . "</pre>";
}
?>
