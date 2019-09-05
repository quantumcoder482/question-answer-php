<?php
require_once("Loader.php");

class Question Extends OneClass {
	
	public static $table_name = "questions";
	public static $db_fields = array( "id" , "user_id" , "title" , "feed", "content", "likes", "dislikes", "answers" , "views" , "follows" , "slug", "created_at" , "updated_at" , "published" , "anonymous");
	
	public $id;
	public $user_id;
	public $title;
	public $feed;
	public $content;
	public $likes;
	public $dislikes;
	public $answers;
	public $views;
	public $follows;
	public $slug;
	public $created_at;
	public $updated_at;
	public $published;
	public $anonymous;
	
	public function view_q() {
		$this->views += 1;
		$this->update();
	}
	
	public function publish() {
		$this->published = 1;
		$this->update();
	}
	
	public static function get_pending($string = "") {
		//get feed ...
		global $db;
		return self::preform_sql("SELECT * FROM " . DBTP . self::$table_name . " WHERE published = 0 {$string} ORDER BY created_at ASC " . $string );
	}
	public static function count_pending($string = "") {
		//get feed ...
		global $db;
		$result = $db->query("SELECT COUNT(id) FROM " . DBTP . self::$table_name . " WHERE published = 0 {$string} ORDER BY created_at ASC " . $string );
		return mysqli_result($result, 0);
	}
	
	public static function get_related_questions_for($feed ="",$string = "") {
		//get feed ...
		global $db;
		
		$query = " feed LIKE '%{$feed}%' ";
		$feed_arr = explode(',' , $feed);
		if(is_array($feed_arr)) {
			$arrayKeys = array_keys($feed_arr);
			$lastArrayKey = array_pop($arrayKeys);
			
			$query = " (  ";
			foreach($feed_arr as $k => $f) {
				$query .= " feed LIKE '%{$f}%'  ";
				if($k != $lastArrayKey) {
					$query .= " OR ";
				}
			}
			$query .= " ) ";
		}
		return self::preform_sql("SELECT DISTINCT * FROM " . DBTP . self::$table_name . " WHERE {$query} ORDER BY answers DESC " . $string );
	}
	
	public static function get_feed_for($user_id , $query , $string) {
		global $db;
		
		/*following :
		$following = FollowRule::get_subscriptions_ids('user',$user_id , 'user_id', "" );
		$users = array();
		if($following) {
			foreach($following as $f) {
				$users[] = $f->obj_id;
			}
		}
		//tags :
		$following = FollowRule::get_subscriptions_ids('tag',$user_id , 'user_id', "" );
		$tags = array();
		if($following) {
			foreach($following as $f) {
				$tag = Tag::get_specific_id($f->obj_id);
				$tags[] = $tag->name;
			}
		}
		//trending :
		$trend = Tag::get_trending(' LIMIT 4 ');
		$trending = array();
		if($trend) {
			foreach($trend as $t) {
				$trending[] = $t->name;
			}
		}
		
		$str = ' AND ( ';
		$or = false;
		
		if(!empty($users)) {
			$user_str = implode(',' , $users);
			$str .= " user_id IN ({$user_str}) ";
			$or = true;
		}
		
		if(!empty($tags)) {
			foreach($tags as $i => $tag) {
				if($or) {
					$str .= " OR ";
				} else {
					$or = true;
				}
				$str .= " feed LIKE '%{$tag}%' ";
			}
		}
		
		if(!empty($trending)) {
			foreach($trending as $i => $tag) {
				if($or) {
					$str .= " OR ";
				} else {
					$or = true;
				}
				$str .= " feed LIKE '%{$tag}%' ";
			}
		}
		
		if($or) {
			$str .= " ) ";
		} else {
			$str = "";
		}
		*/
		$str = "";
		if($query) {
			$str = $query;
		}
		
		return self::preform_sql("SELECT * FROM " . DBTP . self::$table_name . " WHERE published = 1 {$str} ORDER BY created_at DESC, likes ASC " . $string );
	}
	
