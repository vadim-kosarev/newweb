<?php

class DefaultReportBuilder {

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $stmt
	 * @param unknown_type $dArr
	 */
	public function processStmt($stmt, $dArr) {
		$this->printFilterTable($stmt, $dArr);
		$this->printDataTable($stmt, $dArr);
	}



	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $stmt
	 * @param unknown_type $dArr
	 */
	public function printDataHeader($stmt, $dArr) {
		// columns headers
		echo "<table class='sqlData'>";
		echo "<tr class='sqlDataHeader'>\n";
		$cCount = $stmt->columnCount();
		for ($i = 0; $i < $cCount; $i++) {
			$cMeta = $stmt->getColumnMeta($i);
			echo "<th class='sqlDataHeader'>" . $cMeta["name"] . "</th>\n";
		}
		echo "</tr>";
	}





	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $stmt
	 * @param unknown_type $dArr
	 */
	public function printDataTable($stmt, $dArr) {
		$this->printDataHeader($stmt, $dArr);
		
		if (isset($_GET["categories"])) $this->categoryNColumns = $_GET["categories"];
		for ($i = 0 ; $i < $this->categoryNColumns ; $i++) {
			$this->catValues[$i] = null;
		}
		
		while ($row = $stmt->fetch()) {
			$this->printDataRow($stmt, $dArr, $row);
		}
		$this->printDataFooter($stmt, $dArr);
	}



	protected $odd = 1;
	protected $categoryNColumns = 0;
	protected $catValues = array();

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $stmt
	 * @param unknown_type $dArr
	 * @param unknown_type $row
	 */
	public function printDataRow($stmt, $dArr, $row) {

		$cCount = $stmt->columnCount();

		$trCode = "<tr class='sqlDataRow" . $this->odd . "'>\n";

		echo $trCode;
		for ($i = 0; $i < $cCount; $i++) {

			$v = trim($row[$i]);
			if ( $i < $this->categoryNColumns ) {

				if ($this->catValues[$i] != $v) {
					echo "</tr><tr>";
					echo str_repeat("<td></td>", $i);
					echo "<td colspan='".($cCount-$i)."' class='category$i'>$v</td></tr>";
					$this->catValues[$i] = $v;
					for ($j=$i+1;$j<$this->categoryNColumns;$j++) $this->catValues[$j] = null;
					echo $trCode;
				}

			} else {

				if ($i == $this->categoryNColumns) echo str_repeat("<td></td>", $i);
				echo "<td class='sqlDataRow" . $this->odd . "'><span class='td'>" . $v . "</span></td>\n";

			}

		}
		echo "</tr>\n";
		$this->odd = ($this->odd + 1) % 2;
	}





	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $stmt
	 * @param unknown_type $dArr
	 */
	public function printDataFooter($stmt, $dArr) {
		echo "</table>";
	}




	public function printPageHeader($stmt, $dArr) {
		$qName = $dArr["name"];
		?>
<div class="sqlTableHeader"><a href="html_query.php">HOME</a>: <span id='queryNameDiv'><?= $qName ?></span></div>
<script language="JavaScript">
    document.title = document.getElementById('queryNameDiv').textContent;
</script>
		<?php 
	}



	/**
	 *
	 * Prints filter table
	 * @param unknown_type $stmt
	 */
	public function printFilterTable($stmt, $dArr) {
		$cCount = $stmt->columnCount();
		$qOrderBy = $dArr["orderby"];
		$chartColumns = "";
		?>
<a href="#" onClick="showhide('filterForm')">SHOW / HIDE Filter Form</a>
<div id="filterForm" style="display: none">
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
			<tr>
				<td>ORDER BY</td>
				<td><input name="_orderby" value="<?= $qOrderBy ?>" /></td>
			</tr>
			<?php
			foreach (array_keys($_GET) as $key) {
				$arr = array();
				if (!preg_match("/(sql_)|(_)(.+)/i", $key, $arr)) {
					echo "<input type='hidden' name='$key' value='".$_GET[$key]."'/>\n";
				}
			}
			?>
		</table>
		<input type="submit" /> <pre class="hint">
HINT:
sql_X : VALUE     =>   X = 'VALUE'
sql_X : VAL*STR   =>   X LIKE "VAL%STR"
sql_X : V1...     =>   X >= "V1"
sql_X : ...V2     =>   X <= "V2"
sql_X : V1...V2   =>   X >= "V1" AND X <= "V2"

sql_X : RRR,YYY   =>   (X [=|LIKE|>=|<=] RRR OR X [=|LIKE|>=|<=] YYY)
                </pre></form>
		</td>
		<td><?php if ($chartColumns) { ?> <img
			src="png_pChart.php?ts=<?= time() ?>&data=<?=$chartColumns?>&xAxis=<?=$xAxis?>&sql=<?= urlencode($qSQL) ?>" />
			<?php } ?> 
		</td>
	</tr>
</table>

</div>
			<?php
	}



















}

?>