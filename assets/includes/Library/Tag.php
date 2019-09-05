<?php
require_once("Loader.php");

class Tag Extends OneClass {
	
	public static $table_name = "tags";
	public static $db_fields = array( "id" , "name" , "follows" , "description" , "avatar" , "used", "deleted");
	
	public $id;
	public $name;
	public $follows;
	public $description;
	public $avatar;
	public $used;
	public $deleted;
	
	public static function get_trending($limit = "LIMIT 10") {
		global $db;
		return self::preform_sql("SELECT name FROM " . DBTP . self::$table_name . " ORDER BY used DESC " . $limit );
	}
	
	public static function get_tag($name) {
		global $db;
		$result_array = self::preform_sql("SELECT * FROM " . DBTP . self::$table_name . " WHERE name= '{$name}' ORDER BY id DESC LIMIT 1" );
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
}
	
?>