	public static function count_feed_for($user_id , $query , $string) {
		global $db;
		/*following :
		$following = FollowRule::get_subscriptions_ids('user',$user_id , 'user_id', "" );
		$users = array();
		if($following) {
			foreach($following as $f) {
				$users[] = $f->obj_id;
			}
		}
		//tags :
		$following = FollowRule::get_subscriptions_ids('tag',$user_id , 'user_id', "" );
		$tags = array();
		if($following) {
			foreach($following as $f) {
				$tag = Tag::get_specific_id($f->obj_id);
				$tags[] = $tag->name;
			}
		}
		//trending :
		$trend = Tag::get_trending(' LIMIT 4 ');
		$trending = array();
		if($trend) {
			foreach($trend as $t) {
				$trending[] = $t->name;
			}
		}
		
		$str = ' AND ( ';
		$or = false;
		
		if(!empty($users)) {
			$user_str = implode(',' , $users);
			$str .= " user_id IN ({$user_str}) ";
			$or = true;
		}
		
		if(!empty($tags)) {
			foreach($tags as $i => $tag) {
				if($or) {
					$str .= " OR ";
				} else {
					$or = true;
				}
				$str .= " feed LIKE '%{$tag}%' ";
			}
		}
		
		if(!empty($trending)) {
			foreach($trending as $i => $tag) {
				if($or) {
					$str .= " OR ";
				} else {
					$or = true;
				}
				$str .= " feed LIKE '%{$tag}%' ";
			}
		}
		
		if($or) {
			$str .= " ) ";
		} else {
			$str = "";
		}*/
		
		$str= "";
		if($query) {
			$str = $query;
		}
		
		$result = $db->query("SELECT COUNT(id) FROM " . DBTP . self::$table_name . " WHERE published = 1 {$str} ORDER BY created_at DESC" . $string);
		return mysqli_result($result, 0);
	}
	
	public static function get_questions_for($user_id , $string) {
		//get feed ...
		global $db;
		return self::preform_sql("SELECT * FROM " . DBTP . self::$table_name . " WHERE user_id = " . $user_id . " ORDER BY created_at DESC" . $string );
	}
	
	public static function count_questions_for($user_id , $string) {
		//get feed ...
		global $db;
		$result = $db->query("SELECT * FROM " . DBTP . self::$table_name . " WHERE user_id = '" . $user_id . "' ORDER BY created_at DESC" . $string );
		if(mysqli_num_rows($result)) { return mysqli_num_rows($result); } else { return '0'; }
	}
	
	
	public static function check_slug($slug) {
		global $db;
		return self::preform_sql("SELECT id FROM " . DBTP . self::$table_name . " WHERE slug = '{$slug}' ORDER BY created_at ASC" );
	}
	
	public static function check_slug_except($slug , $id) {
		global $db;
		return self::preform_sql("SELECT id FROM " . DBTP . self::$table_name . " WHERE slug = '{$slug}' AND id != {$id} ORDER BY created_at ASC" );
	}
	
	public static function get_slug($slug) {
		$result_array =  static::preform_sql("SELECT * FROM " . DBTP . static::$table_name . " WHERE slug = '" . $slug . "' ORDER BY id DESC LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
	public static function get_tagcloud($data) {
		global $db;
		$result = $db->query("SELECT GROUP_CONCAT(feed SEPARATOR ',') AS tagcloud FROM questions group by 'all' ");
		$arr = mysqli_result($result, 0);
		@$tags = explode(',' , $arr);
		if(is_array($tags)) {
			$tags = array_unique($tags); 
			$input = preg_quote($data, '~'); // don't forget to quote input string!
			$result = preg_grep('~' . $input . '~', $tags);
			return $result; 
		} else { return false; }
	}
}
	
?>