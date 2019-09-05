<?php
require_once("Loader.php");

Class File {

	protected static $table_name = "file_upload";
	protected static $db_fields = array("id" , "filename" , "type" , "size" , "title" );
	public $id;
	public $filename;
	public $type;
	public $size;
	public $title;
	
	private $temp_path;
	protected $upload_dir="upl_files";
	public $errors = array();
	protected $upload_errors = array(
	
	UPLOAD_ERR_OK 					 => "No errors.",
	UPLOAD_ERR_INI_SIZE  		 => "Larger than upload_max_filesize.",
	UPLOAD_ERR_FORM_SIZE 	 => "Larger than form MAX_FILE_SIZE.",
	UPLOAD_ERR_PARTIAL 		 => "Partial upload.",
	UPLOAD_ERR_NO_FILE 			 => "No file.",
	UPLOAD_ERR_NO_TMP_DIR	 => "No temporary directory.",
	UPLOAD_ERR_CANT_WRITE	 => "Can't write to disk.",
	UPLOAD_ERR_EXTENSION		 => "File upload stopped by extension."
	);

	public function attach_file($file , $f) {
		global $db;
		$n = $f ;
		
		if(!$file || empty($file) || !is_array($file)  ) {
			$this->errors[]= "No files were chosen!";
			return false;
		}
		elseif( $file['error'][$n] != 0 ) {
			$this->errors[] = $this->upload_errors[$file['error'][$n]];
			return false;
		} else {
		
		$size = getimagesize($file['tmp_name'][$n]);
		if(!$size) {
			$this->errors[] = "Insecure content detected, go play somewhere else! -_-" . $size['2'];
			return false;
		}
		
		$valid_types = array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP);
		if(!in_array($size[2],  $valid_types)) {
			$this->errors[] = "Invalid file extension, System only accepts Images, Text files and Archives, Given file is " . $size[2];
			return false;
		}
		
		//elseif ($file['type'][$n] !== 'image/gif' && $file['type'][$n] !== 'image/jpeg' && $file['type'][$n] !== 'image/pjpeg' &&   $file['type'][$n] !== 'image/png' && $file['type'][$n] !== 'image/bmp' && $file['type'][$n] !== 'text/plain' && $file['type'][$n] !== 'application/msword' && $file['type'][$n] !== 'application/vnd.ms-excel' && $file['type'][$n] !== 'application/zip' && $file['type'][$n] !== 'application/pdf') {
			//$this->errors[] = "Invalid file extension, System only accepts Images, Text files and Archives, Given file is " . $file['type'][$n];
			//return false;
		//} else {
		$ext = $db->escape_value(substr(basename($file['name'][$n]),-4));
		$var= uniqid();
		$temp = substr($var,-10).$ext;
		$invalid_chars = array('-','/','+','=','*',';',',','@','~','!','#','$','%','^','&','(',')','|');
		$temp_name = str_replace($invalid_chars,'',$temp);
		$this->temp_path = $file['tmp_name'][$n];
		$this->filename =  $temp_name;
		$this->type = $file['type'][$n];
		$this->size = $file['size'][$n];
		return true;
		}
	
	}
	
	public function ajax_attach_file($file) {
		global $db;
		
		if(!$file || empty($file) || !is_array($file)  ) {
			$this->errors[]= "No files were chosen!";
			return false;
		}
		elseif( $file['error'] != 0 ) {
			$this->errors[] = $this->upload_errors[$file['error']];
			return false;
		}
		 else {
		
		$size = getimagesize($file['tmp_name']);
		if(!$size) {
			$this->errors[] = "Insecure content detected, go play somewhere else! -_-" . $size['2'];
			return false;
		}
		
		$valid_types = array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP);
		if(!in_array($size[2],  $valid_types)) {
			$this->errors[] = "Invalid file extension, System only accepts Images, Text files and Archives, Given file is " . $size[2];
			return false;
		}
		
		//elseif ($file['type'] !== 'image/gif' && $file['type'] !== 'image/jpeg' && $file['type'] !== 'image/pjpeg' &&   $file['type'] !== 'image/png' && $file['type'] !== 'image/bmp' && $file['type'] !== 'text/plain' && $file['type'] !== 'application/msword' && $file['type'] !== 'application/vnd.ms-excel' && $file['type'] !== 'application/zip' && $file['type'] !== 'application/pdf') {
			//$this->errors[] = "Invalid file extension, System only accepts Images, Text files and Archives, Given file is " . $file['type'];
			//return false;
		//} else {
		$ext = $db->escape_value(substr(basename($file['name']),-4));
		$var= uniqid();
		$temp = substr($var,-10).$ext;
		$invalid_chars = array('-','/','+','=','*',';',',','@','~','!','#','$','%','^','&','(',')','|');
		$temp_name = str_replace($invalid_chars,'',$temp);
		$this->temp_path = $file['tmp_name'];
		$this->filename =  $temp_name;
		$this->type = $file['type'];
		$this->size = $file['size'];
		return true;
		}
	
	}
	public function image_path() {
		return $this->upload_dir."/".$this->filename;
	}
	public function save() {
	
			//check for errors first ..
			if(!empty($this->errors)) {
				return false;
			}
			
			$target_path = UPLOADPATH .DS. $this->upload_dir .DS. $this->filename;
			if(file_exists($target_path)){
				$this->errors[] = "The file {$this->filename} already exists.";
				return false;				
			}
			
			if($this->type == 'image/jpeg') {
				$img = imagecreatefromjpeg ($this->temp_path);
				$test = imagejpeg ($img, $target_path, 100);
			} elseif($this->type == 'image/png') {
				$img = imagecreatefrompng ($this->temp_path);
				$test = imagepng ($img, $target_path);
			} elseif($this->type == 'image/gif') {
				$img = imagecreatefromgif ($this->temp_path);
				$test = imagegif ($img, $target_path);
			} elseif($this->type == 'image/bmp') {
				$img = imagecreatefromwbmp ($this->temp_path);
				$test = imagewbmp ($img, $target_path);
			} else {
				$this->errors[] = "The file upload failed, Cannot read image details";
				return false;
			}
			//if (move_uploaded_file($this->temp_path , $target_path)) {
			if($test) {
				imagedestroy ($img);
				if ($this->create()) {
					unset($temp_path);
					return true;
				}
			} else {
				$this->errors[] = "The file upload failed, possibly due to incorrect permissions on the upload folder.";
				return false;						
			}	
		}

	public function size_as_text() {
		if ($this->size < 1024 ) {
			$size_bytes = $this->size . " Bytes";
			return $size_bytes;
		} elseif ($this->size < 1048576 ) {
			$size_kb = round($this->size / 1024) . " KBs";
			return $size_kb;
		} else {
			$size_mb = round($this->size / 1048576 , 1 ) . " MBs";
			return $size_mb;			
			
		}
	}

	public function destroy() {
	
		// remove db entry ..
			if($this->delete()) {
				// remove the file ..
				$target_path = UPLOADPATH.DS.$this->image_path();
				return unlink($target_path) ? true : false;
			} else {
				return false;
			}		
	}
	//common functions
	
	public static function get_everything() {
	return self::preform_sql("SELECT * FROM " . DBTP . self::$table_name);
	}

	public static function get_gallery($string="") {
	return self::preform_sql("SELECT * FROM " . DBTP . self::$table_name . " WHERE gallery = 1 " . $string);
	}

	public static function get_highlight($string="") {
	return self::preform_sql("SELECT * FROM " . DBTP . self::$table_name . " WHERE highlight = 1 " . $string);
	}

	public static function find($query="",$string="") {
	global $db;
	return self::preform_sql("SELECT * FROM " . DBTP . self::$table_name ." WHERE filename LIKE '%". $db->escape_value($query) .  "%'  AND gallery = 1 OR title LIKE '%" . $db->escape_value($query) . "%'   AND gallery = 1 " . $db->escape_value($string) . " " );
	}
	
	public static function find_highlight($query="",$string="") {
	global $db;
	return self::preform_sql("SELECT * FROM " . DBTP . self::$table_name ." WHERE title LIKE '%". $db->escape_value($query) .  "%'  AND highlight = 1 OR description LIKE '%" . $db->escape_value($query) . "%'   AND highlight = 1 " . $db->escape_value($string) . " " );
	}
	
	public static function get_types() {
	return self::preform_sql("SELECT type FROM " . DBTP . self::$table_name . " WHERE gallery = 1  " );
	}

	public static function get_for_type($type) {
	return self::preform_sql("SELECT * FROM " . DBTP . self::$table_name . " WHERE gallery = 1 AND type = '{$type}' " );
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
	/*
	public function save(){
		return (isset($this->id)) ? $this->update() : $this->create() ;
	}
	*/
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
			//$this->reset_auto_increment();
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
?>