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
 * Display the setting.
 */
function besnr_display_editors_supported() {
	$besnr = new Block_Editor_Search_Replace();

	$editors_supported = get_option( 'besnr_editors_supported', $besnr->editors_supported );

	$options_html = '';

	$editors_available = array(
		'classic',
		'block',
	);

	foreach ( $editors_available as $editor ) {
		$editor_text = ucwords( $editor );
		$selected    = '';

		if ( is_array( $editors_supported ) && in_array( $editor, $editors_supported, true ) ) {
			$selected = 'selected';
		}

		$options_html .= "<option value=\"{$editor}\" {$selected}>{$editor_text}</option>";
	}
	?>
		<select id="besnr-editors-supported" name="besnr_editors_supported[]" multiple>
			<?php echo wp_kses( $options_html, json_decode( BESNR_PLUGIN_ALLOWED_HTML_ARR, true ) ); ?>
		</select>
		<p class="description">
			<small>
				<?php echo esc_html__( 'Select supported editors by the plugin.', 'block-editor-search-replace' ); ?>
			</small>
		</p>
	<?php
}

/**
 * Sanitize and update the setting.
 */
function besnr_sanitize_editors_supported( $editors_supported ) {
	// Verify the nonce.
	$_wpnonce = ( isset( $_REQUEST['besnr_wpnonce'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['besnr_wpnonce'] ) ) : '';

	if ( empty( $_wpnonce ) || ! wp_verify_nonce( $_wpnonce, 'besnr_settings_nonce' ) ) {
		return;
	}

	// Nothing selected.
	if ( empty( $editors_supported ) ) {
		return;
	}

	// Option changed and updated.
	if ( ! get_transient( 'besnr_settings_editors_supported' )
		&& get_option( 'besnr_editors_supported', '' ) != $editors_supported ) { // Don't use strict comparsions to check that arrays are equal.
		add_settings_error(
			'besnr_settings_errors',
			'besnr_settings_editors_supported',
			esc_html__( 'Supported editors option was updated successfully.', 'block-editor-search-replace' ),
			'updated'
		);

		// Add transient to avoid double notice on initial Save when using settings_errors().
		set_transient( 'besnr_settings_editors_supported', true, 5 );
	}

	return array_map( 'sanitize_text_field', $editors_supported );
}
