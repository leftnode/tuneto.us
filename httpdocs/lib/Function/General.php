<?php

function ttu_get_param_list($ignore_list=array()) {
	$param_list = array();
	$ignore_list = array_make_values_keys($ignore_list);
	
	foreach ( $_GET as $k => $v ) {
		if ( false === isset($ignore_list[$k]) ) {
			$param_list[$k] = $v;
		}
	}
	return $param_list;
}

function ttu_get_param_list_string($ignore_list=array()) {
	$param_list = ttu_get_param_list($ignore_list);
	$query_string = NULL;
	$len = count($param_list);
	$i=0;
	foreach ( $param_list as $k => $v ) {
		$query_string .= $k . "=" . $v;
		
		if ( $i < $len ) {
			$query_string .= "&amp;";
		}
		$i++;
	}
	
	$query_string = "?" . $query_string;
	return $query_string;
}