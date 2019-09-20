<?php
/**
 * Login Auth class
 *
 * @since 1.0
 */
namespace dologin;

defined( 'WPINC' ) || exit;

class Auth extends Instance
{
	protected static $_instance;

	private $_visitor_geo_data = array();
	private $_err_added = false;

	/**
	 * Init
	 *
	 * @since  1.0
	 * @access public
	 */
	public function init()
	{
		add_action( 'login_head', array( $this, 'login_head' ) );
		add_filter( 'authenticate', array( $this, 'authenticate' ), 2, 3 );
		add_action( 'wp_login_failed', array( $this, 'wp_login_failed' ) );

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

		if ( $this->_err_added ) {
			return;
		}

		// check whitelist
		if ( ! $this->try_whitelist() ) {
			$error .= Lang::msg( 'not_in_whitelist' );
			return;
		}

		// check blacklist
		if ( $this->try_blacklist() ) {
			$error .= Lang::msg( 'in_blacklist' );
			return;
		}

		// Check if has login error
		if ( $this->_has_login_err() ) {
			# code...
		}
	}

	private function _has_login_err()
	{
		Data::create_tb_failure();
	}

	/**
	 * Authenticate
	 *
	 * @since  1.0
	 * @access public
	 */
	public function authenticate( $user, $username, $password )
	{
		$in_whitelist = $this->try_whitelist();
		if ( is_wp_error( $user ) || $in_whitelist === 'hit' ) {
			return $user;
		}

		$error = new \WP_Error();

		if ( ! $in_whitelist ) {
			$error->add( 'not_in_whitelist', Lang::msg( 'not_in_whitelist' ) );
			$this->_err_added = true;
		}

		if ( $this->try_blacklist() ) {
			$error->add( 'in_blacklist', Lang::msg( 'in_blacklist' ) );
			$this->_err_added = true;
		}

		if ( $this->_err_added ) {
			// bypass verifying user info
			remove_filter( 'authenticate', 'wp_authenticate_username_password', 20 );
			remove_filter( 'authenticate', 'wp_authenticate_email_password', 20 );
			return $error;
		}

		return $user;
	}

	/**
	 * Log login failure
	 *
	 * @since  1.0
	 * @access public
	 */
	public function wp_login_failed( $user )
	{
		// create table and log it
		$ip = IP::me();
	}

	/**
	 * Validate if hit whitelist
	 *
	 * @since  1.0
	 * @access public
	 */
	private function try_whitelist()
	{
		$list = Conf::v( 'whitelist' );
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
		$list = Conf::v( 'blacklist' );
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
		if ( ! $this->_visitor_geo_data ) {
			$this->_visitor_geo_data = IP::geo();
		}

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

}