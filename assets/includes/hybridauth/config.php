<?php
include_once(dirname(__FILE__) .'/../config.php');
global $facebook_api;
global $google_api;
/**
 * HybridAuth
 * http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
 * (c) 2009-2015, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
 */
// ----------------------------------------------------------------------------------------
//	HybridAuth Config file: http://hybridauth.sourceforge.net/userguide/Configuration.html
// ----------------------------------------------------------------------------------------
return
		array(
			"base_url" => WEB_LINK.'assets/includes/hybridauth/',
			"providers" => array(
				"Google" => array(
					"enabled" => true,
					"keys" => array("id" => "{$google_api['id']}", "secret" => "{$google_api['secret']}"),
					"scope"   => 'email' // optional
				),
				"Facebook" => array(
					"enabled" => true,
					"keys" => array("id" => "{$facebook_api['id']}", "secret" => "{$facebook_api['secret']}"),
					"trustForwarded" => false,
					"scope"   => ['email'], // optional
					"display" => "popup" // optional

				),
				"Twitter" => array(
					"enabled" => true,
					"keys" => array("key" => "", "secret" => ""),
					"includeEmail" => false
				),
			),
			// If you want to enable logging, set 'debug_mode' to true.
			// You can also set it to
			// - "error" To log only error messages. Useful in production
			// - "info" To log info and error messages (ignore debug messages)
			"debug_mode" => false,
			// Path to file writable by the web server. Required if 'debug_mode' is not false
			"debug_file" => "",
);
