<?php

include_once("sys_config.php");

$sql_IDDataType = "INT(10) UNSIGNED NOT NULL";
$sql_IDCreateDataType = "INT(10) UNSIGNED NOT NULL AUTO_INCREMENT";

$arrDataTypesMap = array(
    "date" => "TIMESTAMP",
    "ext" => $sql_IDDataType,
    "id" => $sql_IDCreateDataType,
    "int" => "INT(10) UNSIGNED NULL",
    "str" => "VARCHAR(255) NULL",
    "tag" => "INT(10) UNSIGNED NULL",
    "default" => "VARCHAR(255) NULL"
);

$arrPDOParamTypes = array(
    "date" => PDO::PARAM_INT,
    "ext" => PDO::PARAM_INT,
    "id" => PDO::PARAM_INT,
    "int" => PDO::PARAM_INT,
    "str" => PDO::PARAM_STR,
    "tag" => PDO::PARAM_INT,
    "default" => PDO::PARAM_STR
);

$dbh = new PDO('mysql:host=' . $config_db_host . ';dbname=' . $config_db_name, $config_db_user, $config_db_password);

/**
 * Data Class : Event
 */
class Event {

    /**
     * Equals to array $_GET
     * @var <type> 
     */
    protected $initArray;

    /**
     * Initializes data
     * @param <type> $array 
     */
    public function init($array) {
        $this->initArray = $array;
        $this->internalInit();
    }

    /**
     * Stores data into database
     */
    public function store() {
        global $dbh;
        try {
            $dbh->beginTransaction();
            $this->ensureTablesExist();
            $this->storeData();
            $dbh->commit();
        } catch (Exception $e) {
            $dbh->rollback();
        }
    }

    /**
     *
     */
    public function selfTest() {
        echo $this->getFieldDataType("d_int_pid");
    }

    // =============== to be protected further ======================
    /**
     *
     */
    public function createTable($tableName, $arrFields) {
        $sql = "CREATE TABLE `$tableName` (\n";
        foreach ($arrFields as $field) {
            $sql .= "    `$field` " . $this->sql_DataType($this->getFieldDataType($field)) . ",";
            $sql .= "\n";
        }
        $sql .= "PRIMARY KEY (`" . $arrFields[0] . "`)\n";
        $sql .= ") ENGINE=MyISAM ROW_FORMAT=DEFAULT";

        global $dbh;
        return $dbh->query($sql);
    }

// =================================================================================================================

    /**
     * internal init
     */
    private function internalInit() {
        
    }

    /**
     *
     * @param <type> $arr 
     */
    private function pushTagsFields(&$arr) {
        array_push($arr, "tag0");
        array_push($arr, "tag1");
        array_push($arr, "tag2");
        array_push($arr, "tag3");
        array_push($arr, "tag4");
        array_push($arr, "tag5");
        array_push($arr, "tag6");
        array_push($arr, "tag7");
        array_push($arr, "tag8");
        array_push($arr, "tag9");
    }

    /**
     * Creates or alters all necesary tables to match required fields
     */
    private function ensureTablesExist() {
        global $dbh;
        $event = $this->initArray["event"];

        $dataTableFields = array();
        array_push($dataTableFields, "id");

        $arrData = array();

        foreach (array_keys($this->initArray) as $key) {
            if ($key == "event")
                continue;
            array_push($dataTableFields, $key);
            $val = $this->initArray[$key];

            if ($this->isExtData($key)) {
                $this->ensureExtTableExists($key);
                $val = $this->getExtDataID($key, $this->initArray[$key]);
            }
            $arrData[$key] = $val;
        }

        $tableName = $this->getDataTableName($event);
        $this->pushTagsFields($dataTableFields);
        $this->ensureTableFields($tableName, $dataTableFields);
        return $this->sql_InsertRecord($tableName, $arrData);
    }

    /**
     *
     * @param <type> $dataTableKey
     * @param <type> $value 
     */
    private function getExtDataID($dataTableKey, $value, $insertIfNotFound = true) {
        $fName = $this->getFieldDataName($dataTableKey);
        $extTableName = $this->getExtTableName($fName);
        $sql = "SELECT id FROM `$extTableName` WHERE d_str_name=:STRVAL";
        global $dbh;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(":STRVAL", $value, PDO::PARAM_STR);
        if ($stmt->execute()) {
            if ($row = $stmt->fetch()) {
                return $row[0];
            } else {
                if ($insertIfNotFound) {
                    $this->addExtValue($dataTableKey, $value);
                    return $this->getExtDataID($dataTableKey, $value, false);
                } else {
                    return -1;
                }
            }
        } else {
            return -1;
        }
    }

