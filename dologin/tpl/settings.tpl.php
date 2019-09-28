<?php
namespace dologin;

defined( 'WPINC' ) || exit;

?>
<style>
	.login-security-settings .field-col {
		display: inline-block;
		margin-right: 20px;
	}

	.login-security-settings .field-col-desc{
		min-width: 540px;
		max-width: calc(100% - 640px);
		vertical-align: top;
	}
</style>
<div class="wrap login-security-settings">
	<h2><?php echo __( 'Login Security Settings', 'dologin' ); ?></h2>

	<form method="post" action="<?php menu_page_url( 'dologin' ); ?>" class="dologin-relative">
	<?php wp_nonce_field( 'dologin' ); ?>

	<table class="form-table">
		<tr>
			<th scope="row" valign="top"><?php echo __( 'Lockout', 'dologin' ); ?></th>
			<td>
				<p><input type="text" size="3" maxlength="4" name="max_retries" value="<?php echo Conf::val( 'max_retries' ); ?>" /> <?php echo __( 'Allowed retries', 'dologin' ); ?></p>

				<p><input type="text" size="3" maxlength="4" name="duration" value="<?php echo Conf::val( 'duration' ); ?>" /> <?php echo __( 'minutes lockout', 'dologin' ); ?></p>
				<p class="description"><?php echo sprintf( __( 'If hit %1$s maximum retries in %2$s minutes, the login attempt from that IP will be temporarily disabled.', 'dologin' ), '<code>' . Conf::val( 'max_retries' ) . '</code>', '<code>' . Conf::val( 'duration' ) . '</code>' ); ?></p>
			</td>
		</tr>

		<tr style="display: none;">
			<th scope="row" valign="top"><?php echo __( 'Login Security', 'dologin' ); ?></th>
			<td>
				<p><label><input type="checkbox" name="sms" value="1" <?php echo Conf::val( 'sms' ) ? 'checked' : '' ; ?> /> <?php echo __( 'Enable Two Step SMS Auth', 'dologin' ); ?></label></p>
				<p class="description">
					<?php echo __( 'Verify text code for each login attempt.', 'dologin' ); ?>
					<?php echo __( 'Users need to setup Phone in their profile.', 'dologin' ); ?>
					<?php echo sprintf( __( 'Text message is free sent by API from %s.', 'dologin' ), '<a href="https://www.doapi.us" target="_blank">DoAPI.us</a>' ); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row" valign="top"><?php echo __( 'Whitelist', 'dologin' ); ?></th>
			<td>
				<div class="field-col">
					<textarea name="whitelist" rows="15" cols="80"><?php echo esc_textarea( implode( "\n", Conf::val( 'whitelist' ) ) ); ?></textarea>
				</div>
				<div class="field-col field-col-desc">
					<p class="description">
						<?php echo __( 'Format', 'dologin' ); ?>: <code>prefix1:value1, prefix2:value2</code>.
						<?php echo __( 'Both prefix and value are case insensitive.', 'dologin' ); ?>
						<?php echo __( 'Spaces around comma/colon are allowed.', 'dologin' ); ?>
						<?php echo __( 'One rule set per line.', 'dologin' ); ?>
					</p>
					<p class="description">
						<?php echo __( 'Prefix list', 'dologin' ); ?>: <code>ip</code>, <code><?php echo implode( '</code>, <code>', IP::PREFIX_SET ); ?></code>.
					</p>
					<p class="description"><?php echo __( 'IP prefix with colon is optional. IP value support wildcard (*).', 'dologin' ); ?></p>
					<p class="description"><?php echo __( 'Example', 'dologin' ); ?> 1) <code>ip:1.2.3.*</code></p>
					<p class="description"><?php echo __( 'Example', 'dologin' ); ?> 2) <code>42.20.*.*, continent_code: NA</code> (<?php echo __( 'Dropped optional prefix', 'dologin' ); ?> <code>ip:</code>)</p>
					<p class="description"><?php echo __( 'Example', 'dologin' ); ?> 3) <code>continent: North America, country_code: US, subdivision_code: NY</code></p>
					<p class="description"><?php echo __( 'Example', 'dologin' ); ?> 4) <code>subdivision_code: NY, postal: 10001</code></p>
					<p class="description">
						<button type="button" class="button button-link" id="dologin_get_ip"><?php echo __( 'Get my GeoLocation data from', 'dologin' ); ?> DoAPI.us</button>
						<code id="dologin_mygeolocation">-</code>
					</p>
				</div>
			</td>
		</tr>

		<tr>
			<th scope="row" valign="top"><?php echo __( 'Blacklist', 'dologin' ); ?></th>
			<td>
				<div class="field-col">
					<textarea name="blacklist" rows="15" cols="80"><?php echo esc_textarea( implode( "\n", Conf::val( 'blacklist' ) ) ); ?></textarea>
				</div>
				<div class="field-col field-col-desc">
					<p class="description">
						<?php echo sprintf( __( 'Same format as %s', 'dologin' ), '<strong>' . __( 'Whitelist', 'dologin' ) . '</strong>' ); ?>
					</p>
				</div>
			</td>
		</tr>
	</table>

	<p class="submit">
		<?php submit_button(); ?>
	</p>
	</form>
</div>

<script>
	jQuery( function( $ ) {
		$( '#dologin_get_ip' ).click( function( e ) {
			$.ajax( {
				url: '<?php echo get_rest_url( null, 'dologin/v1/myip' ); ?>',
				dataType: 'json',
				success: function( data ) {
					var html = [];
					$.each( data, function( k, v ) {
						 html.push( k + ':' + v );
					});
					$( '#dologin_mygeolocation' ).html( html.join( ', ' ) ) ;
				}
			} ) ;
		} );
	} );
</script>