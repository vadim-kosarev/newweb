<?php
include_once("../Common/sys_config.php");
include_once("../Common/sys_utils.php");

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


<div class="sqlTableHeader"><a href="html_query.php">HOME</a>: <span id='queryNameDiv'><?= $qName ?></span></div>
<script language="JavaScript">
    document.title = document.getElementById('queryNameDiv').textContent;
</script>

<!-- <?= $qSQL ?> -->

<?php
$stmt = $dbh->prepare($qSQL);
$categoryNColumns = 0;
foreach (array_keys($_GET) as $key) {
	$arr = array();
    if (preg_match("/(sql_)(.+)/i", $key, $arr)) {
    	$stmt->bindparam(":" . $arr[2], $_GET[$key]);
    }
}
?>


<?php

// execute and serialize results
$odd = 1;
if ($stmt->execute()) {
    $cCount = $stmt->columnCount();
?>
    <table>
        <tr>
            <td>
                <form action="<?= $_SERVER['REQUEST_URI'] ?>">
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
                    <tr><td>ORDER BY</td><td><input name="_orderby" value="<?= $qOrderBy ?>"/></td></tr>
<?php 
foreach (array_keys($_GET) as $key) {
	$arr = array();
    if (!preg_match("/(sql_)|(_)(.+)/i", $key, $arr)) {
    	echo "<input type='hidden' name='$key' value='".$_GET[$key]."'/>\n"; 
    }
}
?>                    
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
            <pre class="sql"><?= htmlentities($qSQL) ?></pre></td>
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
                    	$trCode = "<tr class='sqlDataRow$odd'>\n"; 
                        echo $trCode;
                        for ($i = 0; $i < $cCount; $i++) {
                        	
                        	$v = trim($row[$i]);
                        	if ( $i < $categoryNColumns ) {
                        		
                        		if ($catValues[$i] != $v) {
                        			echo "</tr><tr>";
                        			echo str_repeat("<td></td>", $i);
                        			echo "<td colspan='".($cCount-$i)."' class='category$i'>$v</td></tr>";
                        			$catValues[$i] = $v;
                        			for ($j=$i+1;$j<$categoryNColumns;$j++) $catValues[$j] = null;
                        			echo $trCode;
                        		}
                        		
                        	} else {
                        		
                        		if ($i == $categoryNColumns) echo str_repeat("<td></td>", $i);
                            	echo "<td class='sqlDataRow$odd'><span class='td'>" . $v . "</span></td>\n";
                            	
                        	}
                            
                        }
                        echo "</tr>\n";
                        $odd = ($odd + 1) % 2;
                    }
                }
    ?>
</table>
