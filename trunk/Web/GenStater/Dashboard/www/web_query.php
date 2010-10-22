<?php
include_once("sys_config.php");
include_once("sys_utils.php");

$dbUrl = "mysql:host=$config_db_host;dbname=$config_db_name;port=$config_db_port";
$dbh = new PDO($dbUrl, $config_db_user, $config_db_password);

$queryID = 1;
if (isset($_GET["id"]))
    $queryID = $_GET["id"];

$stmt = $dbh->prepare("SELECT * FROM sys_queries WHERE query_id = :queryID");
$stmt->bindValue(":queryID", $queryID);

$qSQL = "";
$qName = "";
$qOrderBy = "";
$chartColumns = "";
$xAxis = "";

if ($stmt->execute()) {
    $dArr = $stmt->fetch();
    $qSQL = $dArr["sql"];
    $qName = $dArr["name"];
    $qOrderBy = $dArr["orderby"];
    $chartColumns = $dArr["chart_columns"];
    $xAxis = $dArr["chart_x_axis"];
}
?>




<div class="sqlTableHeader"><?= $qName ?></div>




<?php
// populate with GET parameters

if (isset($_GET["orderby"])) {
    $qOrderBy = $_GET["orderby"];
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
$limitAdd = " LIMIT 0,1000 ";


$qSQL = $qSQL . $whereClause . "\n" . $orderByAdd . "\n" . $limitAdd;
?>





<!-- <?= $qSQL ?> -->





<?php
$stmt = $dbh->prepare($qSQL);

// execute and serialize results
$odd = 1;
if ($stmt->execute()) {
    $cCount = $stmt->columnCount();
?>
    <table>
        <tr>
            <td>
                <form action="<?= $_SERVER['REQUEST_URI'] ?>">
                    <input type="hidden" name="id" value="<?= $queryID ?>"/>
                    <table>
                    <?php
                    for ($i = 0; $i < $cCount; $i++) {
                        $cMeta = $stmt->getColumnMeta($i);
                        $cMetaName = $cMeta["name"];
                        $formParamName = "sql_$cMetaName";
                        $formParamValue = "";
                        if (isset($_GET[$formParamName])) {
                            $formParamValue = $_GET[$formParamName];
                        }
                        echo "<tr><td>" . $formParamName . "</td>";
                        echo "<td><input name='" . my_encode($formParamName) .
                        "' type='text' value='$formParamValue'/></td></tr>\n";
                    }
                    ?>
                    <tr><td>ORDER BY</td><td><input name="orderby" value="<?= $qOrderBy ?>"/></td></tr>
                </table>
                <input type="submit"/>
                <pre class="hint">
HINT:
sql_X : VALUE     =>   X = 'VALUE'
sql_X : VAL*STR   =>   X LIKE "VAL%STR"
sql_X : V1...     =>   X >= "V1"
sql_X : ...V2     =>   X <= "V2"
sql_X : V1...V2   =>   X >= "V1" AND X <= "V2"

sql_X : RRR,YYY   =>   (X [=|LIKE|>=|<=] RRR OR X [=|LIKE|>=|<=] YYY)
                </pre>
            </form>
        </td>
        <td>
            <?php if ($chartColumns) { ?>
            <img src="png_pChart.php?ts=<?= time() ?>&data=<?=$chartColumns?>&xAxis=<?=$xAxis?>&sql=<?= urlencode($qSQL) ?>"/>
            <?php } ?>
            <pre class="sql"><?= $qSQL ?></pre></td>
    </tr></table>
<table class="sqlData">
    <?php
                    // columns headers
                    echo "<tr class='sqlDataHeader'>\n";
                    for ($i = 0; $i < $cCount; $i++) {
                        $cMeta = $stmt->getColumnMeta($i);
                        echo "<th class='sqlDataHeader'>" . $cMeta["name"] . "</th>\n";
                    }
                    echo "</tr>";

                    while ($row = $stmt->fetch()) {
                        echo "<tr class='sqlDataRow$odd'>\n";
                        for ($i = 0; $i < $cCount; $i++) {
                            echo "<td class='sqlDataRow$odd'>" . $row[$i] . "</td>\n";
                        }
                        echo "</tr>\n";
                        $odd = ($odd + 1) % 2;
                    }
                }
    ?>
</table>
