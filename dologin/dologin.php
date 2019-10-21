<?php
/**
 * Plugin Name:       DoLogin Security
 * Description:       GeoLocation (Continent/Country/City) or IP range to limit login attempts. Support Whitelist and Blacklist. Will have free text SMS message passcode for 2nd step verification support soon.
 * Version:           1.3.1
 * Author:            WPDO
 * License:           GPLv3
 * License URI:       http://www.gnu.org/licenses/gpl.html
 *
 * Copyright (C) 2019 WPDO
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.

 */
defined( 'WPINC' ) || exit;

if ( defined( 'DOLOGIN_V' ) ) {
	return;
}

define( 'DOLOGIN_V', '1.3.1' );

! defined( 'DOLOGIN_DIR' ) && define( 'DOLOGIN_DIR', dirname( __FILE__ ) . '/' );// Full absolute path '/usr/local/***/wp-content/plugins/dologin/' or MU
! defined( 'DOLOGIN_PLUGIN_URL' ) && define( 'DOLOGIN_PLUGIN_URL', plugin_dir_url( __FILE__ ) ) ;// Full URL path '//example.com/wp-content/plugins/dologin/'

require_once DOLOGIN_DIR . 'autoload.php';

\dologin\Core::get_instance();

