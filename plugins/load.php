<?php 

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }

$ejda_plugin_files = array(
	'wp-ejda-colors/ejda-colors.php',
);

foreach ($ejda_plugin_files as $file) {
	include_once($file);
}