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
 * Display a notice encouraging users to rate the plugin
 * on WordPress.org and provide options to dismiss the notice.
 */
function besnr_display_rating_notice() {
	$besnr_admin = new BESNR_Admin();

	$current_screen = get_current_screen();

	if ( ! get_option( 'besnr_rating_notice', '' ) && strpos( $current_screen->id, 'besnr_' ) ) {
		?>
			<div class="notice notice-success is-dismissible besnr-admin">
				<h3>
					<?php echo esc_html( BESNR_PLUGIN_NAME ); ?>  🚀
				</h3>
				<p>
					<?php
					printf(
						wp_kses(
							/* translators: %1$s is replaced with "by giving it 5 stars rating" */
							__( '✨💪🔌 Could you kindly support the plugin %1$s? Thank you in advance!', 'block-editor-search-replace' ),
							json_decode( BESNR_PLUGIN_ALLOWED_HTML_ARR, true )
						),
						'<strong>' . esc_html__( 'by giving it 5 stars rating', 'block-editor-search-replace' ) . '</strong>'
					);
					?>
				</p>
				<div class="button-group">
					<a href="<?php echo esc_url( BESNR_PLUGIN_WPORG_RATE ); ?>" target="_blank" class="button button-primary">
						<?php echo esc_html__( 'Rate us @ WordPress.org', 'block-editor-search-replace' ); ?>
						<i class="dashicons dashicons-external"></i>
					</a>
					<a href="<?php echo esc_url( admin_url( $besnr_admin->admin_page . BESNR_SETTINGS_SLUG . '&_wpnonce=' . wp_create_nonce( 'besnr_rating_notice_nonce' ) . '&action=besnr_dismiss_rating_notice' ) ); ?>" class="button">
						<?php echo esc_html__( 'I already did', 'block-editor-search-replace' ); ?>
					</a>
					<a href="<?php echo esc_url( admin_url( $besnr_admin->admin_page . BESNR_SETTINGS_SLUG . '&_wpnonce=' . wp_create_nonce( 'besnr_rating_notice_nonce' ) . '&action=besnr_dismiss_rating_notice' ) ); ?>" class="button">
						<?php echo esc_html__( "Don't show this notice again!", 'block-editor-search-replace' ); ?>
					</a>
				</div>
			</div>
		<?php
	}
}

add_action( 'admin_notices', __NAMESPACE__ . '\besnr_display_rating_notice' );
