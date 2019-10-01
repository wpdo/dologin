<?php
/**
 * Admin class
 *
 * @since 1.0
 */
namespace dologin;

defined( 'WPINC' ) || exit;

class Admin extends Instance
{
	protected static $_instance;

	/**
	 * Init admin
	 *
	 * @since  1.0
	 * @access public
	 */
	public function init()
	{
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_filter( 'plugin_action_links_dologin/dologin.php', array( $this, 'add_plugin_links' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	/**
	 * Admin setting page
	 *
	 * @since  1.0
	 * @access public
	 */
	public function admin_menu()
	{
		add_options_page( 'DoLogin Security', 'DoLogin Security', 'manage_options', 'dologin', array( $this, 'setting_page' ) );
	}

	/**
	 * admin_init
	 *
	 * @since  1.2.2
	 * @access public
	 */
	public function admin_init()
	{
		if ( get_transient( 'dologin_activation_redirect' ) ) {
			delete_transient( 'dologin_activation_redirect' );
			if ( ! is_network_admin() && ! isset( $_GET['activate-multi'] ) ) {
				wp_safe_redirect( menu_page_url( 'dologin', 0 ) );
			}
		}
	}

	/**
	 * Plugin link
	 *
	 * @since  1.1
	 * @access public
	 */
	public function add_plugin_links( $links )
	{
		$links[] = '<a href="' . menu_page_url( 'dologin', 0 ) . '">' . __( 'Settings', 'dologin' ) . '</a>';

		return $links;
	}

	/**
	 * Display and save options
	 *
	 * @since  1.0
	 * @access public
	 */
	public function setting_page()
	{
		Data::get_instance()->create_tb_failure();

		if ( ! empty( $_POST ) ) {
			check_admin_referer( 'dologin' );

			// Save options
			$list = array() ;

			foreach ( Conf::get_instance()->get_options() as $id => $v ) {
				if ( $id == '_ver' ) {
					continue;
				}

				$list[ $id ] = ! empty( $_POST[ $id ] ) ? $_POST[ $id ] : false ;
			}

			// Special handler for list
			$list[ 'whitelist' ] = $this->_sanitize_list( $_POST[ 'whitelist' ] );
			$list[ 'blacklist' ] = $this->_sanitize_list( $_POST[ 'blacklist' ] );

			foreach ( $list as $id => $v ) {
				Conf::update( $id, $v );
			}
		}

		require_once DOLOGIN_DIR . 'tpl/settings.tpl.php';
	}

	/**
	 * Sanitize list
	 *
	 * @since  1.0
	 * @access public
	 */
	private function _sanitize_list( $list )
	{
		if ( ! is_array( $list ) ) {
			$list = explode( "\n", trim( $list ) );
		}

		foreach ( $list as $k => $v ) {
			$list[ $k ] = implode( ', ', array_map( 'trim', explode( ',', $v ) ) );
		}

		return array_filter( $list );
	}

	/**
	 * Display failure log
	 *
	 * @since  1.1
	 * @access public
	 */
	public function log()
	{
		global $wpdb;
		return $wpdb->get_results( 'SELECT * FROM ' . Data::tb_failure() . ' ORDER BY id DESC LIMIT 10' );
	}
}