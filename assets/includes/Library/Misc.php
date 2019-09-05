<?php
require_once("Loader.php");

Class MiscFunction {
	protected static $table_name = "functions";
	protected static $db_fields = array("id" , "function" , "value" , "msg" );
	public $id;
	public $function;
	public $value;
	public $msg;

	
	public static function get_function($func="") {
	global $db;
	$result_array =  self::preform_sql("SELECT * FROM " . DBTP . self::$table_name . " WHERE function = '" . $db->escape_value($func) . "' LIMIT 1");
	return !empty($result_array) ? array_shift($result_array) : false;
	}

	public function check_status() {
		if ($this->value == 0) {
			redirect_to(ADMINPANEL.DS."closed.php");
		 }
	}

	//common functions
	public static function check_id_existance($id="") {
		global $db;
		$query = "SELECT * from " . DBTP . self::$table_name . " WHERE id = '{$id}' LIMIT 1 ";
		$result_set = $db->query($query);
		if($db->num_rows($result_set) != 1) {
		return false;
		} else {
		return true;
		}
	}
	public static function get_everything() {
	return self::preform_sql("SELECT * FROM " . DBTP . self::$table_name);
	}

	public static function get_specific_id($id=0) {
	global $db;
	$result_array =  self::preform_sql("SELECT * FROM " . DBTP . self::$table_name . " WHERE id = " . $db->escape_value($id) . " LIMIT 1");
	return !empty($result_array) ? array_shift($result_array) : false;
	}

	public static function preform_sql($sql="") {
		global $db;
		$result_set = $db->query($sql);	
		$object_array = array();
		while ($row = $db->fetch_array($result_set)) {
			$object_array[] = self::instantiate($row);
		}
		return $object_array;
	}

	//Instantiate + Sanitizing Attributes ...
	
	private static function instantiate($record) {
		$object =new self;
	  foreach ($record as $attribute => $value) {
		   if($object->has_attribute($attribute)) {
			 $object->$attribute = $value;
		    }
	    }
	return $object;

	}
	
	private function has_attribute($attribute) {
	$object_vars = $this->attributes();
	return array_key_exists($attribute , $object_vars);
	}

	protected function attributes() {
		$attributes = array();
		$fields_array = self::$db_fields;
		foreach($fields_array as $field) {
			if(property_exists($this , $field)){
				$attributes[$field] = $this->$field;
			}
		}
		return ($attributes);
	}

	protected function clean_attributes() {
		global $db;
		$clean_attributes = array();
		foreach ($this->attributes() as $key => $value ) {
			$clean_attributes[$key] = $db->escape_value($value);
		}
		return ($clean_attributes);
	}

	
	//CRUD Shared Methods

	public function save(){
		return (isset($this->id)) ? $this->update() : $this->create() ;
	}
	
	public function create() {
		global $db;
		$attributes = $this->clean_attributes();
		$sql = "INSERT INTO ".DBTP . self::$table_name." (" ;
		$sql .= join(", " , array_keys($attributes));
		$sql .= ") VALUES ('";
		$sql .= join("', '" , array_values($attributes));
		$sql .= "' )";
		if($db->query($sql)) {
			$this->id = $db->insert_id();
			return true;
		} else {
			return false;
		}
	}

	public function update() {
		global $db;
		$attributes = $this->clean_attributes();	
		$attribute_pairs = array();
		foreach($attributes as $key => $value){
			$attribute_pairs[] = "{$key} = '{$value}' ";
		}
		
		$sql = "UPDATE ".DBTP . self::$table_name." SET ";
		$sql .= join(", " , $attribute_pairs);
		$sql .= " WHERE id=" . $db->escape_value($this->id) ;
		
		$db->query($sql);
		if($db->affected_rows() == 1 ) {
			return true;
		} else {
			return false;
		}
	}

	public function delete() {
		global $db;
		$sql = "DELETE FROM ".DBTP . self::$table_name." WHERE ";
		$sql .= "id=".$db->escape_value($this->id) . " LIMIT 1 ";
		$db->query($sql);
		if($db->affected_rows() == 1 ) {
			$this->reset_auto_increment();
			return true;
		} else {
			return false;
		}
	}

	public function reset_auto_increment() {
		global $db;
		$sql = "ALTER TABLE " .DBTP . self::$table_name. " AUTO_INCREMENT = 1";
		$db->query($sql);
	}
}

if(filesize($parent.'/config.php') != '0') {
	$general_settings = MiscFunction::get_function("general_settings");
	$settings = unserialize(str_replace('\\' , '',$general_settings->value));
	defined('APPNAME') ? null : define ('APPNAME' , $settings['site_name']);
	defined('APPSLOGAN') ? null : define ('APPSLOGAN' , $settings['site_description']);
	defined('APPKEYWORDS') ? null : define ('APPKEYWORDS' , $settings['site_keywords']);
	defined('URLTYPE') ? null : define ('URLTYPE' , $settings['url_type']);
}
?>