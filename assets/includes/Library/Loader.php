<?php
ob_start();
session_start();

error_reporting(0);
ini_set('display_errors', 0);

defined('DS') ? null : define('DS' , DIRECTORY_SEPARATOR);

$current = dirname(__FILE__);
$parent = dirname(dirname(dirname(__FILE__)));
$parent2 = dirname(dirname(dirname(dirname(__FILE__))));

defined('LIBRARY') ? null : define('LIBRARY' ,  $current );
defined('ADMINPANEL') ? null : define('ADMINPANEL' ,$parent2);
defined('UPLOADPATH') ? null : define('UPLOADPATH' , $parent );
defined('UPLOADPATH2') ? null : define('UPLOADPATH2' , $parent . '/upl_files/' );

//assign globals!
require_once(LIBRARY.DS."DBManager.php");
require_once(LIBRARY.DS."SessManager.php");
require_once(LIBRARY.DS."Uploader.php");

require_once(LIBRARY.DS."OneClass.php");

require_once(LIBRARY.DS."Group.php");

require_once(LIBRARY.DS."PasswordHash.php");
require_once(LIBRARY.DS."Functions.php");

require_once(LIBRARY.DS."Question.php");
require_once(LIBRARY.DS."LikeRule.php");
require_once(LIBRARY.DS."FollowRule.php");
require_once(LIBRARY.DS."Answer.php");
require_once(LIBRARY.DS."Tag.php");
require_once(LIBRARY.DS."Notif.php");

require_once(LIBRARY.DS."File.php");
require_once(LIBRARY.DS."Log.php");
require_once(LIBRARY.DS."Misc.php");
require_once(LIBRARY.DS."Pagination.php");
require_once(LIBRARY.DS."Mailer.php");
require_once(LIBRARY.DS."User.php");

?>