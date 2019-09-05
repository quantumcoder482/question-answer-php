<?php
require_once("Loader.php");

class Answer Extends OneClass {
	
	public static $table_name = "answers";
	public static $db_fields = array( "id" , "user_id" , "q_id" , "content", "likes", "dislikes", "created_at" , "updated_at" , "published");
	
	public $id;
	public $user_id;
	public $q_id;
	public $content;
	public $likes;
	public $dislikes;
	public $answers;
	public $created_at;
	public $updated_at;
	public $published;
	
	public static function get_best_answer_for($q_id=0) {
	global $db;
	$result_array =  static::preform_sql("SELECT * FROM " . DBTP . self::$table_name . " WHERE q_id = " . $q_id . " AND published = 1 ORDER BY likes DESC LIMIT 1");
	return !empty($result_array) ? array_shift($result_array) : false;
	}

	public static function get_pending($string = "") {
		global $db;
		return self::preform_sql("SELECT * FROM " . DBTP . self::$table_name . " WHERE published = 0 {$string} ORDER BY created_at ASC " . $string );
	}
	public static function count_pending($string = "") {
		//get feed ...
		global $db;
		$result = $db->query("SELECT COUNT(id) FROM " . DBTP . self::$table_name . " WHERE published = 0 {$string} ORDER BY created_at ASC " . $string );
		return mysqli_result($result, 0);
	}
	
	public static function get_answers_for($q_id , $string = "") {
		global $db;
		return self::preform_sql("SELECT * FROM " . DBTP . self::$table_name . " WHERE q_id = " . $q_id . " ORDER BY likes DESC " . $string);
	}
	
	public static function get_answers_for_user($user_id , $string = "") {
		global $db;
		return self::preform_sql("SELECT * FROM " . DBTP . self::$table_name . " WHERE user_id = " . $user_id . " ORDER BY created_at DESC" . $string);
	}
	
	public static function count_answers_for($user_id , $string="") {
		global $db;
		$result = $db->query("SELECT * FROM " . DBTP . self::$table_name . " WHERE q_id = " . $user_id . " ORDER BY created_at DESC" . $string );
		if(mysqli_num_rows($result)) { return mysqli_num_rows($result); } else { return '0'; }
	}
	
	public static function count_answers_for_user($user_id , $string="") {
		global $db;
		$result = $db->query("SELECT * FROM " . DBTP . self::$table_name . " WHERE user_id = " . $user_id . " ORDER BY created_at DESC" . $string );
		if(mysqli_num_rows($result)) { return mysqli_num_rows($result); } else { return '0'; }
	}
	
	public function publish() {
		$this->published = 1;
		$this->update();
	}
	
}
	
?>