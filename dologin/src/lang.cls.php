<?php
/**
 * Language class
 *
 * @since 1.0
 */
namespace dologin;

defined( 'WPINC' ) || exit;

class Lang extends Instance
{
	protected static $_instance;

	/**
	 * Init hook
	 * @since  1.4.7
	 */
	public function init()
	{
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) ) ;
	}

	/**
	 * Plugin loaded hooks
	 * @since 1.4.7
	 */
	public function plugins_loaded()
	{
		load_plugin_textdomain( 'dologin', false, 'dologin/lang/' ) ;
	}

	public static function msg( $tag, $num = null )
	{
		switch ( $tag ) {
			case 'try_after' :
				$msg = sprintf( __( 'Please try after %ds.', 'dologin' ), $num );
				break;

			case 'not_phone_set_curr' :
				$msg = __( 'No Dologin Security phone number set under your profile.', 'dologin' );
				break;

			case 'not_phone_set_user' :
				$msg = __( 'No phone number under this user profile.', 'dologin' );
				break;

			case 'empty_u_p' :
				$msg = __( 'Empty username/password.', 'dologin' );
				break;

			case 'not_in_whitelist' :
				$msg = __( 'Your IP is not in the whitelist.', 'dologin' );
				break;

			case 'in_blacklist' :
				$msg = __( 'Your IP is in the blacklist.', 'dologin' );
				break;

			case 'max_retries_hit' :
				$msg = __( 'Too many failed login attempts.', 'dologin' );
				break;

			case 'under_protected' :
				$msg = __( 'ON', 'dologin' );
				break;

			case 'max_retries' :
				$msg = sprintf( __( '%s attempt(s) remaining.', 'dologin' ), '<strong>' . $num . '</strong>' );
				break;

			case 'sms_missing' :
				$msg = __( 'Dynamic code is required.', 'dologin' );
				break;

			case 'sms_wrong' :
				$msg = __( 'Dynamic code is not correct.', 'dologin' );
				break;

			default:
				$msg = 'unknown msg';
				break;
		}

		return __( 'DoLogin Security', 'dologin' ) . ': ' . $msg;
	}
}
