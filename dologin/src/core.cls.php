<?php
/**
 * Core class
 *
 * @since 1.0
 */
namespace dologin;

defined( 'WPINC' ) || exit;

class Core
{
	private static $_instance;

	const PREFIX_SET = array(
		'continent',
		'continent_code',
		'country',
		'country_code',
		'city',
		'postal',
		'subdivisions',
		'subdivisions_code',
	);

	/**
	 * Init
	 *
	 * @since  1.0
	 * @access private
	 */
	private function __construct()
	{
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'login_head', array( $this, 'login_head' ) );

		REST::get_instance() ;
	}

	/**
	 * Login page display messages
	 *
	 * @since  1.0
	 * @access public
	 */
	public function login_head()
	{
		if ( $this->maybe_whitelist() ) {
			return;
		}
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

		return $list;
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
			$this->conf_update( 'whitelist', $this->_sanitize_list( $_POST[ 'whitelist' ] ) );
			$this->conf_update( 'blacklist', $this->_sanitize_list( $_POST[ 'blacklist' ] ) );
		}

		require_once DOLOGIN_DIR . 'tpl/settings.tpl.php';
	}

	/**
	 * Get visitor's IP
	 *
	 * @since  1.0
	 * @access public
	 */
	public static function ip()
	{
		return preg_replace( '/^(\d+\.\d+\.\d+\.\d+):\d+$/', '\1', $_SERVER[ 'REMOTE_ADDR' ] );
	}

	/**
	 * Get geolocation info of visitor IP
	 *
	 * @since 1.0
	 * @access public
	 */
	public function geo_ip()
	{
		$ip = self::ip();

		$response = wp_remote_get( "https://www.doapi.us/ip/$ip/json" );

		if ( is_wp_error( $response ) ) {
			return new \WP_Error( 'remote_get_fail', 'Failed to fetch geolocation info', array( 'status' => 404 ) );
		}

		$data = $response[ 'body' ];

		$data = json_decode( $data, true );

		// Combine data to prefix+value format
		$html = array( 'ip:' . $ip );
		foreach ( $data as $prefix => $v ) {
			if ( in_array( $prefix, self::PREFIX_SET ) ) {
				$html[] = $prefix . ':' . $v;
			}
		}

		return implode( ', ', $html );
	}

	/**
	 * Get option of dologin
	 *
	 * @since  1.0
	 * @access public
	 */
	public static function conf( $id, $default_v = false )
	{
		return get_option( 'dologin.' . $id, $default_v );
	}

	/**
	 * Update option of dologin
	 *
	 * @since  1.0
	 * @access public
	 */
	public static function conf_update( $id, $data )
	{
		update_option( 'dologin.' . $id, $data );
	}

	/**
	 * Get the current instance object.
	 *
	 * @since 1.0
	 * @access public
	 */
	public static function get_instance()
	{
		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

}
