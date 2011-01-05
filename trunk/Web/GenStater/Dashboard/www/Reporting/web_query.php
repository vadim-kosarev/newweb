<?php
include_once("../Common/sys_config.php");
include_once("../Common/sys_utils.php");

$dbUrl = "mysql:host=$config_db_host;dbname=$config_db_name;port=$config_db_port";
$dbh = new PDO($dbUrl, $config_db_user, $config_db_password);

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

if ($stmt->execute()) {
    $dArr = $stmt->fetch();
    $qSQL = $dArr["sql"];
    $qName = $dArr["name"];
    $qOrderBy = $dArr["orderby"];
    $chartColumns = $dArr["chart_columns"];
    $xAxis = $dArr["chart_x_axis"];
}

$reportBuilderObject->printPageHeader($stmt, $dArr);

// populate with GET parameters

if (isset($_GET["_orderby"])) {
    $qOrderBy = $_GET["_orderby"];
}

$whereClause = "";
$whereClauseEmpty = true;
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
$limitAdd = " LIMIT 0,10000 ";


$qSQL = $qSQL . $whereClause . "\n" . $orderByAdd . "\n" . $limitAdd;
?>





<!-- <?= $qSQL ?> -->





<?php
$stmt = $dbh->prepare($qSQL);

// execute and serialize results
if ($stmt->execute()) {        
	$reportBuilderObject->processStmt($stmt, $dArr);
}	
?>
