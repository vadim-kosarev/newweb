<?php

//error_reporting(0);
header('Content-type: image/png');

include_once("DefaultReportBuilder.php");
/* pChart library inclusions */
include("pChart2.1.3/class/pData.class.php");
include("pChart2.1.3/class/pDraw.class.php");
include("pChart2.1.3/class/pImage.class.php");

class PChartReportBuilder extends DefaultReportBuilder {

	public function getRandomArray($size) {
		$result = array();
		for ($i = 0 ; $i < $size ; $i++ ) {
			$result[] = rand(-20,20);
		}
		return $result;
	}

	public function printPageHeader($stmt, $dArr) {
	}

	public function processStmt($stmt, $dArr) {

		$chartTitle = $dArr["name"];
		$xLabel = $dArr["chart_x_axis"];

		$yLabel = $dArr["chart_columns"];
		$dataColumns = preg_split("/[,;]/", $yLabel);

		$seriesCount = count($dataColumns);


		/* Create and populate the pData object */
		$myData = new pData();
			
		$myData->setAxisName(0, preg_replace("/[,;]/", "\n", $yLabel));
			
		$chartData = array();
		for ($i = 0 ; $i < $seriesCount ; $i++) {
			$chartData[$i] = array();
		}

		$labels = array();

			
		while ($row = $stmt->fetch()) {
			$labels[] = $row[$xLabel];
			for ($i = 0 ; $i < count($dataColumns) ; $i++) {
				$v1 = $row[$dataColumns[$i]];
				$vFinal = $this->getDataCellValue($stmt, $dArr, $row, $v1);
				if (empty($vFinal)) $vFinal = VOID;
				$chartData[$i][] = $vFinal;
			}

		}

		for ($i = 0 ; $i < $seriesCount ; $i++) {
			$serieName = $dataColumns[$i];
			$myData->addPoints($chartData[$i], $serieName);
			$myData->setSerieWeight($serieName, 0.5);
		}
			
		$myData->addPoints($labels, $xLabel);
		$myData->setSerieDescription($xLabel,$xLabel);
		$myData->setAbscissa($xLabel);


		// ------------------ VISUAL EFFECTS BELOW --------------------------


		$myData->setAxisPosition(0,AXIS_POSITION_LEFT);
		$myData->setAxisName(0,"1st axis");
		$myData->setAxisUnit(0,"");

		$myPicture = new pImage(800,500,$myData);
		$Settings = array("R"=>240, "G"=>240, "B"=>240, "Dash"=>1, "DashR"=>260, "DashG"=>260, "DashB"=>260);
		$myPicture->drawFilledRectangle(0,0,800,500,$Settings);

		$Settings = array("StartR"=>255, "StartG"=>255, "StartB"=>255, "EndR"=>208, "EndG"=>208, "EndB"=>208, "Alpha"=>50);
		$myPicture->drawGradientArea(0,0,800,500,DIRECTION_VERTICAL,$Settings);

		$myPicture->drawRectangle(0,0,799,499,array("R"=>0,"G"=>0,"B"=>0));

		$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>50,"G"=>50,"B"=>50,"Alpha"=>20));

		$myPicture->setFontProperties(array("FontName"=>"fonts/Forgotte.ttf","FontSize"=>14));
		$TextSettings = array("Align"=>TEXT_ALIGN_MIDDLEMIDDLE
				, "R"=>41, "G"=>41, "B"=>41);
		$myPicture->drawText(400,25,"$chartTitle",$TextSettings);

		$myPicture->setShadow(FALSE);
		$myPicture->setGraphArea(50,50,775,400);
		$myPicture->setFontProperties(array("R"=>0,"G"=>0,"B"=>0,"FontName"=>"fonts/pf_arma_five.ttf","FontSize"=>6));

		$Settings = array("Pos"=>SCALE_POS_LEFTRIGHT
				, "Mode"=>SCALE_MODE_FLOATING
				, "LabelingMethod"=>LABELING_ALL
				, "GridR"=>255, "GridG"=>255, "GridB"=>255, "GridAlpha"=>50, "TickR"=>0, "TickG"=>0, "TickB"=>0, "TickAlpha"=>50, "LabelRotation"=>90, "CycleBackground"=>1, "DrawXLines"=>1, "DrawSubTicks"=>1, "SubTickR"=>255, "SubTickG"=>0, "SubTickB"=>0, "SubTickAlpha"=>50, "DrawYLines"=>ALL);
		$myPicture->drawScale($Settings);

		$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>50,"G"=>50,"B"=>50,"Alpha"=>10));

		$Config = array("DisplayValues"=>1);
		$myPicture->drawLineChart($Config);

		$Config = array("FontR"=>0, "FontG"=>0, "FontB"=>0, "FontName"=>"fonts/pf_arma_five.ttf", "FontSize"=>6, "Margin"=>6, "Alpha"=>30, "BoxSize"=>5, "Style"=>LEGEND_NOBORDER
				, "Mode"=>LEGEND_VERTICAL
		);
		$myPicture->drawLegend(653,16,$Config);

		$myPicture->stroke();
	}

}
