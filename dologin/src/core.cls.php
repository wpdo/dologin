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

	private $_visitor_geo_data = array();

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
		add_filter( 'wp_authenticate_user', array( $this, 'wp_authenticate_user' ), 999, 2 );

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
		global $error;

		$this->_visitor_geo_data = $this->geo_ip();

		// check whitelist
		if ( ! $this->try_whitelist() ) {
			$error .= __( 'Login Security', 'dologin' ) . ': ' . __( 'Your IP is not in whitelist.', 'dologin' );
			return;
		}

		// check blacklist
		if ( $this->try_blacklist() ) {
			$error .= __( 'Login Security', 'dologin' ) . ': ' . __( 'Your IP is in blacklist.', 'dologin' );
			return;
		}
	}

	/**
	 * Login handler
	 *
	 * @since  1.0
	 * @access public
	 */
	public function wp_authenticate_user( $user, $pswd )
	{
		if ( is_wp_error( $user ) || $this->try_whitelist() === 'hit' ) {
			return $user;
		}

		if ( $this->try_blacklist() ) {
			$error = new \WP_Error();
			$error->add( 'in_blacklist', 'IP in blacklist.' );

			return $error;
		}

		return $user;
	}

	/**
	 * Validate if hit whitelist
	 *
	 * @since  1.0
	 * @access public
	 */
	private function try_whitelist()
	{
		$list = self::conf( 'whitelist', array() );
		if ( ! $list ) {
			return true;
		}

		if ( $this->maybe_hit_rule( $list ) ) {
			return 'hit';
		}

		return false;
	}

	/**
	 * Validate if hit blacklist
	 *
	 * @since  1.0
	 * @access public
	 */
	private function try_blacklist()
	{
		$list = self::conf( 'blacklist', array() );
		if ( ! $list ) {
			return false;
		}

		if ( $this->maybe_hit_rule( $list ) ) {
			return 'hit';
		}

		return false;
	}

	/**
	 * Validate if hit whitelist
	 *
	 * @since  1.0
	 * @access public
	 */
	private function maybe_hit_rule( $list )
	{
		foreach ( $list as $v ) {
			$v = explode( ',', $v );

			// Go through each rule
			foreach ( $v as $v2 ) {

				if ( ! strpos( $v2, ':' ) ) { // Optional `ip:` case
					$curr_k = 'ip';
				}
				else {
					list( $curr_k, $v2 ) = explode( ':', $v2, 2 );
				}

				$curr_k = trim( $curr_k );
				$v2 = trim( $v2 );

				// Invalid rule
				if ( ! $v2 ) {
					continue 2;
				}

				// Rule set not match
				if ( empty( $this->_visitor_geo_data[ $curr_k ] ) ) {
					continue 2;
				}

				$v2 = strtolower( $v2 );
				$visitor_v = strtolower( $this->_visitor_geo_data[ $curr_k ] );
				$visitor_v = trim( $visitor_v );

				// If has IP wildcard range, convert $v2
				if ( $curr_k == 'ip' && strpos( $v2, '*' ) !== false ) {
					// If is same ip type (both are ipv4 or v6)
					$visitor_ip_type = \WP_Http::is_ip_address( $visitor_v );
					if ( $visitor_ip_type == \WP_Http::is_ip_address( $v2 ) ) {
						$ip_separator = $visitor_ip_type == 4 ? '.' : ':';
						$uip = explode( $ip_separator, $visitor_v );
						$v2 = explode( $ip_separator, $v2 );
						foreach ( $uip as $k3 => $v3 ) {
							if ( $v2[ $k3 ] == '*' ) {
								$v2[ $k3 ] = $v3;
							}
						}
						$v2 = implode( $ip_separator, $v2 );
					}

				}

				if ( $visitor_v != $v2 ) {
					continue 2;
				}
			}

			return true;
		}

		return false;
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

		// Build geo data
		$geo_list = array( 'ip' => $ip );
		foreach ( $data as $prefix => $v ) {
			if ( in_array( $prefix, self::PREFIX_SET ) ) {
				$geo_list[ $prefix ] = trim( $v );
			}
		}

		return $geo_list;
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
