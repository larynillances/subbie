<?php
//Custom Helper
//To get the CURRENT URL including the QUERY STRINGS
//i.e. ?page=home&id=69
function current_full_url(){
    $CI =& get_instance();

    $url = $CI->config->site_url($CI->uri->uri_string());

    return $_SERVER['QUERY_STRING'] ? $url . '?' . $_SERVER['QUERY_STRING'] : $url;
}

function current_uri_string(){
    $CI =& get_instance();

    $url = $CI->uri->uri_string();

    return $_SERVER['QUERY_STRING'] ? $url . "?" . $_SERVER['QUERY_STRING'] : $url;
}

function current_query_string(){
    return $_SERVER['QUERY_STRING'] ? $_SERVER['QUERY_STRING'] : "";
}