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
	 * Deactivate
	 *
	 * @since  1.1
	 * @access public
	 */
	public static function deactivate()
	{
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
	 * @since  1.1.2
	 * @access public
	 */
	// public static function activation_redirect( $plugin )
	// {
	// 	if( $plugin == plugin_basename( __FILE__ ) ) {
	// 		wp_redirect( menu_page_url( 'dologin', 0 ) );
	// 		exit;
	// 	}
	// }

}