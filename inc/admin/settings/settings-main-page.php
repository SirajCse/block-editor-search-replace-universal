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

$besnr_admin = new BESNR_Admin();

?>
<div class="besnr-admin">
	<div class="besnr-container">
		<div class="besnr-pro">
			<h4>
				<?php echo esc_html__( 'Get the PRO version today!', 'block-editor-search-replace' ); ?>
			</h4>
			<p>
				<?php echo esc_html__( 'The PRO version offers more features, improved performance, and a faster recovery process.', 'block-editor-search-replace' ); ?>
			</p>
			<table>
				<tr>
					<th><?php echo esc_html__( 'Feature', 'block-editor-search-replace' ); ?></th>
					<th><?php echo esc_html__( 'Free', 'block-editor-search-replace' ); ?></th>
					<th><?php echo esc_html__( 'PRO', 'block-editor-search-replace' ); ?></th>
				</tr>
				<tr>
					<td><?php echo esc_html__( 'Supported types', 'block-editor-search-replace' ); ?></td>
					<td><?php echo esc_html__( 'posts & pages', 'block-editor-search-replace' ); ?></td>
					<td><?php echo esc_html__( 'posts, pages and CPTs', 'block-editor-search-replace' ); ?></td>
				</tr>
				<tr>
					<td><?php echo esc_html__( 'Dry-run', 'block-editor-search-replace' ); ?></td>
					<td><?php echo esc_html__( 'no', 'block-editor-search-replace' ); ?></td>
					<td><?php echo esc_html__( 'yes', 'block-editor-search-replace' ); ?></td>
				</tr>
				<tr>
					<td><?php echo esc_html__( 'Highlight search input', 'block-editor-search-replace' ); ?></td>
					<td><?php echo esc_html__( 'yes', 'block-editor-search-replace' ); ?></td>
					<td><?php echo esc_html__( 'yes', 'block-editor-search-replace' ); ?></td>
				</tr>
				<tr>
					<td><?php echo esc_html__( 'Case sensitive search & replace', 'block-editor-search-replace' ); ?></td>
					<td><?php echo esc_html__( 'yes', 'block-editor-search-replace' ); ?></td>
					<td><?php echo esc_html__( 'yes', 'block-editor-search-replace' ); ?></td>
				</tr>
				<tr>
					<td><?php echo esc_html__( 'Multiple terms, Images and Links', 'block-editor-search-replace' ); ?></td>
					<td><?php echo esc_html__( 'yes', 'block-editor-search-replace' ); ?></td>
					<td><?php echo esc_html__( 'yes', 'block-editor-search-replace' ); ?></td>
				</tr>
				<tr>
					<td><?php echo esc_html__( 'Shortcodes, HTML, and RegEx', 'block-editor-search-replace' ); ?></td>
					<td><?php echo esc_html__( 'no', 'block-editor-search-replace' ); ?></td>
					<td><?php echo esc_html__( 'yes', 'block-editor-search-replace' ); ?></td>
				</tr>
				<tr>
					<td><?php echo esc_html__( 'Partial Image & Link URL replacements', 'block-editor-search-replace' ); ?></td>
					<td><?php echo esc_html__( 'no', 'block-editor-search-replace' ); ?></td>
					<td><?php echo esc_html__( 'yes', 'block-editor-search-replace' ); ?></td>
				</tr>
				<tr>
					<td><?php echo esc_html__( 'Disable text characters limit and sanitization.', 'block-editor-search-replace' ); ?></td>
					<td><?php echo esc_html__( 'no', 'block-editor-search-replace' ); ?></td>
					<td><?php echo esc_html__( 'yes', 'block-editor-search-replace' ); ?></td>
				</tr>
				<tr>
					<td><?php echo esc_html__( 'Multilingual support', 'block-editor-search-replace' ); ?></td>
					<td><?php echo esc_html__( 'no', 'block-editor-search-replace' ); ?></td>
					<td><?php echo esc_html__( 'yes', 'block-editor-search-replace' ); ?></td>
				</tr>
				<tr>
					<td><?php echo esc_html__( 'Dedicated backup and restore functionality', 'block-editor-search-replace' ); ?></td>
					<td><?php echo esc_html__( 'no', 'block-editor-search-replace' ); ?></td>
					<td><?php echo esc_html__( 'yes', 'block-editor-search-replace' ); ?></td>
				</tr>
				<tr>
					<td><?php echo esc_html__( 'Restrict access by user type', 'block-editor-search-replace' ); ?></td>
					<td><?php echo esc_html__( 'yes', 'block-editor-search-replace' ); ?></td>
					<td><?php echo esc_html__( 'yes', 'block-editor-search-replace' ); ?></td>
				</tr>
				<tr>
					<td><?php echo esc_html__( 'Priority email support', 'block-editor-search-replace' ); ?></td>
					<td><?php echo esc_html__( 'no', 'block-editor-search-replace' ); ?></td>
					<td><?php echo esc_html__( 'yes', 'block-editor-search-replace' ); ?></td>
				</tr>
				<tr>
					<td><?php echo esc_html__( 'Regular plugin updates', 'block-editor-search-replace' ); ?></td>
					<td><?php echo esc_html__( 'delayed', 'block-editor-search-replace' ); ?></td>
					<td><?php echo esc_html__( 'first release', 'block-editor-search-replace' ); ?></td>
				</tr>
			</table>
			<p class="button-group">
				<a
					class="button button-primary button-pro"
					href="https://bit.ly/3Q1p1dP"
					target="_blank"
				>
					<?php echo esc_html__( 'GET PRO VERSION', 'block-editor-search-replace' ); ?>
				</a>
				<a
					class="button button-primary button-watch-video"
					href="https://www.youtube.com/watch?v=zWxPv8pJH4U"
					target="_blank"
				>
					<?php echo esc_html__( 'Watch Video', 'block-editor-search-replace' ); ?>
				</a>
			</p>
		</div>
		<h2>
			<?php echo esc_html__( 'Block Editor Search & Replace Pro', 'block-editor-search-replace' ); ?>
		</h2>
		<p>
			<?php
			printf(
				wp_kses(
					__( 'Easily search and replace text, images or links in the Block Editor, with backward compatibility for the Classic Editor.', 'block-editor-search-replace' ),
					json_decode( BESNR_PLUGIN_ALLOWED_HTML_ARR )
				),
			);
			?>
		</p>
		<p>
			<?php
			printf(
				wp_kses(
					/* translators: %1$s is replaced with "Important" */
					/* translators: %2$s is replaced with "Save" */
					__( '%1$s: Click the "%2$s" button below to apply any changes you make to the options.', 'block-editor-search-replace' ),
					json_decode( BESNR_PLUGIN_ALLOWED_HTML_ARR )
				),
				'<strong>' . esc_html__( 'Important', 'block-editor-search-replace' ) . '</strong>',
				'<strong>' . esc_html__( 'Save', 'block-editor-search-replace' ) . '</strong>'
			);
			?>
		</p>
		<form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
			<div id="besnr-output" class="notice is-dismissible besnr-output"></div>
			<?php settings_errors( 'besnr_settings_errors' ); ?>
			<?php wp_nonce_field( 'besnr_settings_nonce', 'besnr_wpnonce' ); ?>
			<?php
				settings_fields( BESNR_SETTINGS_SLUG );
				do_settings_sections( BESNR_SETTINGS_SLUG );
			?>
			<p class="submit button-group">
				<button type="submit" class="button button-primary" id="besnr-save-settings" name="besnr-save-settings">
					<?php echo esc_html__( 'Save', 'block-editor-search-replace' ); ?>
				</button>
				<button type="button" class="button" id="besnr-reset-settings" name="besnr-reset-settings">
					<?php echo esc_html__( 'Reset', 'block-editor-search-replace' ); ?>
				</button>
			</p>
		</form>
		<br clear="all" />
		<hr />
		<div class="besnr-support-credits">
			<p>
				<?php
				printf(
					wp_kses(
						/* translators: %1$s is replaced with "Support Forum" */
						__( 'If something is unclear, please open a ticket on the official plugin %1$s. All tickets will be addressed within a couple of working days.', 'block-editor-search-replace' ),
						json_decode( BESNR_PLUGIN_ALLOWED_HTML_ARR, true )
					),
					'<a href="' . esc_url( BESNR_PLUGIN_WPORG_SUPPORT ) . '" target="_blank">' . esc_html__( 'Support Forum', 'block-editor-search-replace' ) . '</a>'
				);
				?>
			</p>
			<p>
				<strong><?php echo esc_html__( 'Please rate us', 'block-editor-search-replace' ); ?></strong>
				<a href="<?php echo esc_url( BESNR_PLUGIN_WPORG_RATE ); ?>" target="_blank">
					<img src="<?php echo esc_url( BESNR_PLUGIN_DIR_URL ); ?>assets/dist/img/rate.png" alt="Rate us @ WordPress.org" />
				</a>
			</p>
			<p>
				<strong><?php echo esc_html__( 'Having issues?', 'block-editor-search-replace' ); ?></strong>
				<a href="<?php echo esc_url( BESNR_PLUGIN_WPORG_SUPPORT ); ?>" target="_blank">
					<?php echo esc_html__( 'Create a Support Ticket', 'block-editor-search-replace' ); ?>
				</a>
			</p>
			<p>
				<strong><?php echo esc_html__( 'Developed by', 'block-editor-search-replace' ); ?></strong>
				<a href="https://krasenslavov.com/" target="_blank">
					<?php echo esc_html__( 'Krasen Slavov @ Developry', 'block-editor-search-replace' ); ?>
				</a>
			</p>
		</div>
		<hr />
		<p>
			<?php
			printf(
				wp_kses(
					/* translators: %1$s is replaced with "Ctrl" */
					/* translators: %2$s is replaced with "Shift" */
					/* translators: %3$s is replaced with "Cmd" */
					__( '• Use the %1$s, %2$s, or %3$s keys to select multiple supported types or user access roles.', 'block-editor-search-replace' ),
					json_decode( BESNR_PLUGIN_ALLOWED_HTML_ARR, true )
				),
				'<code>' . esc_html__( 'Ctrl', 'block-editor-search-replace' ) . '</code>',
				'<code>' . esc_html__( 'Shift', 'block-editor-search-replace' ) . '</code>',
				'<code>' . esc_html__( 'Cmd', 'block-editor-search-replace' ) . '</code>'
			);
			?>
		</p>
	</div>
</div>
