<?php
/**
 * Language class
 *
 * @since 1.0
 */
namespace dologin;

defined( 'WPINC' ) || exit;

class Lang
{
	public static function msg( $tag, $num = null )
	{
		switch ( $tag ) {
			case 'not_in_whitelist' :
				$msg = __( 'Your IP is not in the whitelist.', 'dologin' );
				break;

			case 'in_blacklist' :
				$msg = __( 'Your IP is in the blacklist.', 'dologin' );
				break;

			case 'max_retries_hit' :
				$msg = __( 'Too many failed login attempts.', 'dologin' );
				break;

			case 'max_retries' :
				$msg = sprintf( __( '%s attempt(s) remaining.', 'dologin' ), '<strong>' . $num . '</strong>' );
				break;

			default:
				$msg = 'unknown msg';
				break;
		}

		return __( 'DoLogin Security', 'dologin' ) . ': ' . $msg;
	}
}
