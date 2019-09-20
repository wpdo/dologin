<?php
/**
 * Rest class
 *
 * @since 1.0
 */
namespace dologin;

defined( 'WPINC' ) || exit;

class REST extends Instance
{
	protected static $_instance;

	/**
	 * Init
	 *
	 * @since  1.0
	 * @access public
	 */
	public function init()
	{
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
	}

	/**
	 * Register REST hooks
	 *
	 * @since  1.0
	 * @access public
	 */
	public function rest_api_init()
	{
		register_rest_route( 'dologin/v1', '/myip', array(
			'methods' => 'GET',
			'callback' => __NAMESPACE__ . '\IP::geo',
		) );
	}

}
