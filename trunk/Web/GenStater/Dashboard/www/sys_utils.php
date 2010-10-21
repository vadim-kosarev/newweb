<?php
function paramGetToSQL($param) {
    return str_replace("|", ".", $param);
}

/**
 * Searches column ID by its alias from SQL-query
 * @param <type> $what
 * @param <type> $where
 */
function findFieldByAlias($what, $where) {
    $arr = array();
    $preg = "/([^\\s]+) AS [\\'\\\"]?".$what."[\'\"]?/i";
    if ( preg_match($preg, $where, $arr) ) {
        return $arr[1];
    } else {
        return $what;
    }
}

function my_encode($str) {
    $str = str_replace(".", "|", $str);
    $str = urlencode($str);
    return $str;
}

function my_dencode($str) {
    $str = urldecode($str);
    $str = str_replace("|", ".", $str);
    return $str;
}

?>
