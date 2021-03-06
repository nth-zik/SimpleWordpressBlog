<?php
/**
 * Admin View: Page - Status Logs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<?php if ( $logs ) : ?>
	<div id="log-viewer-select">
		<div class="alignleft">
			<h2>
				<?php echo esc_html( $viewed_log ); ?>
				<?php if ( ! empty( $handle ) ) : ?>
					<a class="page-title-action" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'handle' => $handle ), admin_url( 'admin.php?page=evf-status&tab=logs' ) ), 'remove_log' ) ); ?>" class="button"><?php esc_html_e( 'Delete log', 'everest-forms' );?></a>
				<?php endif; ?>
			</h2>
		</div>
		<div class="alignright">
			<form action="<?php echo admin_url( 'admin.php?page=evf-status&tab=logs' ); ?>" method="post">
				<select name="log_file">
					<?php foreach ( $logs as $log_key => $log_file ) : ?>
						<option value="<?php echo esc_attr( $log_key ); ?>" <?php selected( sanitize_title( $viewed_log ), $log_key ); ?>><?php echo esc_html( $log_file ); ?> (<?php echo date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), filemtime( EVF_LOG_DIR . $log_file ) ); ?>)</option>
					<?php endforeach; ?>
				</select>
				<input type="submit" class="button" value="<?php esc_attr_e( 'View', 'everest-forms' ); ?>" />
			</form>
		</div>
		<div class="clear"></div>
	</div>
	<div id="log-viewer">
		<pre><?php echo esc_html( file_get_contents( EVF_LOG_DIR . $viewed_log ) ); ?></pre>
	</div>
<?php else : ?>
	<div class="updated everest-forms-message inline"><p><?php _e( 'There are currently no logs to view.', 'everest-forms' ); ?></p></div>
<?php endif; ?>
