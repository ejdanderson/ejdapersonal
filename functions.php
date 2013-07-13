<?php

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }

/** This theme relies on a number of plugins:
 *
 * https://github.com/kidfiction/wp-ejda-colors
 *
 *
 *
 **/

// Some specific color overrides, dont always want max contrast
function ejdafp_colors() {
	$filters = array(
		'cf_colors_bio_box_title_color' => 'ejdafp_lightest',
		'cf_colors_bio_box_a' => 'ejdafp_lightest',
		'cf_colors_bio_box_a_hover' => 'ejdafp_light',
		'cf_colors_bio_box_links_a_hover_border' => 'ejdafp_lightest',
		'cf_colors_bio_box_content_color' => 'ejdafp_dark',
		'cf_colors_header_a' => 'ejdafp_lightest',
		'cf_colors_a_hover' => 'ejdafp_lightest',
		'cf_colors_header_a_hover' => 'ejdafp_light',
	);

	foreach ($filters as $filter => $method) {
		// Come in after plugin
		add_filter($filter, $method, 11);
	}

}
add_action('wp', 'ejdafp_colors');

function ejdafp_lightest($key) {
	return 'lightest';
}

function ejdafp_light($key) {
	return 'light';
}

function ejdafp_dark($key) {
	return 'dark';
}
