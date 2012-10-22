<?php

include_once("../Common/sys_config.php");
include_once("../Common/sys_db.php");

class ReportCache {
	
	protected $myWrapped;
	protected $myContent = "";
	
	function __construct($wrapped) {
		$myWrapped = $wrapped;
	}
	
	function p($content) {
		echo $content;
		$this->myContent .= $content;
	}
	
	function loadCache() {
		global $dbh;
		$keyValue = $_SERVER['QUERY_STRING'];
		$keyHash = $this->getCacheKey($keyValue);
		
		$sql = "SELECT cache_content FROM sys_cache WHERE key_hash = :keyHash";
		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(":keyHash", $keyHash);
		
		if ($stmt->execute()) {
			$dArr = $stmt->fetch();
			$this->myContent = $dArr["cache_content"];
		}
	}
	
	function isLoaded() {
		return strlen($this->myContent) > 0;
	}
	
	function getCacheKey($v) {
		return md5($v);
	}
	
	function storeCache() {
		global $dbh;
		
		$keyValue = $_SERVER['QUERY_STRING'];
		$keyHash = $this->getCacheKey($keyValue);
		
		$sql = "INSERT INTO sys_cache (key_hash,key_value,cache_content) VALUES (:keyHash, :keyValue, :content)
  								ON DUPLICATE KEY UPDATE cache_content=:content";
		
		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(":keyHash", $keyHash);
		$stmt->bindValue(":keyValue", $keyValue);
		$stmt->bindValue(":content", $this->myContent);
		
		if ($stmt->execute()) {
			echo "cache saved...";
		} else {
			echo "can't store cache: $sql \n";
			print_r($stmt->errorInfo());
		}
		
	}
	
	function content() {
		return $this->myContent . "\n\nCached copy";
	}
	
}
?>