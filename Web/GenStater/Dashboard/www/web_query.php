<?php
include_once("sys_config.php");
$dbh = new PDO('mysql:host=' . $config_db_host . ';dbname=' . $config_db_name, $config_db_user, $config_db_password);

$queryID = 1;
if (isset($_GET["id"]))
    $queryID = $_GET["id"];

$stmt = $dbh->prepare("SELECT * FROM sys_queries WHERE query_id = :queryID");
$stmt->bindValue(":queryID", $queryID);

$qSQL = "";
$qName = "";

if ($stmt->execute()) {
    $dArr = $stmt->fetch();
    $qSQL = $dArr["sql"];
    $qName = $dArr["name"];
}
?>
<div class="sqlTableHeader"><?= $qName ?></div>
<div class="sql"><?= $qSQL ?></div>

<table class="sqlData">
    <?php
    $stmt = $dbh->prepare($qSQL);
    foreach (array_keys($_GET) as $key) {
        $stmt->bindParam(":" . $key, $_GET[$key]);
    }
    $odd = 1;
    if ($stmt->execute()) {

        // columns headers
        $cCount = $stmt->columnCount();
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