    /**
     *
     * @param <type> $dataField
     * @param <type> $fieldValue 
     */
    private function addExtValue($dataField, $fieldValue) {
        $fName = $this->getFieldDataName($dataField);
        $extTableName = $this->getExtTableName($fName);
        return $this->sql_InsertRecord($extTableName, array("d_str_name" => $fieldValue));
    }

    /**
     *
     * @global <type> $dbh
     * @param <type> $tableName
     * @param <type> $arrData 
     */
    private function sql_InsertRecord($tableName, $arrData) {
        $sqlFields = "";
        $sqlValues = "";

        $comma = false;
        foreach(array_keys($arrData) as $key) {
            if ($comma) {
                $sqlFields .= ",";
                $sqlValues .= ",";
            }
            $sqlFields .= $key;
            $sqlValues .= ":V_" . $key;
            $comma = true;
        }

        $sql = "INSERT INTO `$tableName` (" . $sqlFields . ") VALUES (" . $sqlValues . ")";

        global $dbh;
        $stmt = $dbh->prepare($sql);
        foreach(array_keys($arrData) as $key) {
            $stmt->bindParam(":V_" . $key, $arrData[$key]);
        }
        return $stmt->execute();
    }

    /**
     * creates ext-data table
     */
    private function ensureExtTableExists($extDatakey) {
        $fDataName = $this->getFieldDataName($extDatakey);
        $extDataTable = $this->getExtTableName($fDataName);
        $extDataFields = array("id", "d_str_name");
        $this->ensureTableFields($extDataTable, $extDataFields);
    }

    /**
     * Creates table if not exists
     * @param <type> $dataTable
     * @param <type> $dataFields
     */
    private function ensureTableFields($dataTable, $dataFields) {
        $sql = "SHOW FIELDS FROM $dataTable";
        global $dbh;
        $stmt = $dbh->prepare($sql);

        $existingFields = array();

        if ($stmt->execute()) {
            while ($row = $stmt->fetch()) {
                array_push($existingFields, $row[0]);
            }
        } else {
            $this->createTable($dataTable, $dataFields);
            $existingFields = $dataFields;
        }

        $arrDiff = array_diff($dataFields, $existingFields);
        foreach ($arrDiff as $fieldToAdd) {
            $this->sql_addField($dataTable, $fieldToAdd);
        }
    }

    /**
     *
     * @global <type> $dbh
     * @param <type> $dataTable
     * @param <type> $fieldToAdd
     * @return <type> 
     */
    private function sql_addField($dataTable, $fieldToAdd) {
        $sql = "ALTER TABLE `$dataTable` ADD COLUMN `$fieldToAdd` " .
                $this->sql_DataType($this->getFieldDataType($fieldToAdd));
        global $dbh;
        return $dbh->query($sql);
    }

    /**
     *
     */
    private function storeData() {
        
    }

// =================================================================================================================

    /**
     *
     * @param <type> $eventName
     * @return <type>
     */
    private function getDataTableName($eventName) {
        return "data_events_" . $eventName;
    }

    /**
     *
     * @param <type> $dataName
     * @return <type> 
     */
    private function getExtTableName($dataName) {
        return "data_ext_" . $dataName;
    }

    /**
     *
     * @return <type>
     */
    private function sql_IDType() {
        return $sql_IDDataType;
    }

    /**
     *
     * @param <type> $dataType
     * @return <type> 
     */
    private function sql_DataType($dataType) {
        global $arrDataTypesMap;
        if (isset($arrDataTypesMap[$dataType])) {
            return $arrDataTypesMap[$dataType];
        } else {
            return $arrDataTypesMap["default"];
        }
    }

    private function isExtData($fieldName) {
        $r = strpos($fieldName, "d_ext_");
        if ($r === 0)
            return true;
        return false;
    }

    /**
     *
     * @param <type> $fieldName
     * @return array 
     */
    private function getFieldDataType($fieldName) {
        if ($fieldName == "id")
            return "id";
        if (strpos($fieldName, "tag") === 0)
            return "tag";
        $vals = array();
        preg_match("/(\\w)_(\\w+)_(.*)/i", $fieldName, $vals);
        return $vals[2];
    }

    /**
     *
     * @param <type> $fieldName
     * @return array
     */
    private function getFieldDataName($fieldName) {
        if ($fieldName == "id")
            return "id";
        if (strpos($fieldName, "tag") === 0)
            return "tag";
        $vals = array();
        preg_match("/(\\w)_(\\w+)_(.*)/i", $fieldName, $vals);
        return $vals[3];
    }

// =================================================================================================================
}

?>
