<?php
/**
 * [Short description]
 *
 * @package    DEVRY\BESNR
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BESNR;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

/**
 * Handle actions upon activation of the plugin, such as
 * displaying notices or performing specific tasks.
 */
function besnr_activate_plugin( $plugin_file_path ) {
	if ( BESNR_PLUGIN_BASENAME === $plugin_file_path ) {
	}
}

add_action( 'activated_plugin', __NAMESPACE__ . '\besnr_activate_plugin' );

/**
 * Handle actions upon deactivation of the plugin,
 * such as removing stored notices or performing cleanup tasks.
 */
function besnr_deactivate_plugin( $plugin_file_path ) {
	if ( BESNR_PLUGIN_BASENAME === $plugin_file_path ) {
		delete_option( 'besnr_rating_notice' );
		delete_option( 'besnr_upgrade_notice' );
	}
}

add_action( 'deactivated_plugin', __NAMESPACE__ . '\besnr_deactivate_plugin' );
