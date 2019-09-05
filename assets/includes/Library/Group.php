<?php
require_once("Loader.php");

class Group Extends OneClass {
	
	protected static $table_name = "groups";
	protected static $db_fields = array("id" , "name" , "privileges" , "deleted");
	public $id;
	public $name;	
	public $privileges;	
	public $deleted;	
	
	
	public static function get_users(){
	global $db;
	return self::preform_sql("SELECT * FROM " . DBTP . self::$table_name ." WHERE id != 1 AND deleted = 0 ORDER BY id DESC  ");
	}

	
}

?>
