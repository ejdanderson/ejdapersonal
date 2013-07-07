<?php

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }

// Maximize constrast in all text except for hover
function ejda_colors() {
	$ejda_colors = new EJDA_Colors();
	if ($ejda_colors->colors_are_set()) {
		$ejda_colors->add_filters();	
	}
}
add_action('wp', 'ejda_colors');

// Provides better text color selection based on background color
class EJDA_Colors {
	function __construct() {
		$this->color_types = array(
			'darkest',
			'dark',
			'medium',
			'light',
			'lightest'
		);
		if (function_exists('cf_colors_get_colors')) {
			if ($colors = cf_colors_get_colors()) {
				foreach ($this->color_types as $key => $type) {
					$this->$type = $colors[$key];
				}
			}
		}
	}

	function colors_are_set() {
		foreach ($this->color_types as $type) {
			if (!isset($this->$type)) {
				return false;
			}
		}
		return true;
	}

	function add_filters() {
		$filters = array(
			'cf_colors_featured_posts_hover_color' => 'lightest_darkest_light',

			'cf_colors_widget_title_color' => 'lightest_darkest_light',
			'cf_colors_widget_search_placeholder_color' => 'lightest_darkest_light',

			'cf_colors_bio_box_title_color' => 'not_lightest_darkest_medium',
			'cf_colors_bio_box_a' => 'not_lightest_darkest_medium',
			'cf_colors_bio_box_a_hover' => 'dark_light_medium',
			'cf_colors_bio_box_links_a_hover_border' => 'lightest_darkest_meduim',
			'cf_colors_bio_box_content_color' => 'lightest_darkest_meduim',
			
			'cf_colors_social_a_color' => 'medium_dark_light',
			'cf_colors_social_nav_a_color' => 'lightest_darkest_light',

			'cf_colors_footer_color' => 'lightest_darkest_dark',
			'cf_colors_footer_a' => 'light_medium_dark',
		); 

		foreach ($filters as $filter => $function) {
			add_filter($filter, array($this, $function));
		}
	}

// Color selection filters
// Naming is as such: color1_color2_backgroundColor
// @TODO this is a bit tedious, might just want to replace the colors file...
	function lightest_darkest_light($key) {
		$color = $this->greatest_contrast($this->darkest, $this->lightest, $this->light);
		if ($color) {
			return $this->color_key($color);
		}
		return $key;
	}

	function not_lightest_darkest_medium($key) {
		$color = $this->greatest_contrast($this->darkest, $this->lightest, $this->medium);
		if ($color == $this->darkest) {
			$color = $this->lightest;
		}
		else {
			$color = $this->darkest;
		}
		if ($color) {
			return $this->color_key($color);
		}
		return $key;
	}

	function lightest_darkest_meduim($key) {
		$color = $this->greatest_contrast($this->darkest, $this->lightest, $this->medium);
		if ($color) {
			return $this->color_key($color);
		}
		return $key;
	}

	function lightest_darkest_dark($key) {
		$color = $this->greatest_contrast($this->darkest, $this->lightest, $this->dark);
		if ($color) {
			return $this->color_key($color);
		}
		return $key;
	}

	function medium_dark_light($key) {
		$color = $this->greatest_contrast($this->medium, $this->dark, $this->light);
		if ($color) {
			return $this->color_key($color);
		}
		return $key;
	}

	function dark_light_medium($key) {
		$color = $this->greatest_contrast($this->dark, $this->light, $this->medium);
		if ($color) {
			return $this->color_key($color);
		}
		return $key;
	}

	function light_medium_dark($key) {
		$color = $this->greatest_contrast($this->medium, $this->light, $this->dark);
		if ($color) {
			return $this->color_key($color);
		}
		return $key;
	}

	function color_key($color) {
		foreach ($this->color_types as $type) {
			if ($this->$type == $color) {
				return $type;
			}
		}
		return false;
	}

	// Get the numerical distance between the luma of 2 hex colors
	function luma_distance($hex_1, $hex_2) {
		$rgb_1 = $this->rgbify_color($hex_1);
		$rgb_2 = $this->rgbify_color($hex_2);
		if ($rgb_1 && $rgb_2) {
			$luma_1 = $this->get_luma($rgb_1[0], $rgb_1[1], $rgb_1[2]);
			$luma_2 = $this->get_luma($rgb_2[0], $rgb_2[1], $rgb_2[2]);

			return abs($luma_1 - $luma_2);
		}

		return false;
	}

	/**
	 * Choose the color that provides the greatest contrast to a third color
	 *
	 * @TODO make this more generic, 2 params first is an array of colors
	 *
	 * @param string $hex_1 Potential color choice, hex format
	 * @param string $hex_1 Potential color choice, hex format
	 * @param string $from_color Color that the greatest contrast is calculated from
	 * @return string|false hex value of the color chosen, false on failure
	 **/
	function greatest_contrast($hex_1, $hex_2, $from_color) {
		if ($this->luma_distance($hex_1, $from_color) > $this->luma_distance($hex_2, $from_color)) {
			return $hex_1;
		}
		return $hex_2;
	}

	/**
	 * Break a hexadecimal string representation into its RGB components.
	 *
	 * @param string $hex_string 
	 * @return array|false Array with RGB values with keys 0,1,2 respectively. 
	 *						False if an invalid hex color string was passed in
	 **/
	function rgbify_color($hex_string) {
		$hex_string = trim(str_replace('#', '', $hex_string));
		if (strlen($hex_1) == 3) {
			$hex_1 = $hex_1 + $hex_1;
		}

		if (preg_match('/^[a-fA-F0-9]{6}$/i', $hex_string)) {
			return str_split($hex_string, 2);
		}

		return false;
	}

	/**
	 * Calculate luma from a set of RGB colors
	 * see http://en.wikipedia.org/wiki/YIQ for value reference
	 * @return float luma value
	 **/
	function get_luma($red, $blue, $green) {
		return (hexdec($red) * 299) + (hexdec($blue) * 587) + (hexdec($green) * 114) / 1000;
	}
}
