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

	public function init()
	{
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	/**
	 * Admin setting page
	 *
	 * @since  1.0
	 * @access public
	 */
	public function admin_menu()
	{
		add_options_page( 'Login Security', 'Login Security', 'manage_options', 'dologin', array( $this, 'setting_page' ) );
	}

	/**
	 * Display and save options
	 *
	 * @since  1.0
	 * @access public
	 */
	public function setting_page()
	{
		if ( ! empty( $_POST ) ) {
			check_admin_referer( 'dologin' );
			// Save options
			$list = array(
				'max_retries'		=> $_POST[ 'max_retries' ],
				'lockout_duration'	=> $_POST[ 'lockout_duration' ],
				'sms'				=> $_POST[ 'sms' ],
				'whitelist'			=> $this->_sanitize_list( $_POST[ 'whitelist' ] ),
				'blacklist'			=> $this->_sanitize_list( $_POST[ 'blacklist' ] ),
			) ;
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


}