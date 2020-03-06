<div class="am-plugins-holder wpcbtn-clear">
  <?php foreach ( $this->plugins_holder->all_am_plugins as $key => $plugin ) : ?>
	<?php
	$is_url_external = false;

	$data = $this->plugins_holder->get_about_plugins_data( $plugin );

	if ( isset( $plugin['pro'] ) && \array_key_exists( $plugin['pro']['path'], \get_plugins() ) ) {
		$is_url_external = true;
		$plugin          = $plugin['pro'];

		$data = array_merge( $data, $this->plugins_holder->get_about_plugins_data( $plugin, true ) );
	} 
	
	else 
		
	if ( isset( $plugin['pro'] ) 
			&& ! \array_key_exists( $plugin['pro']['path'], \get_plugins() )
			&& \array_key_exists( $plugin['path'], \get_plugins() )						 
		) {
		$is_url_external = true;
		$plugin['url']          = $plugin['pro']['url'];

		$data = array_merge( $data, $this->plugins_holder->get_about_plugins_data( $plugin, true ) );
		$data['action_class'] = str_replace( 
			'disabled', 'wpcallbtn-button-upgrade', ( 
				str_replace( 'button-secondary', 'button-primary', $data['action_class'] ) 
			) 
		);
		$data['status_class'] = 'status-active-can-up';
		$data['action_text'] = esc_attr__( 'Upgrade to Pro', 'wp-call-button' );
	}

	?>
		<div class="plugin-item">
			<div class="details">
				<img src="<?php echo \esc_url( $plugin['icon'] ); ?>">
				<h5 class="plugin-name">
					<?php echo $plugin['name']; ?>
				</h5>
				<p class="plugin-desc">
					<?php echo $plugin['desc']; ?>
				</p>
			</div>
			<div class="actions wpcbtn-clear">
				<div class="status">
					<strong>
						<?php
						\printf(
							/* translators: %s - status HTML text. */
							\esc_html__( 'Status: %s', 'wp-call-button' ),
							'<span class="status-label ' . $data['status_class'] . '">' . $data['status_text'] . '</span>'
						);
						?>
					</strong>
				</div>
				<div class="action-button">
					<?php
					$go_to_class = '';
					$target = '';
					if ( $is_url_external && $data['status_class'] === 'status-download' ) {
						$go_to_class = 'go_to';
					}
					if ( $is_url_external && $data['status_class'] === 'status-active-can-up' ) {
						$target = ' target="_blank" ';
					}
					
					// Print url.
					$cta_url_attr = ( strpos( $data['action_class'], 'disabled' ) !== false ) ? '' : ' href="' . esc_url( $plugin['url'] ) .'" ';
					?>
					<a <?php echo $target; ?>  <?php echo $cta_url_attr; ?>
						class="<?php echo \esc_attr( $data['action_class'] ); ?> <?php echo $go_to_class; ?>"
						data-plugin="<?php echo $data['plugin_src']; ?>">
						<?php echo $data['action_text']; ?>
					</a>
				</div>
			</div>
		</div>
  <?php endforeach; ?>
</div>