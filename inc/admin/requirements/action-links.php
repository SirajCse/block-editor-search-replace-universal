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
 * Add custom action links to the plugin on the Plugins page.
 */
function besnr_add_action_links( $links, $file_path ) {
	$besnr_admin = new BESNR_Admin();

	if ( BESNR_PLUGIN_BASENAME === $file_path ) {
		$links['besnr-settings'] = '<a href="'
			. esc_url( admin_url( $besnr_admin->admin_page . BESNR_SETTINGS_SLUG ) ) . '">'
			. esc_html__( 'Settings', 'block-editor-search-replace' )
			. '</a>';

		return array_reverse( $links );
	}

	return $links;
}
