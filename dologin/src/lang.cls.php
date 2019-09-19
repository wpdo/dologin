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
	public static function msg( $tag )
	{
		switch ( $tag ) {
			case 'not_in_whitelist' :
				$msg = __( 'Your IP is not in the whitelist.', 'dologin' );
				break;

			case 'in_blacklist' :
				$msg = __( 'Your IP is in the blacklist.', 'dologin' );
				break;

			default:
				$msg = 'unknown msg';
				break;
		}

		return __( 'Login Security', 'dologin' ) . ': ' . $msg;
	}
}
