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
 * [AJAX] Reset plugin settings to their default values
 * and provide a success message.
 */
function besnr_reset_settings() {
	$besnr_admin = new BESNR_Admin();

	delete_option( 'besnr_compact_mode' );
	delete_option( 'besnr_editors_supported' );
	delete_option( 'besnr_types_supported' );
	delete_option( 'besnr_user_access' );

	$besnr_admin->print_json_message(
		1,
		__( 'All options have been reset to their default values.', 'block-editor-search-replace' )
	);
}

add_action( 'wp_ajax_besnr_reset_settings', __NAMESPACE__ . '\besnr_reset_settings' );
