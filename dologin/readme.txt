=== DoLogin Security ===
Contributors: WPDO
Tags: Login security, GeoLocation login limit, limit login attempts, passwordless login
Requires at least: 4.0
Tested up to: 5.3
Stable tag: 1.5
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

Passwordless login. Free SMS passcode as 2nd step verification. GeoLocation (Continent/Country/City)/IP range to limit login attempts. Support Whitelist and Blacklist.

== Description ==

Limit the number of login attempts through both the login and the auth cookies.

* Free text SMS message passcode for 2nd step verification support.

* GeoLocation (Continent/Country/City) or IP range to limit login attempts.

* Passwordless login link.

* Support Whitelist and Blacklist.

* GDPR compliant. With this feature turned on, all logged IPs get obfuscated (md5-hashed).

* XMLRPC gateway protection.

= API =

* Call the function `$link = function_exists( 'dologin_gen_link' ) ? dologin_gen_link( 'your plugin name or tag' ) : '';` to generate one passwordless login link for current user.

The generated one-time used link will be expired after 7 days.

* Define const `SILENCE_INSTALL` to avoid redirecting to setting page after installtion.

= How GeoLocation works =

When visitors hit the login page, this plugin will lookup the Geolocation info from API, compare the Geolocation setting (if has) with the whitelist/blacklist to decide if allow login attempts.

== Privacy ==

The online IP lookup service is provided by https://www.doapi.us. The provider's privacy policy is https://www.doapi.us/privacy.

Based on the original code from Limit Login Attemps plugin and Limit Login Attemps Reloaded plugin.

== Screenshots ==

1. Plugin Settings
2. Login Page (After sent dynamic code to mobile text message)
3. Login Page (2 times left)
4. Login Page (Too many failure)
5. Login Page (Blacklist blocked)

== Changelog ==

= 1.5 =
* 🍀 Test SMS Message feature under Settings page.

= 1.4.7 =
* Language supported.

= 1.4.5 =
* PHP5.3 supported.

= 1.4.4 =
* Doc updates.

= 1.4.3 =
* *API* Silent install mode to avoid redirecting to settings by defining const `SILENCE_INSTALL`

= 1.4.2 =
* *API* Generated link defaults to expire in 7 days.

= 1.4.1 =
* *API* New function `dologin_gen_link( 'my_plugin' )` API to generate a link for current user.

= 1.4 =
* 🍀 Passwordless login link.

= 1.3.5 =
* SMS PHP Warning fix.

= 1.3.4 =
* REST warning fix.

= 1.3.3 =
* GUI cosmetic.

= 1.3.2 =
* 🐞 Fixed a bub that caused not enabled SMS WP failed to login.

= 1.3.1 =
* PHP Notice fix.

= 1.3 =
* 🍀 SMS login support.

= 1.2.2 =
* Auto redirect to setting page after activation.

= 1.2.1 =
* Doc improvement.

= 1.2 =
* 🍀 XMLRPC protection.

= 1.1.1 =
* 🐞 Auto upgrade can now check latest version correctly.

= 1.1 =
* 🍀 *New* Display login failure log.
* 🍀 *New* GDPR compliance.
* 🍀 *New* Auto upgrade.
* *GUI* Setting link shortcut from plugin page.
* *GUI* Display security status on login page.
* 🐞 Stale settings shown after successfully saved.
* 🐞 Duration setting can now be saved correctly.
* 🐞 Fully saved geo location failure log.

= 1.0 - Sep 27 2019 =
* Initial Release.