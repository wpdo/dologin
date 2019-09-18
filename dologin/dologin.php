<?php
/**
 * Plugin Name:       Login Security
 * Plugin URI:        https://github.com/wpdo/dologin
 * Description:       Use GeoLocation (Country/City setting), free text sms message or IP range to limit login attempts.
 * Version:           1.0
 * Author:            WPDO
 * License:           GPLv3
 * License URI:       http://www.gnu.org/licenses/gpl.html
 * Text Domain:       dologin
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

define( 'DOLOGIN_V', '1.0' );

! defined( 'DOLOGIN_DIR' ) && define( 'DOLOGIN_DIR', dirname( __FILE__ ) . '/' );// Full absolute path '/usr/local/***/wp-content/plugins/dologin/' or MU

require_once DOLOGIN_DIR . 'autoload.php';

\dologin\Core::get_instance();

