<?php
include_once("DefaultReportBuilder.php");
class PlainReportBuilder extends DefaultReportBuilder {

	/**
	 * (non-PHPdoc)
	 * @see DefaultReportBuilder::printDataHeader()
	 */
	public function printDataHeader($stmt, $dArr) {
		$this->p('
<style>
<!--
div.category0 {
	
}

div.category1 {
	border-top: 1px solid black;
	padding-top: 10pt;
	margin-top: 10pt;
}

div.category2 {
	margin-top: 10pt;
	margin-bottom: 5pt;
	margin-left: 20pt;
	color: darkred;
}
-->
</style>
		');
	}



	/**
	 * (non-PHPdoc)
	 * @see DefaultReportBuilder::printDataFooter()
	 */
	public function printDataFooter($stmt, $dArr) {

	}

	/**
	 * (non-PHPdoc)
	 * @see DefaultReportBuilder::printFilterTable()
	 */
	public function printFilterTable($stmt, $dArr) {
	}

	/**
	 * (non-PHPdoc)
	 * @see DefaultReportBuilder::printPagesLinks()
	 */
	public function printPagesLinks() {
	}

	/**
	 * (non-PHPdoc)
	 * @see DefaultReportBuilder::printPageHeader()
	 */
	public function printPageHeader($stmt, $dArr) {
			
	}


	/**
	 * (non-PHPdoc)
	 * @see DefaultReportBuilder::printDataRow()
	 */
	public function printDataRow($stmt, $dArr, $row) {
		$cCount = $stmt->columnCount();
		for ($i = 0; $i < $cCount; $i++) {
			$v = trim($row[$i]);
			if ( $i < $this->categoryNColumns ) {

				if ($this->catValues[$i] != $v) {

					$this->catValues[$i] = $v;
					for ($j = $i+1 ; $j < $this->categoryNColumns; $j++) $this->catValues[$j] = null;

					$this->p( "<div class='category$i'>$v</div>" );
				}

			} else {
				$this->p( " $v " );
			}
		}
		$this->p( "<br/>\n");

	}


}
?>