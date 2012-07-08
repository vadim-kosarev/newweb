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

function my_decode($str) {
    $str = urldecode($str);
    $str = str_replace("|", ".", $str);
    return $str;
}

function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    $start  = $length * -1; //negative
    return (substr($haystack, $start) === $needle);
}

?>
