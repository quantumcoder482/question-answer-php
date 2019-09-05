<?php
require_once("Loader.php");

class LikeRule Extends OneClass {
	
	public static $table_name = "likes_rules";
	public static $db_fields = array( "id" , "user_id" , "obj_id" , "like_date", "obj_type" , "type");
	
	public $id;
	public $user_id;
	public $obj_id;
	public $like_date;
	public $obj_type;
	public $type;
	
	public static function check_for_obj($obj_type='question' , $type = "like", $id , $user_id) {
		global $db;
		$result_array =  $db->query("SELECT id FROM " . DBTP . self::$table_name . " WHERE type = '{$type}' AND obj_id = " . $id . " AND obj_type = '{$obj_type}' AND user_id = {$user_id} LIMIT 1");
		return mysqli_num_rows($result_array) ? true : false;
	}
	
	public static function get_for_obj($obj_type='question'  , $type = "like", $id , $user_id) {
		global $db;
		$result_array =  static::preform_sql("SELECT * FROM " . DBTP . self::$table_name . " WHERE type = '{$type}' AND obj_id = " . $id . " AND obj_type = '{$obj_type}' AND user_id = {$user_id} LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
}
	
?>