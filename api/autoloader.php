<?php
spl_autoload_register(function($className) {
	$filename = str_replace('\\', '/', $className) . ".php";
	require_once($filename);
	if (class_exists($className)) {
		return TRUE;
	}
	return FALSE;
});