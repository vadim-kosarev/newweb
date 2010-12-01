<?php

error_reporting(0);
header('Content-type: image/png');

// Standard inclusions
include("pChart/pChart/pData.class");
include("pChart/pChart/pChart.class");

// Query data
include_once("../Common/sys_config.php");
include_once("../Common/sys_utils.php");

$isSql = true;
if (isset($_GET["plain"])) {
    $isSql = false;
}

$DataSet = new pData;
$columnsStr = $_GET["data"];
$xAxis = $_GET["xAxis"];
$sqlHash = "___";

$forceGenerate = false;

if ($isSql) {
    $dbUrl = "mysql:host=$config_db_host;dbname=$config_db_name;port=$config_db_port";
    $dbh = new PDO($dbUrl, $config_db_user, $config_db_password);
    $qSql = $_GET["sql"];
    $sqlHash = md5($qSql);

    $stmt = $dbh->prepare($qSql);
    // Dataset definition

    if ($stmt->execute()) {

        $columns = preg_split("/,/", $columnsStr);
        while ($arr = $stmt->fetch()) {
            $DataSet->AddPoint($arr[$xAxis], $xAxis);
            foreach ($columns as $column) {
                $DataSet->AddPoint($arr[$column], $column);
            }
        }
    }
    
    $forceGenerate = true;
    
} else {
    // plain
    $dataStr = $_GET["data"];
    $dataValues = preg_split("/,/", $dataStr);
    $DataSet->AddPoint("data", $xAxis);
    foreach ($dataValues as $d) {
        $DataSet->AddPoint($d, "data");
    }
    $sqlHash = md5($dataStr);
}

$file = "../pChart/Cache/chart$sqlHash.png";



if ( !file_exists($file) || $forceGenerate ) {

$DataSet->SetAbsciseLabelSerie($xAxis);
$DataSet->AddAllSeries();
$DataSet->RemoveSerie($xAxis);
$DataSet->SetYAxisName($columnsStr);


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

$Test->Render($file);
}


readfile($file);
?>