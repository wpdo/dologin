<?php
/**
 * Utility class
 *
 * @since 1.1
 */
namespace dologin;

defined( 'WPINC' ) || exit;

class Util extends Instance
{
	protected static $_instance;

	/**
	 * Init Utility
	 *
	 * @since 1.1
	 * @access public
	 */
	public function init()
	{
		if ( Conf::val( 'auto_upgrade' ) ) {
			add_filter( 'auto_update_plugin', array( $this, 'auto_update' ), 10, 2 );
		}
	}

	/**
	 * Handle auto update
	 *
	 * @since 1.1
	 * @access public
	 */
	public function auto_update( $update, $item )
	{
		if ( $item->slug == 'dologin' ) {
			$auto_v = self::version_check( 'auto_update_plugin' ) ;

			if ( $auto_v && ! empty( $item->new_version ) && $auto_v === $item->new_version ) {
				return true ;
			}
		}

		return $update; // Else, use the normal API response to decide whether to update or not
	}

	/**
	 * Version check
	 *
	 * @since 1.1
	 * @access public
	 */
	public static function version_check( $tag )
	{
		// Check latest stable version allowed to upgrade
		$url = 'https://doapi.us/compatible_list/dologin?v=' . Core::VER . '&v2=' . ( defined( 'DOLOGIN_CUR_V' ) ? DOLOGIN_CUR_V : '' ) . '&src=' . $tag ;

		$response = wp_remote_get( $url, array( 'timeout' => 15 ) ) ;
		if ( ! is_array( $response ) || empty( $response[ 'body' ] ) ) {
			return false ;
		}

		return $response[ 'body' ] ;
	}

	/**
	 * Set seconds/timestamp to readable format
	 *
	 * @since  1.2
	 * @access public
	 */
	public static function readable_time( $seconds_or_timestamp, $timeout = 3600, $backward = true )
	{
		if ( strlen( $seconds_or_timestamp ) == 10 ) {
			$seconds = time() - $seconds_or_timestamp;
			if ( $seconds > $timeout ) {
				return date( 'm/d/Y H:i:s', $seconds_or_timestamp + get_option( 'gmt_offset' ) * 60 * 60 );
			}
		}
		else {
			$seconds = $seconds_or_timestamp;
		}
		$res = '';
		if ( $seconds > 86400 ) {
			$num = floor( $seconds / 86400 );
			$res .= $num . 'd';
			$seconds %= 86400;
		}
		if ( $seconds > 3600 ) {
			if ( $res ) {
				$res .= ', ';
			}
			$num = floor( $seconds / 3600 );
			$res .= $num . 'h';
			$seconds %= 3600;
		}
		if ( $seconds > 60 ) {
			if ( $res ) {
				$res .= ', ';
			}
			$num = floor( $seconds / 60 );
			$res .= $num . 'm';
			$seconds %= 60;
		}
		if ( $seconds > 0 ) {
			if ( $res ) {
				$res .= ' ';
			}
			$res .= $seconds . 's';
		}
		if ( ! $res ) {
			return $backward ? __( 'just now', 'dologin' ) : __( 'right now', 'dologin' );
		}
		$res = $backward ? sprintf( __( ' %s ago', 'dologin' ), $res ) : $res;
		return $res;
	}

	/**
	 * Deactivate
	 *
	 * @since  1.1
	 * @access public
	 */
	public static function deactivate()
	{
		delete_transient( 'dologin_activation_redirect' );

		self::version_check( 'deactivate' ) ;

		Data::get_instance()->del_tables();
	}

	/**
	 * Uninstall clearance
	 *
	 * @since  1.1
	 * @access public
	 */
	public static function uninstall()
	{
		self::version_check( 'uninstall' ) ;

		Data::get_instance()->del_tables();
	}

	/**
	 * Activation redirect
	 *
	 * @since  1.2.2
	 * @access public
	 */
	public static function activate()
	{
		set_transient( 'dologin_activation_redirect', true, 30 );
	}

}