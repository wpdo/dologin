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
			<th scope="row" valign="top"><?php echo __( 'Whitelist', 'dologin' ); ?></th>
			<td>
				<div class="field-col">
					<textarea name="whitelist" rows="15" cols="80"><?php echo esc_textarea( implode( "\n", Core::conf( 'whitelist', array() ) ) ); ?></textarea>
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
					<p class="description"><?php echo __( 'Example', 'dologin' ); ?> 3) <code>continent: North America, country_code: US, subdivisions_code: NY</code></p>
					<p class="description"><?php echo __( 'Example', 'dologin' ); ?> 4) <code>subdivisions_code: NY, postal: 10001</code></p>
					<p class="description">
						<button type="button" class="button button-link" id="dologin_get_ip"><?php echo __( 'Get my GeoLocation data from', 'dologin' ); ?> doapi.us</button>
						<code id="dologin_mygeolocation">-</code>
					</p>
				</div>
			</td>
		</tr>

		<tr>
			<th scope="row" valign="top"><?php echo __( 'Blacklist', 'dologin' ); ?></th>
			<td>
				<div class="field-col">
					<textarea name="blacklist" rows="15" cols="80"><?php echo esc_textarea( implode( "\n", Core::conf( 'blacklist', array() ) ) ); ?></textarea>
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