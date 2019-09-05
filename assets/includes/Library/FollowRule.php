<?php
require_once("Loader.php");

class FollowRule Extends OneClass {
	
	public static $table_name = "follows_rules";
	public static $db_fields = array( "id" , "user_id" , "obj_id" , "follow_date", "obj_type" );
	
	public $id;
	public $user_id;
	public $obj_id;
	public $follow_date;
	public $obj_type;
	
	
	public static function check_for_obj($obj_type='question' , $id , $user_id) {
		global $db;
		$result_array =  $db->query("SELECT id FROM " . DBTP . self::$table_name . " WHERE obj_id = " . $id . " AND obj_type = '{$obj_type}' AND user_id = {$user_id} LIMIT 1");
		return mysqli_num_rows($result_array) ? true : false;
	}
	
	public static function get_for_obj($obj_type='question'  , $id , $user_id) {
		global $db;
		$result_array =  static::preform_sql("SELECT * FROM " . DBTP . self::$table_name . " WHERE obj_id = " . $id . " AND obj_type = '{$obj_type}' AND user_id = {$user_id} LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
	public static function get_subscriptions($obj_type='question' , $id="" , $col = 'user_id' , $string="") {
		global $db;
		return self::preform_sql("SELECT * FROM ".DBTP ."follows_rules WHERE obj_type = '{$obj_type}' AND {$col} = '{$id}' ORDER BY follow_date DESC " . $string );
	}
	
	public static function get_subscriptions_ids($obj_type='question' , $id="" , $col = 'user_id' , $string="") {
		global $db;
		return self::preform_sql("SELECT id,obj_id FROM ".DBTP ."follows_rules WHERE obj_type = '{$obj_type}' AND {$col} = '{$id}' ORDER BY follow_date DESC " . $string );
	}
	
	public static function count_subscriptions($obj_type='question' , $id="" , $col = 'user_id' , $string="") {
		global $db;
		$result = $db->query("SELECT COUNT(id) FROM ".DBTP ."follows_rules WHERE obj_type = '{$obj_type}' AND {$col} = '{$id}' " . $string );
		return mysqli_result($result, 0);
	}
	
	
}
	
?>