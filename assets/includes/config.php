<?php //Database connection settings
defined('DBH') ? null : define ('DBH' , 'localhost');
defined('DBU') ? null : define ('DBU' , 'root');
defined('DBPW') ? null : define ('DBPW' , '');
defined('DBN') ? null : define ('DBN' , 'quora');
defined('DBTP') ? null : define ('DBTP' , 'p_');

//Define your web accessible link to this script, including http:// or https:// with TRAILING SLASH / in the end !IMPORTANT
defined('WEB_LINK') ? null : define('WEB_LINK' , 'http://localhost:10803/');
defined('ERROR_LINK') ? null : define('ERROR_LINK' , WEB_LINK );
defined('UPL_FILES') ? null : define('UPL_FILES' , WEB_LINK.'assets');

//Facebook API Credentials, get them from https://developers.facebook.com/apps
$facebook_api = array("secret"=>"", "id" => "");

//Google API Credentials, get them from https://console.developers.google.com
$google_api = array("secret"=>"", "id" => "");

//Google Captcha Info, get them from https://www.google.com/recaptcha/admin
$captcha_info = array("secret"=>"", "sitekey" => "");

//Google Analytics Info, get them from https://analytics.google.com/analytics/web/
$analytics_info = false;

//AddThis Info, get them from https://www.addthis.com/dashboard/
$addthis_info = false;

require_once("url_mapper.php");

?>