<?php
require_once("Loader.php");

class Log  {
	
	protected static $table_name = "logs";
	protected static $db_fields = array("id" , "user_id" , "customer_id" , "action","msg","done_at" , "ip");
	public $id;
	public $user_id;	
	public $customer_id;	
	public $action;	
	public $msg;	
	public $done_at;
	public $ip;
	
	//common DB Functions ..
		
	public static function get_everything() {
	return self::preform_sql("SELECT * FROM " . DBTP . self::$table_name . " ORDER BY done_at DESC  ");
	}	

	public static function get_actions() {
	return self::preform_sql("SELECT DISTINCT action FROM " . DBTP . self::$table_name);
	}	
	
	public static function get_arranged($string="") {
	global $db;
	return self::preform_sql("SELECT * FROM " . DBTP . self::$table_name . " ORDER BY done_at DESC " . $db->escape_value($string));
	}	
	
	public static function get_user_logs($id="",$string="") {
	global $db;
	return self::preform_sql("SELECT * FROM " . DBTP . self::$table_name . " WHERE user_id = '" . $db->escape_value($id) . "' " . $db->escape_value($string) . " " );
	}	
	
	public static function log_action($user_id="" , $action="" , $msg="" , $customer_id=0) {
	global $db;
		
		$log = New self();
		
		$log->user_id = $db->escape_value($user_id);
		$log->action = $db->escape_value($action);
		$log->msg = $db->escape_value($msg);
		$log->done_at = strftime("%Y-%m-%d %H:%M:%S" , time());
		$log->ip = getRealIpAddr();
		$log->customer_id = $customer_id;
		
		if($log->create()) {
		return true;
		} else {
		return false;
		}

	}
	
	public static function find($query="",$string="") {
	global $db;
	return self::preform_sql("SELECT * FROM " . DBTP . self::$table_name ." WHERE id !=0 " . $query . " ORDER BY done_at DESC " . $db->escape_value($string) . " " );
	}
	
	public static function count_everything() {
	global $db;
	$count = $db->query("SELECT * FROM " . DBTP . self::$table_name);
	return $db->num_rows($count);
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

	public static function check_existance($column="" , $value="") {
	global $db;
	$column = $db->escape_value($column);
	$value = $db->escape_value($value);
	
	$sql = "SELECT * FROM  " . DBTP . self::$table_name;
	$sql .= " WHERE {$column} = '{$value}' ";
	$sql .= "LIMIT 1" ;
	$result_array =  $db->query($sql);
	return $db->num_rows($result_array) ? true : false;
	}

	public static function check_id_existance($id="") {
	global $db;
	$id = $db->escape_value($id);

	$sql = "SELECT * FROM  " . DBTP . self::$table_name;
	$sql .= " WHERE id = '{$id}' ";
	$sql .= "LIMIT 1" ;
	$result_array =  $db->query($sql);
	return $db->num_rows($result_array) ? true : false;
	}


	public static function get_specific_record($column="" , $value="") {
	global $db;
	$column = $db->escape_value($column);
	$value = $db->escape_value($value);
	
	$result_array =  self::preform_sql("SELECT * FROM  " . DBTP . self::$table_name . " WHERE {$column} = '{$value}' LIMIT 1") ;
	//return $db->num_rows($result_array) ? true : false;
	return !empty($result_array) ? array_shift($result_array) : false;
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
		$class_name = get_called_class();
		return (isset($$class_name->id)) ? $this->update() : $this->create() ;
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
		$sql .= " id= ".$db->escape_value($this->id) . " LIMIT 1 ";
		$db->query($sql);
		if($db->affected_rows() == 1 ) {
			//$this->reset_auto_increment();
			return true;
		} else {
			return false;
		}
	}

	public static function trnkt() {
		global $db;
		$sql = "TRUNCATE TABLE ".DBTP . self::$table_name." ";
		$db->query($sql);
			//$this->reset_auto_increment();
		return true;
	}
	
	public function reset_auto_increment() {
		global $db;
		$sql = "ALTER TABLE " .DBTP . self::$table_name. " AUTO_INCREMENT = 1";
		$db->query($sql);
	}

	
	}

?>
