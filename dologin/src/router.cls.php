<?php
/**
 * Router class
 *
 * @since 1.4
 */
namespace dologin;

defined( 'WPINC' ) || exit;

class Router extends Instance
{
	protected static $_instance;

	const NONCE = 'dologin_nonce';
	const ACTION = 'dologin_action';
	const TYPE = 'dologin_type';
	const I = 'dologin_i';

	const ACTION_PSWD = 'pswd';

	private static $_action;

	/**
	 * Init
	 */
	public function init()
	{
		add_action( 'init', array( $this, 'after_user_init' ) );
	}

	/**
	 * Proceed actions
	 *
	 * @since  1.4
	 */
	public function after_user_init()
	{
		$action = Router::get_action();
		switch ( $action ) {
			case self::ACTION_PSWD:
				Pswd::get_instance()->handler();
				break;

			default:
				break;
		}

		if ( $action ) {
			self::redirect();
		}
	}

	/**
	 * Redirect page and drop self params
	 *
	 * @since  1.4
	 */
	public static function redirect( $url = false )
	{
		global $pagenow;
		$qs = '';
		if ( ! $url ) {
			if ( ! empty( $_GET ) ) {
				if ( isset( $_GET[ self::ACTION ] ) ) {
					unset( $_GET[ self::ACTION ] );
				}
				if ( isset( $_GET[ self::NONCE ] ) ) {
					unset( $_GET[ self::NONCE ] );
				}
				if ( isset( $_GET[ self::TYPE ] ) ) {
					unset( $_GET[ self::TYPE ] );
				}
				if ( isset( $_GET[ self::I ] ) ) {
					unset( $_GET[ self::I ] );
				}
				if ( ! empty( $_GET ) ) {
					$qs = '?' . http_build_query( $_GET );
				}
			}
			if ( is_network_admin() ) {
				$url = network_admin_url( $pagenow . $qs );
			}
			else {
				$url = admin_url( $pagenow . $qs );
			}
		}

		wp_redirect( $url );
		exit();
	}

	/**
	 * Parse action
	 *
	 * @since  1.4
	 */
	public static function get_action()
	{
		if ( ! isset( self::$_action ) ) {
			self::$_action = false;
			self::get_instance()->verify_action();
			if ( self::$_action ) {
				function_exists( 'debug' ) && debug( '[Router] do_login action verified: ' . var_export( self::$_action, true ) );
			}

		}
		return self::$_action;
	}

	/**
	 * Verify action
	 *
	 * @since  1.4
	 */
	private function verify_action()
	{
		if ( empty( $_REQUEST[ Router::ACTION ] ) ) {
			function_exists( 'debug2' ) && debug2( '[Router] do_login action bypassed empty' );
			return;
		}

		$action = $_REQUEST[ Router::ACTION ];

		if ( ! $this->verify_nonce( $action ) ) {
			return;
		}

		$_can_option = current_user_can( 'manage_options' );

		switch ( $action ) {
			case self::ACTION_PSWD:
				if ( $_can_option ) {
					self::$_action = $action;
				}
				return;

			default:
				function_exists( 'debug' ) && debug( '[Router] do_login match falied: ' . $action );
				return;
		}

	}

	/**
	 * Verify nonce
	 *
	 * @since  1.4
	 */
	private function verify_nonce( $action )
	{
		if ( ! isset( $_REQUEST[ Router::NONCE ] ) || ! wp_verify_nonce( $_REQUEST[ Router::NONCE ], $action ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get type value
	 *
	 * @since 1.4
	 * @access public
	 */
	public static function verify_type()
	{
		if ( empty( $_REQUEST[ self::TYPE ] ) ) {
			function_exists( 'debug' ) && debug( '[Router] no type', 2 ) ;
			return false ;
		}

		function_exists( 'debug' ) && debug( '[Router] parsed type: ' . $_REQUEST[ self::TYPE ], 2 ) ;

		return $_REQUEST[ self::TYPE ] ;
	}

}