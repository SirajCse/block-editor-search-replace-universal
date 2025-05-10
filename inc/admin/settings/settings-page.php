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

function besnr_display_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'You do not have sufficient permissions to access this page.' );
	}

	// 1. Media Library Settings.
	add_settings_section(
		BESNR_SETTINGS_SLUG,
		'Settings',
		'',
		BESNR_SETTINGS_SLUG
	);

	add_settings_field(
		'besnr_types_supported',
		'<label for="besnr-types-supported">'
			. __( 'Post Types Supported', 'block-editor-search-replace' )
			. '</label>',
		__NAMESPACE__ . '\besnr_display_types_supported',
		BESNR_SETTINGS_SLUG,
		BESNR_SETTINGS_SLUG,
	);

	add_settings_field(
		'besnr_editors_supported',
		'<label for="besnr-editors-supported">'
			. __( 'Editors Supported', 'block-editor-search-replace' )
			. '</label>',
		__NAMESPACE__ . '\besnr_display_editors_supported',
		BESNR_SETTINGS_SLUG,
		BESNR_SETTINGS_SLUG,
	);

	add_settings_field(
		'besnr_user_access',
		'<label for="besnr-user-access">'
			. __( 'User Access', 'block-editor-search-replace' )
			. '</label>',
		__NAMESPACE__ . '\besnr_display_user_access',
		BESNR_SETTINGS_SLUG,
		BESNR_SETTINGS_SLUG,
	);

	add_settings_field(
		'besnr_compact_mode',
		'<label for="besnr-compact-mode">'
			. __( 'Compact Mode', 'block-editor-search-replace' )
			. '</label>',
		__NAMESPACE__ . '\besnr_display_compact_mode',
		BESNR_SETTINGS_SLUG,
		BESNR_SETTINGS_SLUG,
	);

	require_once BESNR_PLUGIN_DIR_PATH . 'inc/admin/settings/settings-main-page.php';
}
