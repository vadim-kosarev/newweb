<?php
include_once("../Common/sys_config.php");
include_once("../Common/sys_db.php");
class DefaultReportBuilder {

	protected $odd = 1;
	protected $categoryNColumns = 0;
	protected $editableColumns = 0;
	protected $catValues = array();
	protected $meCreatedTime = 0;
	protected $outputStream = NULL;

	/**
	 *
	 * Enter description here ...
	 */
	function __construct() {

		$time = microtime();
		$time = explode(' ', $time);
		$time = $time[1] + $time[0];
		$this->meCreatedTime = $time;

		if (isset($_GET["categories"])) $this->categoryNColumns = $_GET["categories"];
		if (isset($_GET["editableColumns"])) $this->editableColumns = $_GET["editableColumns"];

	}

	public function setOutput($out) {
		$this->outputStream = $out;
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function hideProgressDiv() {
		$this->p('<script language="JavaScript">showhide("progressDiv")</script>');
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $stmt
	 * @param unknown_type $dArr
	 */
	public function processStmt($stmt, $dArr) {
		$this->hideProgressDiv();
		$this->printFilterTable($stmt, $dArr);
		$this->printPagesLinks();
		$this->printDataTable($stmt, $dArr);
		$this->printPagesLinks();
		$this->printFooter($stmt, $dArr);
	}

	/**
	 * Enter description here ...
	 * @param unknown_type $dArr
	 */
	public function printHeader($stmt, $dArr) {
		if (isset($dArr["header"])) {
			$this->printDataCell($stmt, $dArr, $dArr, $dArr["header"]);
		}
	}

	/**
	 * Enter description here ...
	 * @param unknown_type $dArr
	 */
	public function printFooter($stmt, $dArr) {
		$this->printDataCell($stmt, $dArr, $dArr, $dArr["footer"]);

		$time = microtime();
		$time = explode(' ', $time);
		$time = $time[1] + $time[0];
		$finish = $time;
		$total_time = round(($finish - $this->meCreatedTime), 4);
		$this->p( 'Page generated in '.$total_time.' seconds on ' . date("D M j G:i:s T Y") );
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $stmt
	 * @param unknown_type $dArr
	 */
	public function printDataHeader($stmt, $dArr) {
		// columns headers
		$this->p( "<table class='sqlData'>" );
		$this->p( "<tr class='sqlDataHeader'>\n");
		$cCount = $stmt->columnCount();
		for ($i = 0; $i < $cCount; $i++) {
			$cMeta = $stmt->getColumnMeta($i);
			if ($this->isColumnVisible($i, $stmt, $dArr)) {
				$this->p( "<th class='sqlDataHeader'>" . $this->getColumnTitle($i, $stmt, $dArr, $cMeta["name"]) . "</th>\n");
			}
		}
		$this->p( "</tr>" );
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $i
	 * @param unknown_type $stmt
	 * @param unknown_type $dArr
	 */
	public function isColumnVisible($i, $stmt, $dArr) {
		if (!array_key_exists("columns", $dArr)) {
			return true;
		}
		$columnsArr = preg_split("/[,;]/", $dArr["columns"]);
		if (count($columnsArr) < 1 || $i >= count($columnsArr)) {
			return true;
		}
		return ($columnsArr[$i] != "hidden");

	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $i
	 * @param unknown_type $stmt
	 * @param unknown_type $dArr
	 * @param unknown_type $dfltValue
	 */
	public function getColumnTitle($i, $stmt, $dArr, $dfltValue) {
		if (!array_key_exists("columns", $dArr) || !$dArr["columns"]) {
			return $dfltValue;
		}
		$columnsArr = preg_split("/[,;]/", $dArr["columns"]);
		if (count($columnsArr) < 1 || $i >= count($columnsArr) || $columnsArr[$i] == "*" || preg_match("/^\{.*\}$/", $columnsArr[$i]) ) {
			return $dfltValue;
		}

		return $columnsArr[$i];
	}


	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $stmt
	 * @param unknown_type $dArr
	 */
	public function printDataTable($stmt, $dArr) {
		$this->printDataHeader($stmt, $dArr);

		for ($i = 0 ; $i < $this->categoryNColumns ; $i++) {
			$this->catValues[$i] = null;
		}

		while ($row = $stmt->fetch()) {
			$this->printDataRow($stmt, $dArr, $row);
		}

		$this->printDataFooter($stmt, $dArr);
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $key
	 * @param unknown_type $row
	 */
	public function getQueryVal($key, $row) {
		$m = array();
		if (preg_match("/\\$(\d+)/", $key, $m)) {
			return $row[$m[1]];
		} else {
			return $key;
		}
	}


	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $queryID
	 */
	public function getSQL($queryID) {
		global $dbh;
		$stmt = $dbh->prepare("SELECT * FROM sys_queries WHERE query_id = :queryID");
		$stmt->bindValue(":queryID", $queryID);
		if ($stmt->execute()) {
			$dArr = $stmt->fetch();
			$qSQL = $dArr["sql"];
			return $qSQL;
		} else {
			return "SELECT 'ERROR'";
		}
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $queryArr
	 * @param unknown_type $row
	 */
	public function execQuery($queryArr, $row) {
		$vp = $this->getExecQueryResult($queryArr, $row);
		$this->p($vp);
	}

	public function getExecQueryResult($queryArr, $row) {
		$result = "";
		$qID = $queryArr[0];

		$querySQL = $this->getSQL($qID);

		global $dbhTarget;
		$stmt = $dbhTarget->prepare($querySQL);

		$arrSize = count($queryArr);
		for ($i=1;$i<$arrSize;$i++) {
			$val = $this->getQueryVal($queryArr[$i], $row);
			$valueToBind = ":ARG" . $i;
			$stmt->bindValue($valueToBind, $val);
			//echo "$valueToBind : $val";
		}

		if ($stmt->execute()) {
			$cc = $stmt->columnCount();
			while ($row = $stmt->fetch()) {
				for ( $i=0 ; $i < $cc ; $i++ ) {
					//$this->p( $row[$i] );
					$result .= $row[$i];
				}
			}
		}
		return $result;
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $stmt
	 * @param unknown_type $dArr
	 * @param unknown_type $row
	 * @param string $v -- can be a string to output OR contain something like {{1211,$0}} which means
	 *    - "execute query id=1211 with $row[0] as a first parameter of that query
	 */
	public function printDataCell($stmt, $dArr, $row, $v) {
		$pv = $this->getDataCellValue($stmt, $dArr, $row, $v);
		$this->p($pv);
	}

	public function getDataCellValue($stmt, $dArr, $row, $v) {
		$extQueries = array();
		$matches = preg_match_all ("/\\{\\{\d+[^\\{\\}]*\\}\\}/", $v, $extQueries);
		if ($matches == 0) {
			return $v;
		} else {
				
			$res = "";
				
			$cCount = $stmt->columnCount();
			$strCInd = 0;
			for ($i = 0 ; $i < $matches ; $i++ ) {
				$strMatch = $extQueries[0][$i];
				$queryArr = array();
				preg_match_all("/([^,{}]+)/", $strMatch, $queryArr);

				$sPos = strpos($v, $strMatch, $strCInd);
				//$this->p( substr($v, $strCInd, $sPos-$strCInd) );
				$res .= substr($v, $strCInd, $sPos-$strCInd);
				//$this->execQuery($queryArr[0], $row);
				$res .= $this->getExecQueryResult($queryArr[0], $row);
				$strCInd = $sPos + strlen($strMatch);
			}
			//$this->p( substr($v, $strCInd) );
			$res .= substr($v, $strCInd);
		}
		return $res;

	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $stmt
	 * @param unknown_type $dArr
	 * @param unknown_type $s
	 * @param unknown_type $cCount
	 */
	public function getVisibleColumnsCount($stmt, $dArr, $s, $cCount) {
		$cVisibleCount = 0;
		for ($i = $s ; $i < $cCount ; $i++) {
			if ($this->isColumnVisible($i, $stmt, $dArr)) $cVisibleCount++;
		}
		return $cVisibleCount;
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $stmt
	 * @param unknown_type $dArr
	 * @param unknown_type $row
	 */
	public function printDataRow($stmt, $dArr, $row) {

		$cCount = $stmt->columnCount();
		$cVisibleCount = $this->getVisibleColumnsCount($stmt, $dArr, 0, $cCount);
		$cVisibleNC = $this->getVisibleColumnsCount($stmt, $dArr, 0, $this->categoryNColumns);

		$dataForCellId = $row;

		$trCode = "<tr class='sqlDataRow" . $this->odd . "'>\n";

		$this->p( $trCode );

		for ($i = 0; $i < $cCount; $i++) {

			if ($this->isColumnVisible($i, $stmt, $dArr)) {

				$visibleI = $this->getVisibleColumnsCount($stmt, $dArr, 0, $i);

				$v = trim($row[$i]);

				if ( $i < $this->categoryNColumns ) {

					if ($this->catValues[$i] != $v) {
						$this->p( "</tr><tr>" );
						$this->p( str_repeat("<td></td>", $visibleI) );
						$this->p( "<td colspan='".($cVisibleCount-$visibleI)."' class='category$i'>$v</td></tr>" );
						$this->catValues[$i] = $v;
						for ($j=$i+1;$j<$this->categoryNColumns;$j++) $this->catValues[$j] = null;
						$this->p( $trCode );
					}

				} else {

					if ($visibleI == $cVisibleNC) {
						$this->p( str_repeat("<td></td>", $visibleI) );
					}

					$dataForCellId["__columnIndex"] = $i;
					$cellClass = "td";
					$cellId = "";

					if ($i >= $cCount - $this->editableColumns) {
						$cellId = base64_encode(json_encode($dataForCellId));
						$cMeta = $stmt->getColumnMeta($i);
						$cellClass = $cMeta["name"];
					}

					$this->p( "<td class='sqlDataRow" . $this->odd . "'><span class='$cellClass' id='$cellId'>" );
					$this->printDataCell($stmt, $dArr, $row, $v);
					$this->p( "</span></td>\n" );
				}

			}

		}

		$this->p( "</tr>\n" );
		$this->odd = ($this->odd + 1) % 2;
	}





	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $stmt
	 * @param unknown_type $dArr
	 */
	public function printDataFooter($stmt, $dArr) {
		$this->p( "</table>" );
	}


	public function p($content) {
		if (is_null($this->outputStream)) {
			echo $content ;
		} else {
			$this->outputStream->p($content);
		}
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $stmt
	 * @param unknown_type $dArr
	 */
	public function printPageHeader($stmt, $dArr) {
		$this->printHeader($stmt, $dArr);
		$qName = "";
		if (isset($dArr["name"])) {
			$qName = $dArr["name"];
		}
		$this->p('<script language="JavaScript">document.title = ' . json_encode($qName) . '</script>');

		$this->p('<script language="JavaScript">');
		$this->p('$(document).ready(function() {');

		if (array_key_exists("columns", $dArr)) {
			$columnsArr = preg_split("/[,;]/", $dArr["columns"]);
			foreach ($columnsArr as $c) {
				if (preg_match("/^\{(.*)\}$/", $c, $matches) > 0) { // $c = {select:SVN_Documentation_Status}
					$data = $matches[1];
					if (preg_match("/^([^\:]+)\:([^\:]+)$/", $data, $m)) { // $data = select:SVN_Documentation_Status
							
						$type = $m[1];
						$proc = $m[2];
						$jsDataString = "";

						if ($type == 'select') {
							$dataArr = array();
							global $dbh;
							$sql = "CALL proc_" . $proc . "_SourceData()";
							$stmt1 = $dbh->prepare($sql);
							if ($stmt1->execute()) {
								while ($row = $stmt1->fetch()) {
									$dataArr[$row[0]] = $row[1];
								}
							}
							$jsDataString = "data:'" . json_encode($dataArr) . "'";

						} else if ($type = "textarea") {

							$jsDataString = "rows:5,cols:30";

						}

						$this->p( "

								$('.$proc').editable('../Collector/save.php' ,{
								type      : '$type',
								indicator : 'saving...',
								tooltip   : 'Click to edit...',
								placeholder : 'Click to add new value...',
								submit    : 'OK',
								submitdata : {proc: '$proc', size: '" . count($columnsArr)."'},
								$jsDataString
					}
						);
								");
					}
				}
			}
		}

		$this->p('}); </script>');

	}

	/**
	 *
	 */
	public function printPagesLinks() {
		global $reportDefaultLimit;
		$currentLimit = $reportDefaultLimit;
		$currentStart = 0;

		if (isset($_GET["limit"])) {
			$a = array();
			if (preg_match("/(\d+),(\d+)/", $_GET["limit"], $a)) {
				$currentStart = $a[1];
				$currentLimit = $a[2];
			}
		}

		$pagesPerDirection = 5;

		$firstPage1 = $currentStart - $currentLimit*$pagesPerDirection;
		if ($firstPage1<0) $firstPage1 = 0;

		$lastPage1 =  $currentStart + $currentLimit*$pagesPerDirection;

		$this->p('
				<script language="JavaScript">
				function applyLimit(limit) {
				document.forms["filterForm"].elements["limit"].value=limit;
				document.forms["filterForm"].submit();
	}
				</script>
				<div class="pagesLinks" align="right">
				');

		$this->p( "<b>Pages:</b>" );
		if($firstPage1!=0) {
			$this->printPageLink(0,$currentLimit,$currentStart);
			if ($firstPage1!=$currentLimit) $this->p( "..." );
		}

		for ($i = $firstPage1 ; $i <= $lastPage1 ; $i+=$currentLimit) {
			$this->printPageLink($i,$currentLimit,$currentStart);
		}

		$this->p('</div>');

	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $i
	 * @param unknown_type $currentLimit
	 * @param unknown_type $currentStart
	 */
	public function printPageLink($i,$currentLimit,$currentStart) {
		$filterValue = "$i,$currentLimit";
		$isCurrentPage = false;
		if ($i==$currentStart) $isCurrentPage = true;
		if ($i>0) $this->p( "," );
		if (!$isCurrentPage) $this->p( "<a href='#' onclick='applyLimit(\"$filterValue\");return false;'>" );
		$this->p( $i/$currentLimit+1 );
		if (!$isCurrentPage) $this->p( "</a>" );
	}


	/**
	 *
	 * Prints filter table
	 * @param unknown_type $stmt
	 */
	public function printFilterTable($stmt, $dArr) {
		global $reportDefaultLimit;
		$cCount = $stmt->columnCount();
		$qOrderBy = $dArr["orderby"];
		if (isset($_GET["_orderby"])) {
			$qOrderBy = $_GET["_orderby"];
		}
		$chartColumns = $dArr["chart_columns"];

		$this->p('
				<br />
				<a href="#" onClick="showhide(\'filterForm\')">SHOW / HIDE Filter Form</a>
				<div id="filterForm" style="display: none">
				<table>
				<tr>
				<td>
				<form action="' . $_SERVER['REQUEST_URI'] . '" name="filterForm" id="filterformForm">
				<table class="filterForm">
				');

		for ($i = 0; $i < $cCount; $i++) {
			$cMeta = $stmt->getColumnMeta($i);
			$cMetaName = $cMeta["name"];
			$formParamName = "sql_$cMetaName";
			$formParamValue = "";
			if (isset($_GET[$formParamName])) {
				$formParamValue = $_GET[$formParamName];
			}
			$this->p( "<tr class='filterForm'><td class='filterForm'>" . $formParamName . "</td>" );
			$this->p( "<td class='filterForm'><input name='" . my_encode($formParamName) .
					"' type='text' value='$formParamValue' class='filterForm'/></td></tr>\n" );
		}

		$this->p('
				<tr class="filterForm">
				<td class="filterForm">ORDER BY</td>
				<td class="filterForm"><input name="_orderby"
				value="' . $qOrderBy . '" class="filterForm" /></td>
				</tr>

				');

		$this->p( "<input type='hidden' name='reportBuilderClass' id='reportBuilderClass' value='".get_class($this)."'/>\n" );
		
		foreach (array_keys($_GET) as $key) {
			$arr = array();
			if ($key == "reportBuilderClass") {
				// do nothing
			}
			else if (!preg_match("/(sql_)|(_)(.+)/i", $key, $arr)) {
				$this->p( "<input type='hidden' name='$key' value='".$_GET[$key]."'/>\n" );
			}
		}
		
		if (!isset($_GET["limit"])) {
			$this->p('<input type="hidden" name="limit" value="0,' . $reportDefaultLimit . '" />');
		}
			
		$this->p('
				</table>
				<input type="submit" />');
		if ($chartColumns) {
			$imageUrl = $_SERVER['REQUEST_URI'];
			$imageUrl = preg_replace("/[^\/\?]+\?/", "web_query.php?", $imageUrl);
			$imageUrl .= "&reportBuilderClass=PChartReportBuilder";
			$this->p("<a href='".$imageUrl."' target='_blank'>[Click to generate image]</a>");
	 		
	    }
		$this->p('
				<pre class="hint">
				HINT:
				sql_X : VALUE     =>   X = "VALUE"
				sql_X : VAL*STR   =>   X LIKE "VAL%STR"
				sql_X : V1...     =>   X >= "V1"
				sql_X : ...V2     =>   X <= "V2"
				sql_X : V1...V2   =>   X >= "V1" AND X <= "V2"

				sql_X : RRR,YYY   =>   (X [=|LIKE|>=|<=] RRR OR X [=|LIKE|>=|<=] YYY)
				sql_X : !RRR      =>   NOT ( RRR )
				</pre></form>
				</td>
				<td>
				');


	 $this->p('
	 		</td>
	 		</tr>
	 		</table>

	 		</div>
	 		');
	}
}
?>
