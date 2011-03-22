<?php

include_once("PlainReportBuilder.php");
class PlainReportBuilderDataOnly extends PlainReportBuilder {

	public function printPagesLinks() {
	}

	public function printFilterTable($stmt, $dArr) {
	}

	public function printDataHeader($stmt, $dArr) {
	}

	public function printDataRow($stmt, $dArr, $row) {
		$cCount = $stmt->columnCount();
		echo $row[$cCount-1] . "\n";
	}
}

?>