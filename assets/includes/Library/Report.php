<?php
require_once("Loader.php");

class Report Extends OneClass {
	
	public static $table_name = "reports";
	public static $db_fields = array( "id" , "user_id" , "obj_id" , "obj_type" , "report_date", "info", "result");
	
	public $id;
	public $user_id;
	public $obj_id;
	public $obj_type;
	public $report_date;
	public $info;
	public $result;


	public static function check_for_obj($obj_type='question' , $id , $user_id) {
		global $db;
		$result_array =  $db->query("SELECT id FROM " . DBTP . self::$table_name . " WHERE obj_id = " . $id . " AND obj_type = '{$obj_type}' AND user_id = {$user_id} AND result = '' LIMIT 1");
		return mysqli_num_rows($result_array) ? true : false;
	}

	public static function count_pending($string = "") {
		//get feed ...
		global $db;
		$result = $db->query("SELECT COUNT(id) FROM " . DBTP . self::$table_name . " WHERE result = '' {$string} ");
		return mysqli_result($result, 0);
	}
	
	
}
	
?>