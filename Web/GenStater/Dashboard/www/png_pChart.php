<?php

error_reporting(0);
header('Content-type: image/png');

// Standard inclusions
include("pChart/pChart/pData.class");
include("pChart/pChart/pChart.class");

// Query data
include_once("sys_config.php");
include_once("sys_utils.php");

$dbUrl = "mysql:host=$config_db_host;dbname=$config_db_name;port=$config_db_port";
$dbh = new PDO($dbUrl, $config_db_user, $config_db_password);

$qSql = $_GET["sql"];
$sqlHash = md5($qSql);

$stmt = $dbh->prepare($qSql);
// Dataset definition
$DataSet = new pData;
$columnsStr = $_GET["data"];
$xAxis = $_GET["xAxis"];

if ($stmt->execute()) {

    $columns = preg_split("/,/", $columnsStr);
    while ($arr = $stmt->fetch()) {
        $DataSet->AddPoint($arr[$xAxis], $xAxis);
        foreach ($columns as $column) {
            $DataSet->AddPoint($arr[$column], $column);
        }
    }
}

//$DataSet->ImportFromCSV("pChart/Sample/bulkdata.csv",",",array(1,2,3),FALSE,0);
$DataSet->SetAbsciseLabelSerie($xAxis);
$DataSet->AddAllSeries();
$DataSet->RemoveSerie($xAxis);
//$DataSet->SetSerieName("January", "Serie1");
//$DataSet->SetSerieName("February", "Serie2");
//$DataSet->SetSerieName("March", "Serie3");
$DataSet->SetYAxisName($columnsStr);
//$DataSet->SetYAxisUnit("µs");


// Initialise the graph
$width = 800; $heigth = 450; // 700x230
$Test = new pChart(800, 450);
$Test->setFontProperties("pChart/Fonts/tahoma.ttf", 8);
$Test->setGraphArea(70, 30, $width-20, $heigth-30);
$Test->drawFilledRoundedRectangle(7, 7, $width-7, $heigth-7, 5, 240, 240, 240);
$Test->drawRoundedRectangle(5, 5, $width-5, $heigth-4, 5, 230, 230, 230);

$Test->drawGraphArea(255, 255, 255, TRUE);
//$Test->drawGraphAreaGradient(132,173,131,50,TARGET_BACKGROUND);
$Test->drawScale($DataSet->GetData(), $DataSet->GetDataDescription(), SCALE_START0, 150,150,150, TRUE, 0, 2);
$Test->drawGrid(4, TRUE, 230, 230, 230, 50);

// Draw the 0 line
$Test->setFontProperties("Fonts/tahoma.ttf", 6);
$Test->drawTreshold(0, 143, 55, 72, TRUE, TRUE);

// Draw the line graph
$Test->setColorPalette(0,0,0,0xff);
$Test->drawLineGraph($DataSet->GetData(), $DataSet->GetDataDescription());
//$Test->drawPlotGraph($DataSet->GetData(), $DataSet->GetDataDescription(), 3, 2, 255, 255, 255);

// Finish the graph
$Test->setFontProperties("pChart/Fonts/tahoma.ttf", 8);
$Test->drawLegend(75, 35, $DataSet->GetDataDescription(), 255, 255, 255);
$Test->setFontProperties("pChart/Fonts/tahoma.ttf", 10);
$Test->drawTitle(60, 22, $column, 50, 50, 50, 585);

$file = "pChart/Cache/chart$sqlHash.png";
$Test->Render($file);
readfile($file);
?>