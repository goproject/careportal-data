<?php
/*
Plugin Name: CarePortal Data
Version: 2.6
Description: Makes shortcodes for Impact Report data
Author: Topher
Author URI: http://topher1kenobe.com
Text Domain: careportal-data
Domain Path: /languages
*/

/**
 * Provides shortcodes to render CarePortal Data
 *
 * @package Careportal_Data
 * @since Careportal_Data 1.0
 * @author Topher
 */

// instantiate the class
$careportal_data_shortcodes = new Careportal_Data;

/**
 * Main Careportal Data Class
 *
 * Contains the main functions for rendering Careportal Data
 *
 * @class Careportal_Data
 * @version 2.1.0
 * @since 1.0
 * @package Careportal_Data
 * @author Topher
 */
class Careportal_Data {

	/**
	 * Class constructor
	 *
	 * @access public
	 * @return	null
	 */
	public function __construct() {

		$this->init();

	}

	/**
	 * Initializes various WordPress functions
	 *
	 * @access	protected
	 * @return	null
	 */
	protected function init() {

		// create the map shortcode
		add_shortcode( 'careportal_data', array( $this, 'get_value' ) );

	}

	/**
	 * Gets JSON data from local files.  Uses _GET variables
	 *
	 * @access	protected
	 * @return	null
	 */
	private function get_data( $source = '' ) {

		$output = '';

		$json = '';

		$impact_area = 'default';

		// check to see if GET var exists.	If so, set var to _GET var.
		if ( isset( $_GET['impact-area'] ) && '' != sanitize_text_field( $_GET['impact-area'] ) ) { // Input var okay.
			$impact_area = sanitize_text_field( $_GET['impact-area'] ); // Input var okay.
		}

		// check to see if input var exists.  If so, set var to $source var.
		if ( isset( $source ) && '' != ( $source ) ) {
			$impact_area = sanitize_text_field( $source );
		}

		// check to see if file exists. If so, open it up
		if ( file_exists( plugin_dir_path( __FILE__ ) . '/json/' . sanitize_text_field( $impact_area ) . '.php' ) ) {
			$json = file_get_contents( plugin_dir_path( __FILE__ ) . '/json/' . sanitize_text_field( $impact_area ) . '.php' ); // Input var okay.
		}

		// make sure we have content
		if ( 0 == strlen( $json ) ) {
			return;
		}

		// turn JSON into PHP array
		$array = json_decode( $json );

		return $array;

	}

	/**
	 * Choose the value of one item from the Array and return it for a shortcode
	 *
	 * @access	protected
	 * @param	array	$args
	 * @return	class	$output
	 */
	public function get_value( $args ) {

		$output = '';

		$keyword = '';

		// find out if we're looking at a custom data source or not
		if ( isset( $args['file'] ) && '' != $args['file'] ) {
			$file = $args['file'];
		} else {
			$file = '';
		}

		// find out if we're looking for a raw integer or not
		if ( isset( $args['raw'] ) && 'true' == $args['raw'] ) {
			$raw = 'true';
		} else {
			$raw = '';
		}

		// go get the data
		$data = $this->get_data( $file );

		if ( ! empty( $args['keyword'] ) ) {

			if ( 'last_updated' == $args['keyword'] ) {
				$keyword = $args['keyword'];
				$output = date( 'l, F j, Y g:i A', strtotime( $data->$keyword . ' +2 hours' ) );
			} else {
				$keyword = sanitize_text_field( $args['keyword'] );

				if ( is_numeric( $data->$keyword ) && '' == $raw ) {
					$data->$keyword = number_format( round( $data->$keyword ) );
				}

				$output = $data->$keyword;
			}
		}

		return $output;

	}

}
