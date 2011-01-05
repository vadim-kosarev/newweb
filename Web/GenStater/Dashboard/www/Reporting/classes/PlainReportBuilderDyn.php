<?php
include_once("PlainReportBuilder.php");
class PlainReportBuilderDyn extends PlainReportBuilder {

	/**
	 * (non-PHPdoc)
	 * @see DefaultReportBuilder::printDataHeader()
	 */
	public function printDataHeader($stmt, $dArr) {
		?>
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
		<?php 
	}

	private $divOpen = false;
	
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
						echo "</div>";
						$this->divOpen = false;
					}
						
					$this->catValues[$i] = $v;
					for ($j = $i+1 ; $j < $this->categoryNColumns; $j++) $this->catValues[$j] = null;
					
					if ( $i == $this->categoryNColumns-1 ) {
						
						$divId = $this->getDivID($i);
						$addHtml = " <small>[<a href=# onclick='showhide(\"$divId\");return false;'>...</a>]</small>";
						echo "<div class='category$i'>$v$addHtml</div>";
						echo "<div id='$divId' style='display: none;'>";
						$this->divOpen = true;
						
					} else {
						echo "<div class='category$i'>$v</div>";
					}
				}
				
			} else {
				echo " $v ";
			}
		}
		echo "<br/>\n";

	}


}
?>