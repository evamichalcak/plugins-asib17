<?php
/*
 * Plugin Name: NG Vote
 * Plugin URI: https://www.nosegraze.com/voting-plugin-wordpress/
 * Description: A super simple voting plugin for techy minimalists.
 * Version: 1.0.0
 * Author: Nose Graze
 * Author URI: https://www.nosegraze.com
 * License: GPL2
*/

require_once plugin_dir_path( __FILE__ ) . 'includes/class-ng-vote.php';

/**
 * Returns an instance of the voting plugin object.
 * Basically gets the party started.
 *
 * @return NG_Vote
 */
function NG_Vote() {
	$instance = NG_Vote::instance( __FILE__, '1.0.0' );

	return $instance;
}

NG_Vote();