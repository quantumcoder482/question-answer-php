<?php
require_once("Loader.php");

class Award Extends OneClass  {
	
	protected static $table_name = "awards";
	protected static $db_fields = array("id" , "user_id" , "created" , "reason");
	public $id;
	public $user_id;	
	public $created;	
	public $reason;
	
	public static function send_award($user_id="" , $msg="" , $link="") {
	global $db;
		
		$award = New self();
		
		$award->user_id = $user_id;
		$award->reason = $msg;
		$award->created = strftime("%Y-%m-%d %H:%M:%S" , time());
		
		if($award->create()) {
			return true;
		} else {
			return false;
		}

	}
	
}

?>
