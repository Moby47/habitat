<div class="notice notice-success is-dismissible <?php echo $this->plugin_slug; ?>-notice-welcome">
    <p>
        <?php printf(
                /* translators: 1: Name of Plugin 2: URL */
                __( 'Thanks for installing <b>%1$s</b>. <a href="%2$s">Click here</a> to configure the plugin.', 'wp-call-button' ),
                $this->plugin_name,
                esc_url( $setting_page )
            ); ?>
    </p>
</div>
<script type="text/javascript">
	jQuery(document).ready( function($) {
		$(document).on( 'click', '.<?php echo $this->plugin_slug; ?>-notice-welcome button.notice-dismiss', function( event ) {
			event.preventDefault();
			$.post( '<?php echo esc_url( $ajax_url ); ?>', {
				action: '<?php echo $this->plugin_slug . '_dismiss_dashboard_notices'; ?>',
				nonce: '<?php echo wp_create_nonce( $this->plugin_slug . '-nonce' ); ?>'
			});
			$( '.<?php echo $this->plugin_slug; ?>-notice-welcome' ).remove();
		});
	});
</script>