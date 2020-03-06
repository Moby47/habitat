<?php
/**
 * Step Three Template
 *
 * The template wrapper for the step three set up page.
 *
 * @package ShareThisShareButtons
 */

?>
<div id="sharethis-step-three-wrap">
	<div class="sharethis-setup-steps">
		<?php
		foreach ( $setup_steps as $num => $step ) :
			$step_class = 3 === $num ? 'current-step' : '';
			$step_class = 1 === $num || 2 === $num ? 'finished-step' : $step_class;
			$num = 1 === $num || 2 === $num ? '<img src="' . esc_url( "{$this->plugin->dir_url}/assets/finished-step.png" ) . '">' : $num;
			?>
			<span class="step-num <?php echo esc_attr( $step_class ); ?>"><?php echo wp_kses_post( $num ); ?></span>

			<div class="step-description"><?php echo esc_html( $step ); ?></div>

			<span class="step-spacer"></span>
		<?php endforeach; ?>
	</div>

	<h4>
		<?php echo esc_html__( 'You\'re almost done! Create an account then configure your WordPress settings.', 'sharethis-share-buttons' ); ?>
	</h4>

	<div class="sharethis-account-creation">
		<div class="page-content" data-size="small" style="text-align: left;">
			<span>
				<div class="c-red text-center lh-18 h-18"></div>
			</span>

			<div class="input">
				<label name="email" class="">Email</label>

				<input type="text" id="st-email" name="email">
			</div>
			<div class="" style="height: 16px; width: 100%;"></div>
			<div class="input " style="margin-bottom: 10px;">
				<label name="password">Create a password</label>

				<input type="password" id="st-password" name="password" minlength="6">
				<small><?php echo esc_html__( 'Password must be at least six characters.','sharethis-share-buttons' ); ?></small>
			</div>

			<span class="st-terms-checkbox">
				<input type="checkbox" class="checkbox filled-in" id="terms-checkbox" value="on">

				<label for="terms-checkbox" style="font-size: 14px; line-height: 14px; padding-top: 2px;">
					I agree to the <a href="https://www.sharethis.com/publisher-terms-of-use/" target="_blank">terms of use.</a>
				</label>
			</span>

			<a class="create-account st-rc-link" href="#">
				<?php esc_html_e( 'REGISTER & CONFIGURE', 'sharethis-share-buttons' ); ?>
			</a>
		</div>
	</div>
</div>
