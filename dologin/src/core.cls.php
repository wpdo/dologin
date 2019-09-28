<?php
/**
 * Core class
 *
 * @since 1.0
 */
namespace dologin;

defined( 'WPINC' ) || exit;

class Core extends Instance
{
	protected static $_instance;

	const VER = '1.0';

	/**
	 * Init
	 *
	 * @since  1.0
	 * @access protected
	 */
	protected function __construct()
	{
		Conf::get_instance()->init();

		if ( is_admin() ) {
			Admin::get_instance()->init();
		}

		Auth::get_instance()->init();

		REST::get_instance()->init();

		Util::get_instance()->init();

		register_deactivation_hook( DOLOGIN_DIR . 'dologin.php', __NAMESPACE__ . '\Util::deactivate' ) ;
		register_uninstall_hook( DOLOGIN_DIR . 'dologin.php', __NAMESPACE__ . '\Util::uninstall' ) ;
	}
}
