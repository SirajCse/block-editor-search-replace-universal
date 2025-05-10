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

function besnr_register_setting_fields() {
	register_setting( BESNR_SETTINGS_SLUG, 'besnr_compact_mode', __NAMESPACE__ . '\besnr_sanitize_compact_mode' );
	register_setting( BESNR_SETTINGS_SLUG, 'besnr_dry_run', __NAMESPACE__ . '\besnr_sanitize_dry_run' );
	register_setting( BESNR_SETTINGS_SLUG, 'besnr_editors_supported', __NAMESPACE__ . '\besnr_sanitize_editors_supported' );
	register_setting( BESNR_SETTINGS_SLUG, 'besnr_types_supported', __NAMESPACE__ . '\besnr_sanitize_types_supported' );
	register_setting( BESNR_SETTINGS_SLUG, 'besnr_user_access', __NAMESPACE__ . '\besnr_sanitize_user_access' );
}

add_action( 'admin_init', __NAMESPACE__ . '\besnr_register_setting_fields' );
