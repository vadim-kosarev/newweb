<?php
include_once("PlainReportBuilder.php");
class PlainReportBuilderDyn extends PlainReportBuilder {

	/**
	 * (non-PHPdoc)
	 * @see DefaultReportBuilder::printDataHeader()
	 */
	public function printDataHeader($stmt, $dArr) {
		$this->p( '
		 
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
				
		' );
	}

	private $divOpen = false;

	protected function showData() {
		if (array_key_exists("showData", $_GET) &&  $_GET["showData"] == "false") {
			return false;
		}
		return true;
	}

	protected function getDivID($i) {
		$res = "";
		$cCount =  $this->categoryNColumns;
		for ($i = 0; $i < $cCount; $i++) {
			$res .= $this->catValues[$i];
		}
		return $res;
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
					if ($this->divOpen) {
						$this->p( "</div>");
						$this->divOpen = false;
					}

					$this->catValues[$i] = $v;
					for ($j = $i+1 ; $j < $this->categoryNColumns; $j++) $this->catValues[$j] = null;

					if ( $i == $this->categoryNColumns-1 ) {

						$divId = $this->getDivID($i);
						$addHtml = "";
						if ($this->showData()) {
							$addHtml = " <small>[<a href=# onclick='showhide(\"$divId\");return false;'>...</a>]</small>";
						}
						$this->p( "<div class='category$i'>$v$addHtml</div>");
						$this->p( "<div id='$divId' style='display: none;'>");

						$this->divOpen = true;

					} else {
						$this->p( "<div class='category$i'>$v</div>");
					}
				}

			} else {
				if ($this->showData())
				$this->p( " $v ");
			}
		}
		if ($this->showData()) $this->p( "<br/>\n");

	}


}
?>