<?php
require_once("Loader.php");

class OneClass {
	
	
	public static function get_everything($query="" , $string="") {
	global $db;
	//return static::preform_sql("SELECT * FROM " .  DBTP. static::$table_name  . " " . $query .  " ORDER BY name ASC " .  $string  );
	return static::preform_sql("SELECT * FROM " .  DBTP. static::$table_name  . " WHERE id !=0 " . $query .  " " .  $string  );
	}

	public static function count_everything($query="" , $string="") {
	global $db;
	$result = $db->query("SELECT COUNT(id) FROM " .  DBTP. static::$table_name  . " WHERE id !=0 " . $query . " " .  $string );
	return mysqli_result($result, 0);
	}
	
	public static function get_arranged() {
	return static::preform_sql("SELECT * FROM " .  DBTP. static::$table_name  . " ORDER BY name ASC " );
	}
	
	public static function find($needle="" ,$haystack="",$string="") {
	global $db;
	return static::preform_sql("SELECT * FROM " .  DBTP. static::$table_name  ." WHERE {$haystack} LIKE '%{$needle}%' " . $string . " " );
	}

	public static function find_exact($needle="" ,$haystack="",$string="") {
	global $db;
	return static::preform_sql("SELECT * FROM " .  DBTP. static::$table_name  ." WHERE {$haystack} = '{$needle}' " . $string . " " );
	}

	public static function get_specific_id($id=0) {
	global $db;
	$result_array =  static::preform_sql("SELECT * FROM " .  DBTP. static::$table_name  . " WHERE id = " . $id . " LIMIT 1");
	return !empty($result_array) ? array_shift($result_array) : false;
	}

	public static function preform_sql($sql="") {
		global $db;
		$result_set = $db->query($sql);	
		$object_array = array();
		while ($row = $db->fetch_array($result_set)) {
			$object_array[] = static::instantiate($row);
		}
		return $object_array;
	}

	public static function check_existance($column="" , $value="") {
	global $db;
	$column = $db->escape_value($column);
	$value = $db->escape_value($value);
	
	$sql = "SELECT id FROM  " .  DBTP. static::$table_name ;
	$sql .= " WHERE {$column} = '{$value}' ";
	$sql .= "LIMIT 1" ;
	$result_array =  $db->query($sql);
	return $db->num_rows($result_array) ? true : false;
	}

	public static function check_id_existance($id="") {
	global $db;
	$id = $db->escape_value($id);

	$sql = "SELECT id FROM  " .  DBTP. static::$table_name ;
	$sql .= " WHERE id = '{$id}' ";
	$sql .= "LIMIT 1" ;
	$result_array =  $db->query($sql);
	return $db->num_rows($result_array) ? true : false;
	}

	public static function get_specific_record($column="" , $value="") {
	global $db;
	$column = $db->escape_value($column);
	$value = $db->escape_value($value);
	
	$result_array =  static::preform_sql("SELECT * FROM  " .  DBTP. static::$table_name  . " WHERE {$column} = '{$value}' LIMIT 1") ;
	//return $db->num_rows($result_array) ? true : false;
	return !empty($result_array) ? array_shift($result_array) : false;
	}



	//Instantiate + Sanitizing Attributes ...

	private static function instantiate($record) {
		$class_name = get_called_class();
		$object =new $class_name;
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
		$fields_array = static::$db_fields;
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

	public function create() {
		global $db;
		$attributes = $this->clean_attributes();
		$sql = "INSERT INTO ". DBTP. static::$table_name ." (" ;
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
		
		$sql = "UPDATE ". DBTP. static::$table_name ." SET ";
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
		$sql = "DELETE FROM ". DBTP. static::$table_name ." WHERE ";
		$sql .= " id= ".$db->escape_value($this->id) . " LIMIT 1 ";
		$db->query($sql);
		if($db->affected_rows() == 1 ) {
			return true;
		} else {
			return false;
		}
	}

	public function reset_auto_increment() {
		global $db;
		$sql = "ALTER TABLE " . DBTP. static::$table_name . " AUTO_INCREMENT = 1";
		$db->query($sql);
	}

	
	}

?>
