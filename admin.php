<?php require_once('assets/includes/route.php');
$title = "Site Admin"; require_once('assets/includes/header.php');


if (isset($_POST['add_topic'])) {
		if(!$current_user->can_see_this("admintopics.read",$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to("{$url_mapper['index/']}&edit=fail&msg={$msg}");
		}
		if($_POST['hash'] == $_SESSION[$elhash]){
			//unset($_SESSION[$elhash]);
			
			$db_fields = Array('name', 'description', 'upload_files');
			
			$upload_key = array_search('upload_files' , $db_fields);
			if($upload_key) {
				unset($db_fields[$upload_key]);
				$upload_present = true;
			}
			
			foreach($db_fields as $field) {
				if(isset($_POST[$field])) {
					$$field = $db->escape_value(str_replace('?','',$_POST[$field]));
				}
			}
			
			$edited_entry = New Tag();
			
			foreach($db_fields as $field) {
				if(isset($$field)) {
					$edited_entry->$field = $$field;
				}
			}
			
			
			if(isset($upload_present) && $upload_present == true) {
				$files = '';
				$f = 0;
				$images = array();
				$num_pics = 1;
				$target = $_FILES['upload_files'];
				$upload_problems = 0;
				for ($f ; $f < $num_pics ; $f++) :
					$file = "file";
					$string = $$file . "{$f}";
					$$string = new File();	
						if(!empty($_FILES['upload_files']['name'][$f])) {
							$$string->attach_file($_FILES['upload_files'], $f);
							if ($$string->save()) {
								$images[$f] = $$string->id;
							} else {
								$upl_msg = "{$lang['alert-upload_error']}:<br>";	
								$upl_msg .= join("<br />" , $$string->errors);							
								$upload_problems = 1;
							}
						}
				endfor;
				
				if(!empty($images)) {
					$final_string = implode("," , $images);
					//if($edited_entry->files != NULL) {
						//$edited_entry->files .= ",". $final_string;
					//} else {
						//$edited_entry->files .= $final_string;
					//}
					$edited_entry->avatar = $final_string;
				}
			}
			
			if ($edited_entry->create()) {
				
				$msg = $lang['alert-create_success'];
				if(isset($upl_msg)) {
					$msg .= $upl_msg;
				}
				redirect_to("{$url_mapper['admin/']}&section=topics&edit=success&msg={$msg}");
			} else {
				$msg = $lang['alert-update_failed'];
				if(isset($upl_msg)) {
					$msg .= $upl_msg;
				}
				redirect_to("{$url_mapper['admin/']}&section=topics&edit=fail&msg={$msg}");
			}
		} else {
			$msg = $lang['alert-auth_error'];
			redirect_to("{$url_mapper['admin/']}&section=topics&edit=fail&msg={$msg}");
		}
}

if (isset($_POST['edit_topic'])) {
		if(!$current_user->can_see_this("admintopics.update",$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to("{$url_mapper['index/']}&edit=fail&msg={$msg}");
		}
		if($_POST['hash'] == $_SESSION[$elhash]){
			//unset($_SESSION[$elhash]);
			
			$edit_id = $db->escape_value($_POST["edit_id"]);
			
			$db_fields = Array('name', 'description', 'upload_files');
			
			$upload_key = array_search('upload_files' , $db_fields);
			if($upload_key) {
				unset($db_fields[$upload_key]);
				$upload_present = true;
			}
			
			foreach($db_fields as $field) {
				if(isset($_POST[$field])) {
					$$field = $db->escape_value(str_replace('?','',$_POST[$field]));
				}
			}
			
			$edited_entry = Tag::get_specific_id($edit_id);
			echo $old_name = $edited_entry->name;
			
			foreach($db_fields as $field) {
				if(isset($$field)) {
					$edited_entry->$field = $$field;
				}
			}
			
			echo $name;
			if(isset($upload_present) && $upload_present == true) {
				$files = '';
				$f = 0;
				$images = array();
				$num_pics = 1;
				$target = $_FILES['upload_files'];
				$upload_problems = 0;
				for ($f ; $f < $num_pics ; $f++) :
					$file = "file";
					$string = $$file . "{$f}";
					$$string = new File();	
						if(!empty($_FILES['upload_files']['name'][$f])) {
							$$string->attach_file($_FILES['upload_files'], $f);
							if ($$string->save()) {
								$images[$f] = $$string->id;
							} else {
								$upl_msg = "{$lang['alert-upload_error']}:<br>";	
								$upl_msg .= join("<br />" , $$string->errors);							
								$upload_problems = 1;
							}
						}
				endfor;
				
				if(!empty($images)) {
					$final_string = implode("," , $images);
					//if($edited_entry->files != NULL) {
						//$edited_entry->files .= ",". $final_string;
					//} else {
						//$edited_entry->files .= $final_string;
					//}
					$edited_entry->avatar = $final_string;
				}
			}
			
			if ($edited_entry->update()) {
				if($old_name != $name) {
					$query = " AND feed LIKE '%{$old_name}%' ";
					$questions = Question::get_feed_for($current_user->id ,$query,"" );
					if($questions) {
						foreach($questions as $q) {
							$tags = explode(',' , $q->feed);
							
							foreach($tags as $k => $v) {
								if(strtolower($v) == strtolower($old_name)) {
									unset($tags[$k]);
									$tags[] = $name;
								}
							}
							$q->feed = implode(',' , $tags);
							$q->update();
						}
					}
				}
				$msg = $lang['alert-update_success'];
				if(isset($upl_msg)) {
					$msg .= $upl_msg;
				}
				redirect_to("{$url_mapper['admin/']}&section=topics&edit=success&msg={$msg}");
			} else {
				$msg = $lang['alert-update_failed'];
				if(isset($upl_msg)) {
					$msg .= $upl_msg;
				}
				redirect_to("{$url_mapper['admin/']}&section=topics&edit=fail&msg={$msg}");
			}
		} else {
			$msg = $lang['alert-auth_error'];
			redirect_to("{$url_mapper['admin/']}&section=topics&edit=fail&msg={$msg}");
		}
}

if (isset($_POST['edit_user'])) {
		if(!$current_user->can_see_this("adminusers.update",$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to("{$url_mapper['index/']}&edit=fail&msg={$msg}");
		}
		if($_POST['hash'] == $_SESSION[$elhash]){
			//unset($_SESSION[$elhash]);
			
			$edit_id = $db->escape_value($_POST["edit_id"]);
			
			$db_fields = Array('f_name','l_name', 'mobile', 'address' ,  'comment' , 'about' , 'disabled' , 'upload_files');
			
			$upload_key = array_search('upload_files' , $db_fields);
			if($upload_key) {
				unset($db_fields[$upload_key]);
				$upload_present = true;
			}
			
			foreach($db_fields as $field) {
				if(isset($_POST[$field])) {
					$$field = $db->escape_value($_POST[$field]);
				}
			}
			
			$edited_entry = User::get_specific_id($edit_id);
			
			foreach($db_fields as $field) {
				if(isset($$field)) {
					$edited_entry->$field = $$field;
				}
			}
			
			$password = $db->escape_value($_POST['password']);
			
			if($current_user->can_see_this('adminusers.changemail' , $group) ) {
			$email = $db->escape_value($_POST['email']);
			
			$current_email = $edited_entry->email;
			$email_exists = User::check_existance_except("email", $email , $edit_id);
			
			if($email_exists) {
				$msg = $lang['alert-email_exists'];
				redirect_to("{$url_mapper['admin/']}&id={$edit_id}&hash={$_POST['hash']}&section=users&type=edit&edit=fail&msg={$msg}");
			}
			
			if($email != '' && $email != $current_email) {
			$edited_entry->email = $email;
			}
			}
			
			if($current_user->can_see_this('adminusers.changeusername' , $group) ) {
			$username = $db->escape_value(trim(str_replace(' ' , '' , $_POST['username'])));
			
			$current_username = $edited_entry->username;
			$username_exists = User::check_existance_except("username", $username , $edit_id);
			
			if($username_exists) {
				$msg = $lang['alert-username_exists'];
				redirect_to("{$url_mapper['admin/']}&id={$edit_id}&hash={$_POST['hash']}&section=users&type=edit&edit=fail&msg={$msg}");
			}
			
			if($username != '' && $username != $current_username) {
			$edited_entry->username = $username;
			}
			}
			
			if($current_user->can_see_this('adminusers.power' , $group) ) {
			$prvlg_group = $db->escape_value($_POST['prvlg_group']);
			
			$edited_entry->prvlg_group= $prvlg_group;
			}
			
			
			if($current_user->can_see_this('adminusers.changepass' , $group) ) {
			$current_password = $edited_entry->password;
			if($password !='' && $password != $current_password ) {
			$phpass = new PasswordHash(8, true);
			$hashedpassword = $phpass->HashPassword($password);
			
			$edited_entry->password = $hashedpassword;
			}
			}
			
			if(isset($upload_present) && $upload_present == true) {
				$files = '';
				$f = 0;
				$images = array();
				$num_pics = 1;
				$target = $_FILES['upload_files'];
				$upload_problems = 0;
				for ($f ; $f < $num_pics ; $f++) :
					$file = "file";
					$string = $$file . "{$f}";
					$$string = new File();	
						if(!empty($_FILES['upload_files']['name'][$f])) {
							$$string->attach_file($_FILES['upload_files'], $f);
							if ($$string->save()) {
								$images[$f] = $$string->id;
							} else {
								$upl_msg = "{$lang['alert-upload_error']}:<br>";	
								$upl_msg .= join("<br />" , $$string->errors);							
								$upload_problems = 1;
							}
						}
				endfor;
				
				if(!empty($images)) {
					$final_string = implode("," , $images);
					//if($edited_entry->files != NULL) {
						//$edited_entry->files .= ",". $final_string;
					//} else {
						//$edited_entry->files .= $final_string;
					//}
					$edited_entry->avatar = $final_string;
				}
			}
			
			if(isset($_POST['prvlg_group']) && is_numeric($_POST['prvlg_group']) && $current_user->can_see_this('adminusers.power' , $group) ) {
				$edited_entry->prvlg_group == $db->escape_value($_POST['prvlg_group']);
			}
			
			if ($edited_entry->update()) {
				//Log::log_action($current_user->id , "Edit User object" , "Edit User object ({$edited_entry->name}) - id #({$edited_entry->id})" );
				$msg = $lang['alert-update_success'];
				if(isset($upl_msg)) {
					$msg .= $upl_msg;
				}
				redirect_to("{$url_mapper['admin/']}&section=users&edit=success&msg={$msg}");
			} else {
				$msg = $lang['alert-update_failed'];
				if(isset($upl_msg)) {
					$msg .= $upl_msg;
				}
				redirect_to("{$url_mapper['admin/']}&section=users&edit=fail&msg={$msg}");
			}
		} else {
			$msg = $lang['alert-auth_error'];
			redirect_to("{$url_mapper['admin/']}&section=users&edit=fail&msg={$msg}");
		}
}



if (isset($_POST['update_settings'])) {
		if(!$current_user->can_see_this("general_settings.update",$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to("{$url_mapper['admin/']}section=general&edit=fail&msg={$msg}");
		}
		if($_POST['hash'] == $_SESSION[$elhash]){
			//unset($_SESSION[$elhash]);
			$site_name= $db->escape_value($_POST["site_name"]);
			$site_description= $db->escape_value($_POST["site_description"]);
			$site_keywords= $db->escape_value($_POST["site_keywords"]);
			$site_status = $db->escape_value($_POST["site_status"]);
			$site_lang = $db->escape_value($_POST["site_lang"]);
			$closure_msg = $db->escape_value($_POST["closure_msg"]);
			$url_type = $db->escape_value($_POST["url_type"]);
			$q_approval = $db->escape_value($_POST["q_approval"]);
			$a_approval = $db->escape_value($_POST["a_approval"]);
			$reg_group = $db->escape_value($_POST["reg_group"]);
			$public_access = $db->escape_value($_POST["public_access"]);
			
			$settings_arr = Array(
									"site_name" => $site_name,
									"site_description" => $site_description,
									"site_keywords" => $site_keywords,
									"site_status" => $site_status,
									"site_lang" => $site_lang,
									"closure_msg" => $closure_msg,
									"url_type" => $url_type,
									"q_approval" => $q_approval,
									"a_approval" => $a_approval,
									"reg_group" => $reg_group,
									"public_access" => $public_access
								);
			$general_settings->value = serialize($settings_arr);
			if ($general_settings->update()) {
				$msg = $lang['alert-update_success'];
				redirect_to("{$url_mapper['admin/']}section=general&edit=success&msg={$msg}");
			} else {
				$msg = $lang['alert-update_failed'];
				redirect_to("{$url_mapper['admin/']}section=general&edit=fail&msg={$msg}");
			}
		} else {
			$msg = $lang['alert-auth_error'];
			redirect_to("{$url_mapper['admin/']}section=general&edit=fail&msg={$msg}");
		}
}

if (isset($_POST['add_group'])) {
		if(!$current_user->can_see_this("groups.create",$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to("{$url_mapper['admin/']}section=groups&edit=fail&msg={$msg}");
		}
		if($_POST['hash'] == $_SESSION[$elhash]){
			//unset($_SESSION[$elhash]);
			$privileges_raw= $_POST["privileges"];
			$name= $db->escape_value($_POST["name"]);
			
			//$privileges_danger = "index,dashboard,".implode("," , $privileges_raw);
			$privileges_danger = implode("," , $privileges_raw);
			$privileges = $db->escape_value($privileges_danger);
			
			$new_entry = New Group();
			$new_entry->name = $name;
			$new_entry->privileges = $privileges;
			
			if ($new_entry->create()) {
				
				//Log::log_action($current_user->id , "Add Group object" , "Add new Group object to application ({$new_entry->name})" );
				
				$msg = $lang['alert-create_success'];
				if($upload_problems == '1') {
					$msg .= "<hr>{$upl_msg}";
				}
				redirect_to("{$url_mapper['admin/']}section=groups&edit=success&msg={$msg}");
			} else {
				$msg = $lang['alert-create_failed'];
				redirect_to("{$url_mapper['admin/']}section=groups&edit=fail&msg={$msg}");
			}
		} else {
			$msg = $lang['alert-auth_error'];
			redirect_to("{$url_mapper['admin/']}section=groups&edit=fail&msg={$msg}");
		}
}


if (isset($_POST['edit_pages'])) {
		if(!$current_user->can_see_this("pages.update",$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to("{$url_mapper['admin/']}section=pages&edit=fail&msg={$msg}");
		}
		if($_POST['hash'] == $_SESSION[$elhash]){
			//unset($_SESSION[$elhash]);
			
			$contact_us = MiscFunction::get_function("contact-us");
			$about_us = MiscFunction::get_function("about-us");
			$privacy_policy = MiscFunction::get_function("privacy-policy");	
			$terms = MiscFunction::get_function("terms");	
			
			$about_us_value = $_POST['about-us'];
			$privacy_policy_value = $_POST['privacy-policy'];
			$contact_us_value = $_POST['contact-us'];
			$contact_us_msg = $_POST['contact-us-msg'];
			$terms_value = $_POST['terms'];
			
			$about_us->value = $about_us_value;
			$contact_us->msg = $contact_us_msg;
			$contact_us->value = $contact_us_value;
			$privacy_policy->value = $privacy_policy_value;
			$terms->value = $terms_value;
			
			
			$contact_us->update();
			$about_us->update();
			$privacy_policy->update();
			$terms->update();
			
			//if ($contact_us->update() || $about_us->update() || $privacy_policy->update() || $terms->update() ) {
				
				//Log::log_action($current_user->id , "Add Group object" , "Add new Group object to application ({$new_entry->name})" );
				
				$msg = $lang['alert-update_success'];
				redirect_to("{$url_mapper['admin/']}section=pages&edit=success&msg={$msg}");
			/*} else {
				$msg = "Unable to save data, please try again";
				redirect_to("{$url_mapper['admin/']}section=pages&edit=fail&msg={$msg}");
			}*/
		} else {
			$msg = $lang['alert-auth_error'];
			redirect_to("{$url_mapper['admin/']}section=pages&edit=fail&msg={$msg}");
		}
}

if (isset($_POST['edit_adblocks'])) {
		if(!$current_user->can_see_this("admanager.update",$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to("{$url_mapper['admin/']}section=admanager&edit=fail&msg={$msg}");
		}
		if($_POST['hash'] == $_SESSION[$elhash]){
			//unset($_SESSION[$elhash]);
			
			$between_q = $_POST['between-q'];
			$between_a = $_POST['between-a'];
			$lt_sidebar = $_POST['lt-sidebar'];
			$rt_sidebar = $_POST['rt-sidebar'];
			
			$admanager1->value = $between_q;
			$admanager1->msg = $between_a;
			$admanager2->value = $lt_sidebar;
			$admanager2->msg = $rt_sidebar;
			
			$admanager1->update();
			$admanager2->update();
			
			//if ($contact_us->update() || $about_us->update() || $privacy_policy->update() || $terms->update() ) {
				
				//Log::log_action($current_user->id , "Add Group object" , "Add new Group object to application ({$new_entry->name})" );
				
				$msg = $lang['alert-update_success'];
				redirect_to("{$url_mapper['admin/']}section=admanager&edit=success&msg={$msg}");
			/*} else {
				$msg = "Unable to save data, please try again";
				redirect_to("{$url_mapper['admin/']}section=pages&edit=fail&msg={$msg}");
			}*/
		} else {
			$msg = $lang['alert-auth_error'];
			redirect_to("{$url_mapper['admin/']}section=admanager&edit=fail&msg={$msg}");
		}
}

if (isset($_POST['edit_profanity_filter'])) {
		if(!$current_user->can_see_this("profanity_filter.update",$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to("{$url_mapper['admin/']}section=profanity_filter&edit=fail&msg={$msg}");
		}
		if($_POST['hash'] == $_SESSION[$elhash]){
			//unset($_SESSION[$elhash]);
			
			$profanity_filter = MiscFunction::get_function("profanity_filter");
			
			$profanity_filter_value = $db->escape_value(strip_tags($_POST['filter']));
			
			$profanity_filter->value = $profanity_filter_value;
			
			if ($profanity_filter->update()) {
				
				//Log::log_action($current_user->id , "Add Group object" , "Add new Group object to application ({$new_entry->name})" );
				
				$msg = $lang['alert-update_success'];
				redirect_to("{$url_mapper['admin/']}section=profanity_filter&edit=success&msg={$msg}");
			} else {
				$msg = $lang['alert-update_failed'];
				redirect_to("{$url_mapper['admin/']}section=profanity_filter&edit=fail&msg={$msg}");
			}
		} else {
			$msg = $lang['alert-auth_error'];
			redirect_to("{$url_mapper['admin/']}section=profanity_filter&edit=fail&msg={$msg}");
		}
}

if (isset($_POST['edit_group'])) {
		if(!$current_user->can_see_this("groups.update",$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to("{$url_mapper['admin/']}section=groups&edit=fail&msg={$msg}");
		}
		if($_POST['hash'] == $_SESSION[$elhash]){
			//unset($_SESSION[$elhash]);
			
			$edit_id = $db->escape_value($_POST["edit_id"]);
			
			$privileges_raw= $_POST["privileges"];
			$name= $db->escape_value($_POST["name"]);
			
			$privileges_danger = implode("," , $privileges_raw);
			$privileges = $db->escape_value($privileges_danger);

			$edited_entry = Group::get_specific_id($edit_id);
			$edited_entry->name = $name;
			$edited_entry->privileges = $privileges;
			
			if ($edited_entry->update()) {
				//Log::log_action($current_user->id , "Edit Group object" , "Edit Group object ({$edited_entry->name}) - id #({$edited_entry->id})" );
				
				$msg = $lang['alert-update_success'];
				redirect_to("{$url_mapper['admin/']}section=groups&edit=success&msg={$msg}");
			} else {
				$msg = $lang['alert-update_failed'];
				redirect_to("{$url_mapper['admin/']}section=groups&edit=fail&msg={$msg}");
			}
		} else {
			$msg = $lang['alert-auth_error'];
			redirect_to("{$url_mapper['admin/']}section=groups&edit=fail&msg={$msg}");
		}
}


require_once('assets/includes/navbar.php');


$sections = Array('index.read,pages.read,error-404.read|Home' => 
					Array(
								'index.notifications|Show Notifications list' => Array(),
								'index.post|Show Post Question form' => Array(),
								'index.feed|Show Feeds list' => Array('feed.follow|Follow Feeds list'),
							),
					'questions.read|Questions' => 
					Array(
								'questions.interact|Like/Dislike/Follow Questions' => array(),
								'post.read,questions.create|Post Questions' => array('questions.power|Post Immediately (without admin approval)'),
								'questions.update|Update Questions' => array(),
								'questions.delete|Delete Questions' => array(),
							),
					'answers.read|Answers' => 
					Array(
								'answers.create|Post Answers' => array('answers.power|Post Immediately (without admin approval)'),
								'answers.update|Update Answers' => array(),
								'answers.delete|Delete Answers' => array(),
							),
							
					'users.read|Profiles' => 
					Array(
								'users.follow|Follow Users' => array(),
								'users.update|Update Account' => array('users.changepass|Change Password' , 'users.changemail|Change Email'),
								'users.delete|Delete Account' => array(),
							),
					'admin.read|Admin Section' => 
					Array(
								'dashboard.read|Show dashboard' => Array(),
								'general_settings.update|Update General Site Settings' => Array(),
								'profanity_filter.update|Update Profanity Filter' => Array(),
								'pending.read|Show Pending Posts' => Array(
																						'pending.update|Approve questions & answers'
																					),
								'pages.read|Show Pages section' => Array(
																						'pages.update|Update pages'
																					),
								'adminusers.read|Show Users profiles' => Array(
																						'adminusers.update|Edit users profiles',
																						'adminusers.changepass|Edit users password',
																						'adminusers.changemail|Edit users email',
																						'adminusers.changeusername|Edit users username',
																						'adminusers.power|Change users privileges',
																						'adminusers.suspend|Suspend accounts',
																						'adminusers.delete|Delete users profiles'
																					),
								'admintopics.read|Show topics page' => Array(
																						'admintopics.update|Edit Topic',
																						'admintopics.delete|Delete Topic'
																					),
								'admanager.read|Show AdManager page' => Array(
																						'admanager.update|Edit Ads Blocks'
																					),
								'groups.read|Show User Group Privileges page' => Array(
																								'groups.create|Create new privilege groups', 
																								'groups.update|Update privilege groups', 
																								'groups.delete|Delete privilege groups'
																							),
								
							)
				);

if($current_user->can_see_this('pending.read' , $group)) {
	$pending_q = Question::count_pending();
	$pending_a = Answer::count_pending();
	$pending_posts = $pending_q + $pending_a;
	$pending_posts_badge = '';
	if($pending_posts) {
		$pending_posts_badge = "&nbsp;&nbsp;<span class='badge badge-inverse'>{$pending_posts}</span>";
	}
	
	$pending_reports = Report::count_pending();
	$pending_reports_badge = '';
	if($pending_reports) {
		$pending_reports_badge = "&nbsp;&nbsp;<span class='badge badge-inverse'>{$pending_reports}</span>";
	}
	
}
?>
<div class="container">		

<div class="row">
	
	<div class="col-md-3 ">
		<h4><i class="glyphicon glyphicon-wrench"></i>&nbsp;&nbsp;<?php echo $lang['admin-title']; ?></h4>
		<hr>
		<ul class="feed-ul ">
			<?php if($current_user->can_see_this('dashboard.read' , $group)) { ?><li><a data-toggle="tab" href="#dashboard" class="col-md-12"><?php echo $lang['admin-section-dashboard']; ?></a></li><?php } ?>
			<?php if($current_user->can_see_this('general_settings.update' , $group)) { ?><li><a data-toggle="tab" href="#general" class="col-md-12"><?php echo $lang['admin-section-general']; ?></a></li><?php } ?>
			<?php if($current_user->can_see_this('pending.read' , $group)) { ?><li><a data-toggle="tab" href="#pending" class="col-md-12"><?php echo $lang['admin-section-pending'] . ' ' . $pending_posts_badge; ?></a></li><?php } ?>
			<?php if($current_user->can_see_this('pending.read' , $group)) { ?><li><a data-toggle="tab" href="#reports" class="col-md-12"><?php echo $lang['admin-section-reports'] . ' ' . $pending_reports_badge; ?></a></li><?php } ?>
			<?php if($current_user->can_see_this('adminusers.read' , $group)) { ?><li><a data-toggle="tab" href="#users" class="col-md-12"><?php echo $lang['admin-section-users']; ?></a></li><?php } ?>
			<?php if($current_user->can_see_this('groups.read' , $group)) { ?><li><a data-toggle="tab" href="#groups" class="col-md-12"><?php echo $lang['admin-section-groups']; ?></a></li><?php } ?>
			<?php if($current_user->can_see_this('pages.update' , $group)) { ?><li><a data-toggle="tab" href="#pages" class="col-md-12"><?php echo $lang['admin-section-pages']; ?></a></li><?php } ?>
			<?php if($current_user->can_see_this('admintopics.read' , $group)) { ?><li><a data-toggle="tab" href="#topics" class="col-md-12"><?php echo $lang['admin-section-topics']; ?></a></li><?php } ?>
			<?php if($current_user->can_see_this('admanager.read' , $group)) { ?><li><a data-toggle="tab" href="#admanager" class="col-md-12"><?php echo $lang['admin-section-admanager']; ?></a></li><?php } ?>
			<?php if($current_user->can_see_this('profanity_filter.update' , $group)) { ?><li><a data-toggle="tab" href="#profanity_filter" class="col-md-12"><?php echo $lang['admin-section-filter']; ?></a></li><?php } ?>
			
		</ul>
		
	</div>
	
	
	<div class="col-md-9">
	
		<?php
			if (isset($_GET['edit']) && isset($_GET['msg']) && $_GET['edit'] == "success") :
			$status_msg = $db->escape_value($_GET['msg']);				
		?>
			<div class="alert alert-success">
				<i class="glyphicon glyphicon-check"></i> <strong><?php echo $lang['alert-type-success']; ?>!</strong>&nbsp;&nbsp;<?php echo $status_msg; ?>
			</div>
		<?php
			endif; 	
			if (isset($_GET['edit']) && isset($_GET['msg']) && $_GET['edit'] == "fail") :
			$status_msg = $db->escape_value($_GET['msg']);		
		?>
			<div class="alert alert-danger">
				<i class="glyphicon glyphicon-times"></i> <strong><?php echo $lang['alert-type-error']; ?>!</strong>&nbsp;&nbsp;<?php echo $status_msg; ?>
			</div>
			
		<?php 
			endif;
		?>
		
		<div class="tab-content">
			<?php 
				$section = "dashboard";
				if(isset($_GET['section']) && $_GET['section'] != '') {
					switch ($_GET['section']) {
						case 'general':
							$section = 'general';
						break;
						case 'dashboard':
							$section = 'dashboard';
						break;
						case 'users':
							$section = 'users';
						break;
						case 'groups':
							$section = 'groups';
						break;
						case 'pending':
							$section = 'pending';
						break;
						case 'reports':
							$section = 'reports';
						break;
						case 'topics':
							$section = 'topics';
						break;
						case 'admanager':
							$section = 'admanager';
						break;
						case 'pages':
							$section = 'pages';
						break;
						case 'profanity_filter':
							$section = 'profanity_filter';
						break;
						default :
							redirect_to($url_mapper['error/404/']);
						break;
					}
				}
				
				$contact_us = MiscFunction::get_function("contact-us");
				$about_us = MiscFunction::get_function("about-us");
				$privacy_policy = MiscFunction::get_function("privacy-policy");
				$terms = MiscFunction::get_function("terms");
				$profanity_filter = MiscFunction::get_function("profanity_filter");
			?>
			
			<?php if($current_user->can_see_this('dashboard.read' , $group)) { ?>
			<div id="dashboard" class="tab-pane fade in <?php if($section == 'dashboard') { echo 'active'; } ?>">
			<h3 class="page-header"><?php echo $lang['admin-hello']; ?>, <?php echo $current_user->f_name; ?>!</h3>
			
			<p style="font-size:16px"><?php $str = $lang['admin-dashboard-users']; $str = str_replace('[COUNT]' , User::count_everything(' AND id != 1000 AND deleted = 0 ') , $str ); echo $str; ?></p>
			
				<canvas id="user-registration"  class="full" height="100"></canvas>
			
				<br><hr><br>

				<p style="font-size:16px"><?php $str = $lang['admin-dashboard-questions']; $str = str_replace('[COUNT]' , Question::count_everything() , $str ); echo $str; ?></p>
				
				
				<canvas id="questions"  class="full" height="100"></canvas>
				
				<br><hr><br>

				<p style="font-size:16px"><?php $str = $lang['admin-dashboard-answers']; $str = str_replace('[COUNT]' , Answer::count_everything() , $str ); echo $str; ?></p>
				
				
				<canvas id="answers"  class="full" height="100"></canvas>
				
			
			</div>
			<?php } if($current_user->can_see_this('pages.update' , $group)) { ?>
			<div id="pages" class="tab-pane fade in <?php if($section == 'pages') { echo 'active'; } ?>">
				<form method="post" action="<?php echo $url_mapper['admin/']; ?>">
			
				<h3 class="page-header"><?php echo $lang['admin-pages-title']; ?></h3>
				
					<div class="row">
						
						<ul class="nav nav-tabs">
						  <li class="active"><a data-toggle="tab" href="#about-us" href="#me"><?php echo $lang['pages-about-title']; ?></a></li>
						  <li><a data-toggle="tab" href="#contact-us" href="#me" ><?php echo $lang['pages-contact-title']; ?></a></li>
						  <li><a data-toggle="tab" href="#privacy-policy" href="#me"><?php echo $lang['pages-privacy-title']; ?></a></li>
						  <li><a data-toggle="tab" href="#terms" href="#me"><?php echo $lang['pages-terms-title']; ?></a></li>
						</ul>
						<div class="tab-content">
							<div id="about-us" class="tab-pane fade in active">
								<br>
								<textarea class="summernote" name="about-us" ><?php echo $about_us->value; ?></textarea>
								<br><br>
							</div>
							<div id="contact-us" class="tab-pane fade ">
								<br>
								<textarea class="summernote" name="contact-us"><?php echo $contact_us->value; ?></textarea>
								<hr>
								<div class="form-group">
									<label for="msg"><?php echo $lang['admin-pages-about-email']; ?></label>
									<input type="email" class="form-control" name="contact-us-msg" id="msg" placeholder="" value="<?php echo $contact_us->msg; ?>">
								</div>
								<br><br>
							</div>
							<div id="privacy-policy" class="tab-pane fade ">
								<br>
								<textarea class="summernote" name="privacy-policy"><?php echo $privacy_policy->value; ?></textarea>
								<br><br>
							</div>
							<div id="terms" class="tab-pane fade ">
								<br>
								<textarea class="summernote" name="terms"><?php echo $terms->value; ?></textarea>
								<br><br>
							</div>
						</div>
						
					</div>
					
							<center>
								<input class="btn btn-success" type="submit" name="edit_pages" value="<?php echo $lang['btn-submit']; ?>">
							</center>
						
					<?php 
						$_SESSION[$elhash] = $random_hash;
						echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>";
					?>
					</form>
			</div>
			<?php } ?>
			
			
			<?php if($current_user->can_see_this('profanity_filter.update' , $group)) { ?>
			<div id="profanity_filter" class="tab-pane fade in <?php if($section == 'profanity_filter') { echo 'active'; } ?>">
				<form method="post" action="<?php echo $url_mapper['admin/']; ?>">
				
				<h3 class="page-header"><?php echo $lang['admin-filter-title']; ?></h3>
				
					<div class="row">
						<textarea name="filter" class="form-control" rows="15"><?php echo $profanity_filter->value; ?></textarea>
					</div>
					<br><br>
							<center>
								<input class="btn btn-success" type="submit" name="edit_profanity_filter" value="<?php echo $lang['btn-submit']; ?>">
							</center>
						
					<?php 
						$_SESSION[$elhash] = $random_hash;
						echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>";
					?>
					</form>
			</div>
			<?php } ?>
			
			
			
			
			
			<?php if($current_user->can_see_this('general_settings.update' , $group)) { ?>
			<div id="general" class="tab-pane fade in <?php if($section == 'general') { echo 'active'; } ?>">
				<form method="post" action="<?php echo $url_mapper['admin/']; ?>">
			
				<h3 class="page-header"><?php echo $lang['admin-general-title']; ?></h3>
				
					<div class="row">
						<div class="col-md-6">
							<h4><center><?php echo $lang['admin-general-site-title']; ?></center></h4><br>
							<div class="form-group">
								<label for="site_name"><?php echo $lang['admin-general-site-name']; ?></label>
								<input type="text" class="form-control" name="site_name" id="site_name" value="<?php echo $settings['site_name']; ?>" placeholder="Site Name.." required>
								<br>
								
								<label for="site_description"><?php echo $lang['admin-general-site-description']; ?></label>
								<input type="text" class="form-control" name="site_description" id="site_description" value="<?php echo $settings['site_description']; ?>" placeholder="Site Description.." required>
								<br>
								
								<label for="site_keywords"><?php echo $lang['admin-general-site-keywords']; ?></label>
								<input type="text" class="form-control" name="site_keywords" id="site_keywords" value="<?php echo $settings['site_keywords']; ?>" placeholder="Keywords.." required>
								<br>
								
								<?php $langs = scandir($current.'/lang/'); unset($langs[0]); unset($langs[1]); ?>
								
								<label for="site_lang"><?php echo $lang['admin-general-site-lang']; ?></label>&nbsp;&nbsp;
								<select id="site_lang" name="site_lang" class="form-control select2" style="width:200px" data-placeholder="Site Language..">
								<?php  foreach($langs as $l) {
									$l = explode('.' , $l);
									echo "<option value='{$l[1]}' ";
										if($l[1] == $settings['site_lang']) { echo ' selected'; }
									echo " >{$l[1]}</option>";
								} ?>	
								</select><br><br>
								
								<label for="site_status"><?php echo $lang['admin-general-site-status']; ?></label>&nbsp;&nbsp;
								<select id="site_status" name="site_status" class="form-control select2" style="width:200px" data-placeholder="Site Status..">
									<option value="1" <?php if($settings['site_status']== '1') { echo ' selected'; } ?>>Active</option>
									<option value="0" <?php if($settings['site_status']== '0') { echo ' selected'; } ?>>Closed</option>
								</select><br><br>
								<label for="closure_msg"><?php echo $lang['admin-general-site-status_msg']; ?></label>
								<input type="text" class="form-control" name="closure_msg" id="closure_msg" value="<?php echo $settings['closure_msg']; ?>" placeholder="Site Closed Message..">
							</div>
							<hr>
							<h4><center><?php echo $lang['admin-general-url-title']; ?></center></h4><br>
							<div class="form-group">
								<label for="url_type"><?php echo $lang['admin-general-url-type']; ?></label>&nbsp;&nbsp;
								<select id="url_type" name="url_type" class="form-control select2" style="width:200px" data-placeholder="URL Type.."> 
									<option value="slug" <?php if($settings['url_type']== 'slug') { echo ' selected'; } ?>>By Subject/Slug</option>
									<option value="id" <?php if($settings['url_type']== 'id') { echo ' selected'; } ?>>By ID</option>
								</select><br><br>
							</div>
						</div>
						<div class="col-md-6">
							<h4><center><?php echo $lang['admin-general-posting-title']; ?></center></h4><br>
							<div class="form-group">
								<label for="q_approval"><?php echo $lang['admin-general-posting-questions']; ?></label>&nbsp;&nbsp;
								<select id="q_approval" name="q_approval" class="form-control select2" style="width:200px"> 
									<option value="0" <?php if($settings['q_approval']== '0') { echo ' selected'; } ?> >Immediately</option>
									<option value="1" <?php if($settings['q_approval']== '1') { echo ' selected'; } ?>>After Admin Approval</option>
								</select><br><br>
								
								<label for="a_approval"><?php echo $lang['admin-general-posting-answers']; ?></label>&nbsp;&nbsp;
								<select id="a_approval" name="a_approval" class="form-control select2" style="width:200px"> 
									<option value="0" <?php if($settings['a_approval']== '0') { echo ' selected'; } ?>>Immediately</option>
									<option value="1" <?php if($settings['a_approval']== '1') { echo ' selected'; } ?> >After Admin Approval</option>
								</select><br><br>
							</div>
							<br><br>
							<h4><center><?php echo $lang['admin-general-access-title']; ?></center></h4><br>
							<div class="form-group">
								<label for="public_access"><?php echo $lang['admin-general-access-login']; ?></label>&nbsp;&nbsp;
								<select id="public_access" name="public_access" class="form-control select2" style="width:200px"> 
									<option value="0" <?php if($settings['public_access']== '0') { echo ' selected'; } ?>>Disabled</option>
									<option value="1" <?php if($settings['public_access']== '1') { echo ' selected'; } ?> >Enabled</option>
								</select><br><br>
							</div>
							<br><br>
							<h4><center><?php echo $lang['admin-general-reg-title']; ?></center></h4><br>
							<div class="form-group">
								<label for="reg_group"><?php echo $lang['admin-general-reg-group']; ?></label>&nbsp;&nbsp;
								<select id="reg_group" name="reg_group" class="form-control select2" style="width:200px"> 
									<?php 
										$groups = Group::get_users(); 
										foreach($groups as $g) {
											echo "<option value='{$g->id}' ";
												if($g->id == $settings['reg_group']) { echo ' selected'; }
											echo " >{$g->name}</option>";
										}
									?>
								</select><br><br>
							</div>
							
						</div>
						
					</div>
					
							<center>
								<input class="btn btn-success" type="submit" name="update_settings" value="<?php echo $lang['btn-submit']; ?>">
							</center>
						
					<?php 
						$_SESSION[$elhash] = $random_hash;
						echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>";
					?>
					</form>
			</div>
			<?php } ?>
			<?php if($current_user->can_see_this('pending.read' , $group)) { ?>
			<div id="pending" class="tab-pane fade <?php if($section == 'pending') { echo ' in active'; } ?>">
				
				<form method="post" action="<?php echo $url_mapper['admin/']; ?>">
			
				<h3 class="page-header"><?php echo $lang['admin-pending-title']; ?></h3>
				
					<div class="row">
						<div class="col-md-6">
							<h4><center><?php echo $lang['admin-pending-questions']; ?></center></h4><br>
							<?php $pending_q = Question::get_pending(); ?>
							<?php if($pending_q) { ?>
								<table class="custom_table table table-striped table-bordered" cellspacing="0" width="100%">
								<thead>
									<th><?php echo $lang['admin-pending-questions-title']; ?></th>
									<th><?php echo $lang['admin-pending-questions-user']; ?></th>
									<th><i class="fa fa-wrench"></i></th>
								</thead>
								<tbody><?php foreach($pending_q as $pq) { $pq_user = User::get_specific_id($pq->user_id); 
								if(URLTYPE == 'slug') {
									$url_type = $pq->slug;
								} else {
									$url_type = $pq->id;
								}
								?>
									<tr>
									<td><a href="<?php echo $url_mapper['questions/view']; echo $url_type; ?>" target="_blank"><?php echo $pq->title; ?></a></td>
									<td><a href="<?php echo $url_mapper['users/view']; echo $pq_user->id . '/'; ?>" target="_blank"><?php echo $pq_user->f_name . " " . $pq_user->l_name . "</a><br><small style='font-size:10px; color:grey'>" . date_ago($pq->created_at) . "</small>"; ?></td>
									<td>
										<p class="btn-group approve-machine"><a href="#me" class="btn btn-default btn-sm approve-item" data-obj="question" data-id="<?php echo $pq->id; ?>" data-action="approve"><i class="fa fa-check"></i></a>
										<a href="#me" class="btn btn-default  btn-sm reject-item" data-obj="question" data-id="<?php echo $pq->id; ?>" data-action="reject"><i class="fa fa-times"></i></a></p>
									</td></tr>
								<?php } ?>
								</tbody></table>
							<?php } ?>
						</div>
						<div class="col-md-6">
							<h4><center><?php echo $lang['admin-pending-answers']; ?></center></h4><br>
							<?php $pending_q = Answer::get_pending(); ?>
							<?php if($pending_q) { ?>
								<table class="custom_table table table-striped table-bordered" cellspacing="0" width="100%">
								<thead>
									<th><?php echo $lang['admin-pending-answers-comment']; ?></th>
									<th><?php echo $lang['admin-pending-answers-user']; ?></th>
									<th><i class="fa fa-wrench"></i></th>
								</thead>
								<tbody><?php foreach($pending_q as $pa) { $pa_user = User::get_specific_id($pa->user_id);
								$pq = Question::get_specific_id($pa->q_id);
								if(URLTYPE == 'slug') {
									$url_type = $pq->slug;
								} else {
									$url_type = $pq->id;
								}
								
								
							$string = strip_tags($pa->content);
							if (strlen($string) > 100) {
								$stringCut = substr($string, 0, 100);
								$string= substr($stringCut, 0, strrpos($stringCut, ' '))."..."; 
							}	
							
							if($string == '') { $string = 'Undefined'; }
								?>
									<tr>
									<td><a href="<?php echo $url_mapper['questions/view']; echo $url_type; echo "#answer-" . $pa->id; ?>" target="_blank"><?php echo $string; ?></a></td>
									<td><a href="<?php echo $url_mapper['users/view']; echo $pa_user->id; ?>/" target="_blank"><?php echo $pa_user->f_name . " " . $pa_user->l_name . "</a><br><small style='font-size:10px; color:grey'>" . date_ago($pa->created_at) . "</small>"; ?></td>
									<td>
										<p class="btn-group approve-machine"><a href="#me" class="btn btn-default btn-sm approve-item" data-obj="answer" data-id="<?php echo $pa->id; ?>" data-action="approve"><i class="fa fa-check"></i></a>
										<a href="#me" class="btn btn-default  btn-sm reject-item" data-obj="answer" data-id="<?php echo $pa->id; ?>" data-action="reject"><i class="fa fa-times"></i></a></p>
									</td></tr>
								<?php } ?>
								</tbody></table>
							<?php } ?>
						</div>
					</div>
					
					<?php 
						$_SESSION[$elhash] = $random_hash;
						echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>";
					?>
					</form>
				
			</div>
			
			<div id="reports" class="tab-pane fade <?php if($section == 'reports') { echo ' in active'; } ?>">
				
				<form method="post" action="<?php echo $url_mapper['admin/']; ?>">
			
				<h3 class="page-header"><?php echo $lang['admin-reports-title']; ?></h3>
				
					<div class="row">
						<div class="col-md-12">
							<?php $reports = Report::get_everything(' AND result= "" '); ?>
							<?php if($reports) { ?>
								<table class="custom_table table table-striped table-bordered" cellspacing="0" width="100%">
								<thead>
									<th>#</th>
									<th><?php echo $lang['admin-reports-post']; ?></th>
									<th><?php echo $lang['admin-reports-user']; ?></th>
									<th><?php echo $lang['admin-reports-info']; ?></th>
									<th><i class="fa fa-wrench"></i></th>
								</thead>
								<tbody><?php foreach($reports as $r) { $pq_user = User::get_specific_id($r->user_id); 
									if($r->obj_type == 'answer') {
										$a = $pq = Answer::get_specific_id($r->obj_id);
										$pq = Question::get_specific_id($a->q_id);
										if(URLTYPE == 'slug') {
											$url_type = $pq->slug;
										} else {
											$url_type = $pq->id;
										}
										$title = $lang['admin-reports-type-a'] . ': ' . $pq->title;
										$link = $url_mapper['questions/view'] . $url_type . '#answer-' . $r->id;
									} else {
										$pq = Question::get_specific_id($r->obj_id);
										if(URLTYPE == 'slug') {
											$url_type = $pq->slug;
										} else {
											$url_type = $pq->id;
										}
										$title = $lang['admin-reports-type-q'] . ': ' . $pq->title;
										$link = $url_mapper['questions/view'] . $url_type;
									}
									$i = 1;
								?>
									<tr>
									<td><?php echo $i; ?></td>
									<td><a href="<?php echo $link; ?>" target="_blank"><?php echo $title; ?></a></td>
									<td><a href="<?php echo $url_mapper['users/view']; echo $pq_user->id . '/'; ?>" target="_blank"><?php echo $pq_user->f_name . " " . $pq_user->l_name . "</a><br><small style='font-size:10px; color:grey'>" . date_ago($pq->created_at) . "</small>"; ?></td>
									<td><?php echo $r->info; ?></td>
									<td>
										<p class="btn-group approve-machine">
										<a href="#me" class="btn btn-default btn-sm approve-report-item" data-obj="<?php echo $r->obj_type; ?>" data-id="<?php echo $pq->id; ?>" data-action="approve_report" data-report_id="<?php echo $r->id; ?>"><?php echo $lang['admin-reports-approve_report']; ?></a>
										<a href="#me" class="btn btn-default  btn-sm reject-report-item" data-obj="<?php echo $r->obj_type; ?>" data-id="<?php echo $pq->id; ?>" data-action="reject_report" data-report_id="<?php echo $r->id; ?>"><?php echo $lang['admin-reports-reject_report']; ?></a></p>
									</td></tr>
								<?php $i++; } ?>
								</tbody></table>
							<?php } ?>
						</div>
						
					</div>
					
					<?php 
						$_SESSION[$elhash] = $random_hash;
						echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>";
					?>
					</form>
				
			</div>
			<?php } ?>
			<?php if($current_user->can_see_this('adminusers.read' , $group)) { ?>
			<div id="users" class="tab-pane fade <?php if($section == 'users') { echo 'in active'; } ?>">
				<h3><?php echo $lang['admin-users-title']; ?>
				
				<?php if($section == 'users' && isset($_GET['type']) && $_GET['type'] != '' ) { ?>
				<a href="<?php echo $url_mapper['admin/']; ?>/&section=users" class="btn btn-sm btn-primary"><i class="fa fa-arrow-<?php echo $lang['direction-left']; ?>"></i>&nbsp;<?php echo $lang['btn-back']; ?></a>
				<?php } ?>
				</h3>
				
				
				<?php if($section == 'users' && isset($_GET['type']) && $_GET['type'] == 'new' ) {
				?>
				
				
				
				<?php } elseif($section == 'users' && isset($_GET['type']) && $_GET['type'] == 'edit'  && isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['hash'])) {
					if(!User::check_id_existance($db->escape_value($_GET['id']))) {
						redirect_to($url_mapper['error/404/']);
					}
					$user = User::get_specific_id($db->escape_value($_GET['id']));
					if($user->avatar) {
						$img = File::get_specific_id($user->avatar);
						$quser_avatar= WEB_LINK."assets/".$img->image_path();
						
						$quser_avatar_path = UPLOADPATH."/".$img->image_path();
						if (!file_exists($quser_avatar_path)) {
							$quser_avatar = WEB_LINK.'assets/img/avatar.png';
						}
						
					} else {
						$quser_avatar = WEB_LINK.'assets/img/avatar.png';
					}
				?>
				
				
				
				<form method="post" action="<?php echo $url_mapper['admin/']; ?>/" enctype="multipart/form-data">
			
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="f_name"><?php echo $lang['admin-users-f_name']; ?></label>
								<input type="text" class="form-control" name="f_name" id="f_name" placeholder="First Name.." required value="<?php echo $user->f_name; ?>">
								<br>
								
								<label for="l_name"><?php echo $lang['admin-users-l_name']; ?></label>
								<input type="text" class="form-control" name="l_name" id="l_name" placeholder="Last Name.." required value="<?php echo $user->l_name; ?>">
								<br>
								
								<label for="mobile"><?php echo $lang['admin-users-phone']; ?></label>
								<input type="text" class="form-control" name="mobile" id="mobile" placeholder="Phone.." value="<?php echo $user->mobile; ?>">
								<br>
								
								<label for="address"><?php echo $lang['admin-users-address']; ?></label>
								<input type="text" class="form-control" name="address" id="address" placeholder="Address.." value="<?php echo $user->address; ?>">
								<br>
								
								</div>
							<hr>
								<br>
								<?php if($current_user->can_see_this('adminusers.power' , $group)  && $user->id != '1' ) { ?>
								<div class="form-group">
									<label for="prvlg_group"><?php echo $lang['admin-users-group']; ?></label>&nbsp;&nbsp;
									<select id="prvlg_group" name="prvlg_group" class="form-control" style="width:200px" <?php if($user->id == '1') { echo ' readonly'; } ?> >
										<?php 
											$groups = Group::get_everything(); 
											foreach($groups as $g) {
												echo "<option value='{$g->id}' ";
													if($g->id == $user->prvlg_group) { echo ' selected'; }
												echo " >{$g->name}</option>";
											}
										?>
									</select><br><br>
								</div>
								<?php } ?>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="comment"><?php echo $lang['admin-users-comment']; ?></label>
								<input type="text" class="form-control" name="comment" id="comment" placeholder="Short Description.." value="<?php echo $user->comment; ?>">
								<br>
								
								<label for="about"><?php echo $lang['admin-users-about']; ?></label>
								<textarea name="about" class="form-control" rows="3"><?php echo $user->about; ?></textarea>
								<br>
								
								
								<label class="control-label" for="img1_upl"><?php echo $lang['admin-users-avatar']; ?></label>
								<div class="controls">
									
									<img src="<?php echo $quser_avatar; ?>" class="img-polaroid img-circle" style="float:<?php echo $lang['direction-left']; ?>; padding:5px; margin-<?php echo $lang['direction-right']; ?>:10px; width:64px; height:64px" id="img1">
									<div style="height:64px; padding-top: 12px;width:200px;float:<?php echo $lang['direction-left']; ?>">
										<input class="text-input " type="file" name="upload_files[]" id="img1_upl"/><br/>
									</div>
								
								</div>
								
				<br><br><br><br><br>
				
				  <label for="username" class="control-label"><?php echo $lang['admin-users-username']; ?></label>
				  
					<div class="input-group">
					  <span class="input-group-addon" id="basic-addon1">@</span>
					  <input type="text" class="form-control " id="username" name="username" placeholder="" value="<?php echo $user->username; ?>"  <?php if(!$current_user->can_see_this('adminusers.changeusername' , $group) ) { ?> disabled readonly <?php } ?> >
					</div>
				  
				  
				  <br>
					
					<?php if($current_user->can_see_this('adminusers.changemail' , $group) ) { ?>
				  <label for="username" class="control-label"><?php echo $lang['admin-users-email']; ?></label>
				  <div class="controls"><input type="email" class="form-control " id="username" name="email" placeholder="Unchanged" ></div>
				  <?php } ?>
					<?php if($current_user->can_see_this('adminusers.suspend' , $group) ) { ?>
					<br>
					<label><input type="checkbox" data-checkbox="form-control" value="1" name="disabled" <?php if($user->disabled == '1') { echo ' checked'; } ?>> <?php echo $lang['admin-users-suspend']; ?></label>
					<?php } ?>
					
				  
				  <br><br>
				  
				  <?php if($current_user->can_see_this('adminusers.changepass' , $group) ) { ?>
				  <label for="password" class="control-label"><?php echo $lang['admin-users-pass']; ?></label>
				  <div class="controls"><input type="text" class="form-control " id="password" name="password" placeholder="Unchanged" ></div>
				  <?php } ?>
				   <div id="messages"></div>
							
					<br><br>
								
								</div>
							
						</div>
						
					</div>
					
							<center>
								<input class="btn btn-success" type="submit" name="edit_user" value="<?php echo $lang['btn-submit']; ?>">
							</center>
						
					<?php 
						$_SESSION[$elhash] = $random_hash;
						echo "<input type=\"hidden\" name=\"edit_id\" value=\"".$user->id."\" readonly/>";
						echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>";
					?>
					</form>
				
				
				
				
				<?php
				} elseif($section == 'users' && isset($_GET['type']) && $_GET['type'] == 'delete'  && isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['hash'])) {
					$id = $db->escape_value($_GET['id']);
					
					if(!User::check_id_existance($id)) {
						redirect_to("{$url_mapper['error/404/']}");
					}
					
					$this_obj = User::get_specific_id($id);
					
					if(!$current_user->can_see_this("adminusers.delete",$group)) {
						$msg = $lang['alert-restricted'];
						redirect_to("{$url_mapper['admin/']}section=users&edit=fail&msg={$msg}");
					}
					if($id == "1") {
						$msg = $lang['alert-restricted'];
						redirect_to("{$url_mapper['admin/']}section=users&edit=fail&msg={$msg}");
					}
					$this_obj->deleted = 1;
					if($this_obj->update()) {
						$msg = $lang['alert-delete_success'];
						//Log::log_action($current_user->id , "Delete Group object" , "Delete Group object named ({$this_obj->name}) - id #({$this_obj->id})");
						redirect_to("{$url_mapper['admin/']}section=users&edit=success&msg={$msg}");
					} else {
						$msg = $lang['alert-delete_failed'];
						redirect_to("{$url_mapper['admin/']}section=users&edit=fail&msg={$msg}");
					}
					
					
				} else { ?>
				
					<table class="table table-hover table-bordered">
                      <thead>
                        <tr>
                          <th style='width:10px'>#</th>
                          <th><?php echo $lang['admin-users-f_name']; ?></th><th><?php echo $lang['admin-users-group']; ?></th>
						  <th><?php echo $lang['admin-users-phone']; ?></th><th><?php echo $lang['admin-users-email']; ?></th>
						  <th><?php echo $lang['admin-users-questions']; ?></th><th><?php echo $lang['admin-users-answers']; ?></th>
                          <th style='width:150px'><i class="fa fa-wrench"></i></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
							
							if (isset($_GET['per_page']) && is_numeric($_GET['per_page']) ) {
								$per_page= $_GET['per_page'];
							} else {
								$per_page=20;
							}
							
							if (isset($_GET['page']) && is_numeric($_GET['page']) ) {
								$page= $_GET['page'];
							} else {
								$page=1;
							}
							
							
							$query = '';
							if(isset($_GET['search']) && $_GET['search'] == true && isset($_GET['data']) ) {
								$query = " AND (f_name LIKE '%" . $db->escape_value($_GET['data']) .  "%' OR l_name LIKE '%" . $db->escape_value($_GET['data']) .  "%') ";
							}
							
							$total_count = User::count_everything(" AND id != 1000 AND deleted = 0 {$query} ");
							$pagination = new Pagination($page, $per_page, $total_count);
							$all_obj= User::get_users($query," LIMIT {$per_page} OFFSET {$pagination->offset()} ");
						
							
							$i= (($page-1) * $per_page) + 1;
							
							
							foreach($all_obj as $obj) :
							
							if($obj->avatar) {
								$pic = "<a href=\"#modal-image-{$obj->id}\" data-toggle='modal' class='btn btn-sm btn-icon btn-rounded btn-warning' data-rel='' data-placement='top' title='Profile Picture' data-original-title='Profile Picture'><i class='fa fa-search'></i></a>";
							} else {
								$pic = "";
							}
							
							if($current_user->can_see_this("users.update",$group)) {
								$edit = "<a href=\"{$url_mapper['admin/']}/&section=users&id={$obj->id}&type=edit&hash={$random_hash}\" class='btn btn-sm btn-icon btn-rounded btn-primary' data-rel='' data-placement='top' title='Edit' data-original-title='Edit'  ><i class='fa fa-pencil'></i></a>";
							} else {
								$edit = "<a href=\"#me\" class='btn btn-sm btn-icon btn-rounded btn-default' data-rel='' data-placement='top' title='Edit (unavailable)' data-original-title='Edit (unavailable)'  ><i class='fa fa-pencil'></i></a>";
							}
							
							if($current_user->can_see_this("users.delete",$group)) {
								$delete = "<a href=\"{$url_mapper['admin/']}/&section=users&id={$obj->id}&type=delete&hash={$random_hash}\" class='btn btn-sm btn-icon btn-rounded btn-danger' data-rel='' data-placement='top' title='delete' data-original-title='delete'   onclick=\"return confirm('Are you sure you want to delete this record?');\" ><i class='fa fa-times'></i></a>";
							} else {
								$delete = "<a href=\"#me\" class='btn btn-sm btn-icon btn-rounded btn-default' data-rel='' data-placement='top' title='delete (unavailable)' data-original-title='delete (unavailable)'  ><i class='fa fa-times'></i></a>";
							}
							
							$usergroup = Group::get_specific_id($obj->prvlg_group);
							$questions = Question::count_questions_for($obj->id," ");
							$answers = Answer::count_answers_for_user($obj->id," ");
							
						?>
						<tr <?php if($obj->disabled) { echo ' style="color:red" '; } ?> >
                          <td><?php echo $i; ?></td>
                          <td><?php echo $obj->f_name. ' ' . $obj->l_name;?></td>
						  <td><?php echo $usergroup->name; ?></td>
						  <td><?php echo $obj->mobile; ?></td>
						  <td><?php echo $obj->email; ?></td>
						  <td><?php echo $questions; ?></td>
						  <td><?php echo $answers; ?></td>
                          <td><div class="btn-group"><?php echo $pic .$edit . $delete; ?></div></td>
						  
						<?php 
							if(isset($obj->avatar) && $obj->avatar) {
								$img = File::get_specific_id($obj->avatar);
								$link = UPL_FILES."/".$img->image_path();
						?>
						<div class="modal fade modal-image" id="modal-image-<?php echo $obj->id; ?>" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
							<div class="modal-dialog">
							  <div class="modal-content">
								<div class="modal-header">
								  <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
								</div>
								<div class="modal-body">
								  <img src="<?php echo $link; ?>" alt="picture 1" class="img-responsive">
								</div>
								<div class="modal-footer">
								  <p><?php echo $obj->f_name . ' ' . $obj->l_name; ?></p>
								</div>
							  </div>
							</div>
						  </div>
						<?php 
							}
						?>
                        </tr>
                        
						<?php 
							$i++;
							endforeach;
						?>
                      </tbody>
                    </table>
					
					<?php
					
					if(isset($pagination) && $pagination->total_pages() > 1) {
					?>
					<div class="pagination btn-group">
					
							<?php
							if ($pagination->has_previous_page()) {
								$page_param = $url_mapper['admin/'];
								$page_param .= "&section=users&page=";
								$page_param .= $pagination->previous_page();

							echo "<a href=\"{$page_param}\" class=\"btn btn-default\" type=\"button\"><i class=\"glyphicon glyphicon-chevron-{$lang['direction-left']}\"></i></a>";
							} else {
							?>
							<a class="btn btn-default" type="button"><i class="glyphicon glyphicon-chevron-<?php echo $lang['direction-left']; ?>"></i></a>
							<?php
							}
							
							for($p=1; $p <= $pagination->total_pages(); $p++) {
								if($p == $page) {
									echo "<a class=\"btn btn-default active\" type=\"button\">{$p}</a>";
								} else {
									$page_param = $url_mapper['admin/'];
									$page_param .= "&section=users&page=";
									$page_param .= $p;

									echo "<a href=\"{$page_param}\" class=\"btn btn-default\" type=\"button\">{$p}</a>";
								}
							}
							if($pagination->has_next_page()) {
								$page_param = $url_mapper['admin/'];
								$page_param .= "&section=users&page=";
								$page_param .= $pagination->next_page();

							echo " <a href=\"{$page_param}\" class=\"btn btn-default\" type=\"button\"><i class=\"glyphicon glyphicon-chevron-{$lang['direction-right']}\"></i></a> ";
							} else {
							?>
							<a class="btn btn-default" type="button"><i class="glyphicon glyphicon-chevron-<?php echo $lang['direction-right']; ?>"></i></a>
							<?php
							}
							?>
					
					</div>
					<?php
					}
					?>
				
				
				<?php } ?>
				
				
			</div>
			<?php } ?>
			
			
			<?php if($current_user->can_see_this('admintopics.read' , $group)) { ?>
			<div id="topics" class="tab-pane fade <?php if($section == 'topics') { echo 'in active'; } ?>">
				<h3><?php echo $lang['admin-topics-title']; ?>
				<?php
					$back = $url_mapper['admin/'].'&section=topics';
					$add = $url_mapper['admin/'].'&section=topics&type=new&hash='.$random_hash;
					if(isset($_GET['ref']) && $_GET['ref'] != '' ) { $back = $url_mapper['feed/']."{$_GET['ref']}/"; } 
				?>
				<?php if($section == 'topics' && isset($_GET['type']) && $_GET['type'] != '' ) { ?>
				<a href="<?php echo $back; ?>" class="btn btn-sm btn-primary"><i class="fa fa-arrow-<?php echo $lang['direction-left']; ?>"></i>&nbsp;<?php echo $lang['btn-back']; ?></a>
				<?php } else { ?>
				<a href="<?php echo $add; ?>" class="btn btn-sm btn-danger"><i class="fa fa-plus"></i>&nbsp;<?php echo $lang['btn-add']; ?></a>
				<?php } ?>
				</h3>
				<hr>
				
				<?php if($section == 'topics' && isset($_GET['type']) && $_GET['type'] == 'new' ) {
				?>
				
				
				<form method="post" action="<?php echo $url_mapper['admin/']; ?>/" enctype="multipart/form-data">
			
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="name"><?php echo $lang['admin-topics-name']; ?></label>
									<input type="text" class="form-control" name="name" id="name" placeholder="Topic Name.." required value="">
								<br>
								<label class="control-label" for="img1_upl"><?php echo $lang['admin-topics-avatar']; ?></label>
								<div class="controls">
									
									<img src="<?php echo WEB_LINK.'assets/img/topic.png'; ?>" class="img-polaroid img-circle" style="float:<?php echo $lang['direction-left']; ?>; padding:5px; margin-<?php echo $lang['direction-right']; ?>:10px; width:64px; height:64px" id="img1">
									<div style="height:64px; padding-top: 12px;width:200px;float:<?php echo $lang['direction-left']; ?>">
										<input class="text-input " type="file" name="upload_files[]" id="img1_upl"/><br/>
									</div>
								
								</div>
								
							</div>
							
						</div>
						<div class="col-md-6">
							<div class="form-group">
								
								<label for="description"><?php echo $lang['admin-topics-description']; ?></label>
								<textarea class="form-control" rows='5' name="description" id="description" placeholder="Topic Description.."></textarea>
								<br>
								
								
								</div>
						</div>
						
					</div>
					
							<center>
								<input class="btn btn-success" type="submit" name="add_topic" value="<?php echo $lang['btn-submit']; ?>">
							</center>
						
					<?php 
						$_SESSION[$elhash] = $random_hash;
						echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>";
					?>
					</form>
				
				
				
				<?php } elseif($section == 'topics' && isset($_GET['type']) && $_GET['type'] == 'edit'  && isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['hash'])) {
					if(!Tag::check_id_existance($db->escape_value($_GET['id']))) {
						redirect_to($url_mapper['error/404/']);
					}
					$topic = Tag::get_specific_id($db->escape_value($_GET['id']));
					if($topic->avatar) {
						$img = File::get_specific_id($topic->avatar);
						$quser_avatar= WEB_LINK."assets/".$img->image_path();
						$quser_avatar_path = UPLOADPATH."/".$img->image_path();
						if (!file_exists($quser_avatar_path)) {
							$quser_avatar = WEB_LINK.'assets/img/topic.png';
						}
					} else {
						$quser_avatar = WEB_LINK.'assets/img/topic.png';
					}
				?>
				
				
				<form method="post" action="<?php echo $url_mapper['admin/']; ?>/" enctype="multipart/form-data">
			
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="name"><?php echo $lang['admin-topics-name']; ?></label>
									<input type="text" class="form-control" name="name" id="name" placeholder="Topic Name.." required value="<?php echo $topic->name; ?>">
								<br>
								<label class="control-label" for="img1_upl"><?php echo $lang['admin-topics-avatar']; ?></label>
								<div class="controls">
									
									<img src="<?php echo $quser_avatar; ?>" class="img-polaroid img-circle" style="float:<?php echo $lang['direction-left']; ?>; padding:5px; margin-<?php echo $lang['direction-right']; ?>:10px; width:64px; height:64px" id="img1">
									<div style="height:64px; padding-top: 12px;width:200px;float:<?php echo $lang['direction-left']; ?>">
										<input class="text-input " type="file" name="upload_files[]" id="img1_upl"/><br/>
									</div>
								
								</div>
								
							</div>
							
						</div>
						<div class="col-md-6">
							<div class="form-group">
								
								<label for="description"><?php echo $lang['admin-topics-description']; ?></label>
								<textarea class="form-control" rows='5' name="description" id="description" placeholder="Topic Description.."><?php echo strip_tags($topic->description); ?></textarea>
								<br>
								
								
								</div>
						</div>
						
					</div>
					
							<center>
								<input class="btn btn-success" type="submit" name="edit_topic" value="<?php echo $lang['btn-submit']; ?>">
							</center>
						
					<?php 
						$_SESSION[$elhash] = $random_hash;
						echo "<input type=\"hidden\" name=\"edit_id\" value=\"".$topic->id."\" readonly/>";
						echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>";
					?>
					</form>
				
				
				
				
				<?php
				} elseif($section == 'topics' && isset($_GET['type']) && $_GET['type'] == 'delete'  && isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['hash'])) {
					$id = $db->escape_value($_GET['id']);
					
					if(!Tag::check_id_existance($id)) {
						redirect_to("{$url_mapper['error/404/']}");
					}
					
					$this_obj = Tag::get_specific_id($id);
					
					if(!$current_user->can_see_this("admintopics.delete",$group)) {
						$msg = $lang['alert-restricted'];
						redirect_to("{$url_mapper['admin/']}section=topics&edit=fail&msg={$msg}");
					}
					if($id == "1") {
						$msg = $lang['alert-restricted'];
						redirect_to("{$url_mapper['admin/']}section=topics&edit=fail&msg={$msg}");
					}
					
					//Check Subscriptions!!
					$tags = FollowRule::get_subscriptions('tag',$this_obj->id , 'obj_id' , '');
					if($tags) {
						foreach($tags as $tag) {
							$tag->delete();
						}
					}
					if($this_obj->delete()) {
						$msg = $lang['alert-delete_success'];
						if(isset($_GET['ref']) && $_GET['ref'] == 'index' ) {  
							redirect_to("{$url_mapper['index/']}&edit=success&msg={$msg}");
						} else {
							redirect_to("{$url_mapper['admin/']}section=topics&edit=success&msg={$msg}");
						}
					} else {
						$msg = $lang['alert-delete_failed'];
						redirect_to("{$url_mapper['admin/']}section=topics&edit=fail&msg={$msg}");
					}
					
					
				} else { ?>
				
					<table class="table table-hover table-bordered">
                      <thead>
                        <tr>
                          <th style='width:10px'>#</th>
                          <th><?php echo $lang['admin-topics-name']; ?></th><th><?php echo $lang['admin-topics-description']; ?></th>
						  <th style='width:150px'><i class="fa fa-wrench"></i></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
							
							if (isset($_GET['per_page']) && is_numeric($_GET['per_page']) ) {
								$per_page= $_GET['per_page'];
							} else {
								$per_page=20;
							}
							
							if (isset($_GET['page']) && is_numeric($_GET['page']) ) {
								$page= $_GET['page'];
							} else {
								$page=1;
							}
							
							
							$query = '';
							if(isset($_GET['search']) && $_GET['search'] == true && isset($_GET['data']) ) {
								$query = " AND (name LIKE '%" . $db->escape_value($_GET['data']) .  "%' OR description LIKE '%" . $db->escape_value($_GET['data']) .  "%') ";
							}
							
							$total_count = Tag::count_everything(" AND deleted = 0 {$query} ");
							$pagination = new Pagination($page, $per_page, $total_count);
							$all_obj= Tag::get_everything(" AND deleted = 0 {$query} "," LIMIT {$per_page} OFFSET {$pagination->offset()} ");
						
							
							$i= (($page-1) * $per_page) + 1;
							
							
							foreach($all_obj as $obj) :
							
							if($obj->avatar) {
								$pic = "<a href=\"#modal-image-{$obj->id}\" data-toggle='modal' class='btn btn-sm btn-icon btn-rounded btn-warning' data-rel='' data-placement='top' title='Avatar' data-original-title='Avatar'><i class='fa fa-search'></i></a>";
							} else {
								$pic = "";
							}
							
							if($current_user->can_see_this("admintopics.update",$group)) {
								$edit = "<a href=\"{$url_mapper['admin/']}/&section=topics&id={$obj->id}&type=edit&hash={$random_hash}\" class='btn btn-sm btn-icon btn-rounded btn-primary' data-rel='' data-placement='top' title='Edit' data-original-title='Edit'  ><i class='fa fa-pencil'></i></a>";
							} else {
								$edit = "<a href=\"#me\" class='btn btn-sm btn-icon btn-rounded btn-default' data-rel='' data-placement='top' title='Edit (unavailable)' data-original-title='Edit (unavailable)'  ><i class='fa fa-pencil'></i></a>";
							}
							
							if($current_user->can_see_this("admintopics.delete",$group)) {
								$delete = "<a href=\"{$url_mapper['admin/']}/&section=topics&id={$obj->id}&type=delete&hash={$random_hash}\" class='btn btn-sm btn-icon btn-rounded btn-danger' data-rel='' data-placement='top' title='delete' data-original-title='delete'   onclick=\"return confirm('Are you sure you want to delete this topic?');\" ><i class='fa fa-times'></i></a>";
							} else {
								$delete = "<a href=\"#me\" class='btn btn-sm btn-icon btn-rounded btn-default' data-rel='' data-placement='top' title='delete (unavailable)' data-original-title='delete (unavailable)'  ><i class='fa fa-times'></i></a>";
							}
							
							
						?>
						<tr >
                          <td><?php echo $i; ?></td>
                          <td><?php echo $obj->name;?></td>
						  <td><?php echo $obj->description; ?></td>
						  <td><div class="btn-group"><?php echo $pic .$edit . $delete; ?></div></td>
						  
						<?php 
							if(isset($obj->avatar) && $obj->avatar) {
								$img = File::get_specific_id($obj->avatar);
								$link = UPL_FILES."/".$img->image_path();
						?>
						<div class="modal fade modal-image" id="modal-image-<?php echo $obj->id; ?>" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
							<div class="modal-dialog">
							  <div class="modal-content">
								<div class="modal-header">
								  <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
								</div>
								<div class="modal-body">
								  <img src="<?php echo $link; ?>" alt="picture 1" class="img-responsive">
								</div>
								<div class="modal-footer">
								  <p><?php echo $obj->name; ?></p>
								</div>
							  </div>
							</div>
						  </div>
						<?php 
							}
						?>
                        </tr>
                        
						<?php 
							$i++;
							endforeach;
						?>
                      </tbody>
                    </table>
					
					<?php
					
					if(isset($pagination) && $pagination->total_pages() > 1) {
					?>
					<div class="pagination btn-group">
					
							<?php
							if ($pagination->has_previous_page()) {
								$page_param = $url_mapper['admin/'];
								$page_param .= "&section=topics&page=";
								$page_param .= $pagination->previous_page();

							echo "<a href=\"{$page_param}\" class=\"btn btn-default\" type=\"button\"><i class=\"glyphicon glyphicon-chevron-{$lang['direction-left']}\"></i></a>";
							} else {
							?>
							<a class="btn btn-default" type="button"><i class="glyphicon glyphicon-chevron-<?php echo $lang['direction-left']; ?>"></i></a>
							<?php
							}
							
							for($p=1; $p <= $pagination->total_pages(); $p++) {
								if($p == $page) {
									echo "<a class=\"btn btn-default active\" type=\"button\">{$p}</a>";
								} else {
									$page_param = $url_mapper['admin/'];
									$page_param .= "&section=topics&page=";
									$page_param .= $p;

									echo "<a href=\"{$page_param}\" class=\"btn btn-default\" type=\"button\">{$p}</a>";
								}
							}
							if($pagination->has_next_page()) {
								$page_param = $url_mapper['admin/'];
								$page_param .= "&section=topics&page=";
								$page_param .= $pagination->next_page();

							echo " <a href=\"{$page_param}\" class=\"btn btn-default\" type=\"button\"><i class=\"glyphicon glyphicon-chevron-{$lang['direction-right']}\"></i></a> ";
							} else {
							?>
							<a class="btn btn-default" type="button"><i class="glyphicon glyphicon-chevron-<?php echo $lang['direction-right']; ?>"></i></a>
							<?php
							}
							?>
					
					</div>
					<?php
					}
					?>
				
				
				<?php } ?>
				
				
			</div>
			<?php } ?>
			<?php if($current_user->can_see_this('admanager.read' , $group)) { ?>
			<div id="admanager" class="tab-pane fade <?php if($section == 'admanager') { echo 'in active'; } ?>">
				
				<form method="post" action="<?php echo $url_mapper['admin/']; ?>">
			
				<h3 class="page-header"><?php echo $lang['admin-admanager-title']; ?></h3>
				
					<div class="row">
						
						<ul class="nav nav-tabs">
						  <li class="active"><a data-toggle="tab" href="#between-q" href="#me"><?php echo $lang['admin-admanager-between_q']; ?></a></li>
						  <li><a data-toggle="tab" href="#between-a" href="#me" ><?php echo $lang['admin-admanager-between_a']; ?></a></li>
						  <li><a data-toggle="tab" href="#lt-sidebar" href="#me"><?php echo $lang['admin-admanager-lt_sidebar']; ?></a></li>
						  <li><a data-toggle="tab" href="#rt-sidebar" href="#me"><?php echo $lang['admin-admanager-rt_sidebar']; ?></a></li>
						</ul>
						<div class="tab-content">
							<div id="between-q" class="tab-pane fade in active">
								<br><p>Adblock Type:
									<div class="btn-group"><a href="#me" class="toggle-code btn btn-default "><i class='fa fa-desktop'></i> Other</a>
									<a href="#me" class="untoggle-code btn btn-default active"><i class='fa fa-code'></i> Google Adsense</a></div>
								</p>
								
								<textarea class="custom-summernote form-control" name="between-q" rows="5" placeholder=""><?php echo $admanager1->value; ?></textarea>
								<br><br>
							</div>
							<div id="between-a" class="tab-pane fade in ">
								<br><p>Adblock Type:
									<div class="btn-group"><a href="#me" class="toggle-code2 btn btn-default "><i class='fa fa-desktop'></i> Other</a>
									<a href="#me" class="untoggle-code2 btn btn-default active"><i class='fa fa-code'></i> Google Adsense</a></div>
								</p>
								
								<textarea class="custom-summernote2 form-control" name="between-a" rows="5"><?php echo $admanager1->msg; ?></textarea>
								<br><br>
							</div>
							<div id="lt-sidebar" class="tab-pane fade in ">
								<br><p>Adblock Type:
									<div class="btn-group"><a href="#me" class="toggle-code3 btn btn-default "><i class='fa fa-desktop'></i> Other</a>
									<a href="#me" class="untoggle-code3 btn btn-default active"><i class='fa fa-code'></i> Google Adsense</a></div>
								</p>
								
								<textarea class="custom-summernote3 form-control" name="lt-sidebar" rows="5"><?php echo $admanager2->value; ?></textarea>
								<br><br>
							</div>
							<div id="rt-sidebar" class="tab-pane fade in ">
								<br><p>Adblock Type:
									<div class="btn-group"><a href="#me" class="toggle-code4 btn btn-default "><i class='fa fa-desktop'></i> Other</a>
									<a href="#me" class="untoggle-code4 btn btn-default active"><i class='fa fa-code'></i> Google Adsense</a></div>
								</p>
								
								<textarea class="custom-summernote4 form-control" name="rt-sidebar" rows="5"><?php echo $admanager2->msg; ?></textarea>
								<br><br>
							</div>
							
						</div>
						
					</div>
					
							<center>
								<input class="btn btn-success" type="submit" name="edit_adblocks" value="<?php echo $lang['btn-submit']; ?>">
							</center>
						
					<?php 
						$_SESSION[$elhash] = $random_hash;
						echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>";
					?>
					</form>
				
				
			</div>
			
			<?php } ?>
			<?php if($current_user->can_see_this('groups.read' , $group)) { ?>
			<div id="groups" class="tab-pane fade <?php if($section == 'groups') { echo 'in active'; } ?>">
				<h3><?php echo $lang['admin-groups-title']; ?>&nbsp;&nbsp;<a href='<?php echo $url_mapper['admin/'] . 'section=groups&type=new'; ?>' class="btn btn-sm btn-primary"><i class='fa fa-plus'></i> <?php echo $lang['btn-add']; ?></a></h3>
				
				<?php if($section == 'groups' && isset($_GET['type']) && $_GET['type'] == 'new' ) {
				?>
				
				<form id="form-validation" action="user_groups.php" method="post" class="">
						
						<div class="col-md-6">
						  <div class="form-group">
							<label for="name" class="control-label"><?php echo $lang['admin-groups-name']; ?></label>
							<input type="text" class="form-control form-white" id="name" name="name" placeholder="Name" required >
						  </div>
						</div>
						
						<br><br>
						
						<br style="clear:both"/>
						
						<div class="row">
							
							<?php 
							foreach($sections as $section_title => $section_privileges) {
							?>
							<div class="col-md-6">
							  <div class="panel panel-default">
								<div class="panel-heading">
								  <h3 class="panel-title"><?php 
									$parent_data = explode('|' , $section_title);
								  echo '<input type="checkbox" class="liParent" name="privileges[]" value="'.$parent_data[0].'" />' . $parent_data[1]; ?></h3>
								</div>
								<div class="panel-body">
									
									<ul class="privileges_menu" >
										<?php 
											foreach($section_privileges as $parent => $child) {
												$parent_data = explode('|' , $parent);
												echo '<li><input type="checkbox" class="liParent" name="privileges[]" value="'.$parent_data[0].'" />'.$parent_data[1];
												if(is_array($child) && !empty($child) ) {	//Has submenu!
													echo '<ul>';
													foreach($child as $grandchild) {
														$grandchild_data = explode('|' , $grandchild);
														echo '<li><input type="checkbox" class="liChild" name="privileges[]" value="'.$grandchild_data[0].'" />'.$grandchild_data[1] .'</li>';
													}
													echo '</ul>';
												}
												echo '</li>';
											}
										?>
									</ul>
									
								</div>
							  </div>
							</div>
							<?php } ?>
						
						
						</div>
					
					<?php	
					echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>"				
					?>
					
					<center>
						<button class="btn btn-success" name="add_group" type="submit" ><?php echo $lang['btn-submit']; ?></button>
						<a href='<?php echo $url_mapper['admin/'] . 'section=groups'; ?>' class="btn btn-danger" ><?php echo $lang['btn-cancel']; ?></a>
					</center>		
					
					</form>
				<?php
				} elseif($section == 'groups' && isset($_GET['type']) && $_GET['type'] == 'edit'  && isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['hash'])) {
					if(!Group::check_id_existance($db->escape_value($_GET['id']))) {
						redirect_to($url_mapper['error/404/']);
					}
					$this_obj = Group::get_specific_id($db->escape_value($_GET['id']));
				?>
				<form id="form-validation" action="<?php echo $url_mapper['admin/']; ?>" method="post" class="">
						
						<div class="col-md-6">
						  <div class="form-group">
							<label for="name" class="control-label"><?php echo $lang['admin-groups-name']; ?></label>
							<input type="text" class="form-control form-white" id="name" name="name" placeholder="Name" value="<?php echo $this_obj->name; ?>" required >
						  </div>
						</div>
						<br><br>
						
						<br style="clear:both"/>
						
						<div class="row">
							
							<?php 
							foreach($sections as $section_title => $section_privileges) {
							?>
							<div class="col-md-6">
							  <div class="panel panel-default">
								<div class="panel-heading">
								  <h3 class="panel-title"><?php 
									$parent_data = explode('|' , $section_title);
								  echo '<input type="checkbox" class="liParent" name="privileges[]" ';  if($current_user->can_see_this( $parent_data[0] ,$this_obj->id)) { echo "checked=\"checked\""; }  echo '  value="'.$parent_data[0].'" />' . $parent_data[1]; ?></h3>
								</div>
								<div class="panel-body">
									
									<ul class="privileges_menu" >
										<?php 
											foreach($section_privileges as $parent => $child) {
												$parent_data = explode('|' , $parent);
												echo '<li><input type="checkbox" class="liParent" name="privileges[]" ';  if($current_user->can_see_this( $parent_data[0] ,$this_obj->id)) { echo "checked=\"checked\""; } echo ' value="'.$parent_data[0].'" />'.$parent_data[1];
												if(is_array($child) && !empty($child) ) {	//Has submenu!
													echo '<ul>';
													foreach($child as $grandchild) {
														$grandchild_data = explode('|' , $grandchild);
														echo '<li><input type="checkbox" class="liChild" name="privileges[]" ';  if($current_user->can_see_this( $grandchild_data[0] ,$this_obj->id)) { echo "checked=\"checked\""; } echo ' value="'.$grandchild_data[0].'" />'.$grandchild_data[1] .'</li>';
													}
													echo '</ul>';
												}
												echo '</li>';
											}
										?>
									</ul>
									
								</div>
							  </div>
							</div>
							<?php } ?>
						
						
						</div>
					
					<?php	
					echo "<input type=\"hidden\" name=\"edit_id\" value=\"".$this_obj->id."\" readonly/>";
					echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>"	;
					?>
					
					<center>
						<button class="btn btn-success" name="edit_group" type="submit" ><?php echo $lang['btn-submit']; ?></button>
						<a href='<?php echo $url_mapper['admin/'] . 'section=groups'; ?>' class="btn btn-danger" ><?php echo $lang['btn-cancel']; ?></a>
					</center>		
					
					</form>
				<?php
				} elseif($section == 'groups' && isset($_GET['type']) && $_GET['type'] == 'delete'  && isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['hash'])) {
					$id = $db->escape_value($_GET['id']);
					
					if(!Group::check_id_existance($id)) {
						redirect_to("{$url_mapper['error/404/']}");
					}
					
					$this_obj = Group::get_specific_id($id);
					
					if(!$current_user->can_see_this("groups.delete",$group)) {
						$msg = $lang['alert-restricted'];
						redirect_to("{$url_mapper['admin/']}section=groups&edit=fail&msg={$msg}");
					}
					if($id <= "3") {
						$msg = $lang['alert-restricted'];
						redirect_to("{$url_mapper['admin/']}section=groups&edit=fail&msg={$msg}");
					}
					$this_obj->deleted = 1;
					if($this_obj->update()) {
						$msg = $lang['alert-delete_success'];
						//Log::log_action($current_user->id , "Delete Group object" , "Delete Group object named ({$this_obj->name}) - id #({$this_obj->id})");
						redirect_to("{$url_mapper['admin/']}section=groups&edit=success&msg={$msg}");
					} else {
						$msg = $lang['alert-delete_failed'];
						redirect_to("{$url_mapper['admin/']}section=groups&edit=fail&msg={$msg}");
					}
					
					
				} else { ?>
				
				
				<table class="table table-hover table-bordered custom_table">
                      <thead>
                        <tr>
                          <th style='width:10px'>#</th>
                          <th><?php echo $lang['admin-groups-name']; ?></th><th><?php echo $lang['admin-groups-users']; ?></th>
                          <th style='width:150px'><i class="fa fa-wrench"></i></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
							
							$query = ' AND deleted = 0 ';
							
							$all_obj= Group::get_everything($query," ");
							
							$i= 1;
							foreach($all_obj as $obj) :
							
							if($current_user->can_see_this("groups.update",$group)) {
								$edit = "<a href=\"{$url_mapper['admin/']}section=groups&id={$obj->id}&type=edit&hash={$random_hash}\" class='btn btn-sm btn-icon btn-rounded btn-primary' data-toggle='tooltip' data-placement='top' title='Edit' data-original-title='Edit'  ><i class='fa fa-pencil'></i></a>";
							} else {
								$edit = "<a href=\"#me\" class='btn btn-sm btn-icon btn-rounded btn-default' data-toggle='tooltip' data-placement='top' title='Edit (Unavailable)' data-original-title='Edit (Unavailable)'  ><i class='fa fa-pencil'></i></a>";
							}
							
							if($current_user->can_see_this("groups.delete",$group)) {
								$delete = "<a href=\"{$url_mapper['admin/']}section=groups&id={$obj->id}&type=delete&hash={$random_hash}\" class='btn btn-sm btn-icon btn-rounded btn-danger' data-toggle='tooltip' data-placement='top' title='Delete' data-original-title='Delete'   onclick=\"return confirm('Are you sure you want to delete this group?');\" ><i class='fa fa-times'></i></a>";
							} else {
								$delete = "<a href=\"#me\" class='btn btn-sm btn-icon btn-rounded btn-default' data-toggle='tooltip' data-placement='top' title='Delete (Unavailable)' data-original-title='Delete (Unavailable)'  ><i class='fa fa-times'></i></a>";
							}
							
							if($obj->id == '1') {
								$delete = '';
							}
							
							$related_users = User::get_users_for_group($obj->id);
								$names = array();
								foreach ($related_users as $user ) {
									$names[] = $user->f_name . ' ' . $user->l_name;
								}
								
								if (!empty($names) ) { $names_string = implode(" - " , $names); } else { $names_string = "None"; }

								$count = "<span data-toggle=\"tooltip\" style=\"cursor:pointer\" data-rel=\"tooltip\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"{$names_string}	\" data-original-title=\"{$names_string}\">".count($related_users)."</span>";
						?>
						<tr>
                          <td><?php echo $i; ?></td>
                          <td><?php echo $obj->name; ?></td><td><?php echo $count; ?></td>
                          <td><div class="btn-group"><?php echo $edit . " " . $delete; ?></div></td>
						  
						
                        </tr>
                        
						<?php 
							$i++;
							endforeach;
						?>
                      </tbody>
                    </table>
				
				<?php } ?>
				
				
			</div>	
			<?php } ?>
			
		</div>
	
		
	</div>
	
</div>
	<?php require_once('assets/includes/footer.php'); ?>
    </div> <!-- /container -->
    <?php require_once('assets/includes/preloader.php'); ?>
	<script src='https://www.google.com/recaptcha/api.js'></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
	<script src="<?php echo WEB_LINK; ?>assets/plugins/summernote/summernote.js"></script>
	<script src="<?php echo WEB_LINK; ?>assets/plugins/charts-chartjs/Chart.min.js"></script>  <!-- ChartJS Chart -->	
	<script>
    $(document).ready(function() {
		
		/**** Line Charts: ChartJs ****/
      var lineChartData = {
        <?php 
		$months = array();
		$numbers = array();
		for($i = 0; $i < 21 ; $i++) {
			$months[] = strftime("%d.%m" , strtotime("-{$i} Day" , time())); 
			$numbers[] = Question::count_everything(' AND DATE_FORMAT(created_at, "%d-%m-%Y") = "' . strftime("%d-%m-%Y" , strtotime("-{$i} Day" , time())) . '" ');
		}
		
		?>
		labels : [<?php echo '"' . implode('","' , $months) . '"'; ?>],
        datasets : [
          {
            label: "Questions",
            fillColor : "rgba(49, 157, 181,0.2)",
            strokeColor : "#319DB5",
            pointColor : "#319DB5",
            pointStrokeColor : "#fff",
            pointHighlightFill : "#fff",
            pointHighlightStroke : "#319DB5",
            data : [<?php echo '"' . implode('","' , $numbers) . '"'; ?>]
          }
        ]
      }
      var ctx = document.getElementById("questions").getContext("2d");
      window.myLine = new Chart(ctx).Line(lineChartData, {
        responsive: true,
        tooltipCornerRadius: 0
      });
	
		/**** Line Charts: ChartJs ****/
      var lineChartData = {
        <?php 
		$months = array();
		$numbers = array();
		for($i = 0; $i < 21 ; $i++) {
			$months[] = strftime("%d.%m" , strtotime("-{$i} Day" , time())); 
			$numbers[] = Answer::count_everything(' AND DATE_FORMAT(created_at, "%d-%m-%Y") = "' . strftime("%d-%m-%Y" , strtotime("-{$i} Day" , time())) . '" ');
		}
		
		?>
		labels : [<?php echo '"' . implode('","' , $months) . '"'; ?>],
        datasets : [
          {
            label: "Answers",
            fillColor : "rgba(49, 157, 181,0.2)",
            strokeColor : "#319DB5",
            pointColor : "#319DB5",
            pointStrokeColor : "#fff",
            pointHighlightFill : "#fff",
            pointHighlightStroke : "#319DB5",
            data : [<?php echo '"' . implode('","' , $numbers) . '"'; ?>]
          }
        ]
      }
      var ctx = document.getElementById("answers").getContext("2d");
      window.myLine = new Chart(ctx).Line(lineChartData, {
        responsive: true,
        tooltipCornerRadius: 0
      });
	
		
		
		/**** Line Charts: ChartJs ****/
      var lineChartData = {
        <?php 
		$months = array();
		$numbers = array();
		for($i = 0; $i < 21 ; $i++) {
			$months[] = strftime("%d.%m" , strtotime("-{$i} Day" , time())); 
			$numbers[] = User::count_everything(' AND DATE_FORMAT(joined, "%d-%m-%Y") = "' . strftime("%d-%m-%Y" , strtotime("-{$i} Day" , time())) . '" ');
		}
		
		?>
		labels : [<?php echo '"' . implode('","' , $months) . '"'; ?>],
        datasets : [
          {
            label: "New Registrations",
            fillColor : "rgba(49, 157, 181,0.2)",
            strokeColor : "#319DB5",
            pointColor : "#319DB5",
            pointStrokeColor : "#fff",
            pointHighlightFill : "#fff",
            pointHighlightStroke : "#319DB5",
            data : [<?php echo '"' . implode('","' , $numbers) . '"'; ?>]
          }
        ]
      }
      var ctx = document.getElementById("user-registration").getContext("2d");
      window.myLine = new Chart(ctx).Line(lineChartData, {
        responsive: true,
        tooltipCornerRadius: 0
      });
	
		
		
		
		$('select').select2({  minimumResultsForSearch: Infinity });
		$('.custom_table').DataTable({
			"dom" : "lrtip"
		});
		
		$('.summernote').summernote({
			height: 400,
			callbacks : {
	            onImageUpload: function(image) {
					sendFile($(this), image[0]);
				}
			}
        });
		
		function sendFile(obj, image) {
            data = new FormData();
            data.append("data", 'summernote-inline-uploader');
            data.append("id", <?php echo $current_user->id; ?>);
            data.append("hash", '<?php echo $random_hash; ?>');
            data.append("img", image);
            $.ajax({
                data: data,
                type: "POST",
                url: "<?php echo WEB_LINK ?>assets/includes/one_ajax.php?type=upl_img",
                cache: false,
                contentType: false,
                processData: false,
                success: function(url) {
                    obj.summernote("insertImage", url);
				},
				error: function(data) {
					console.log(data);
				}
            });
        }
		$('select').select2();
		
		
	});
	
	
$('.approve-machine').on('click' , '.approve-item' , function() {
	var id = $(this).data('id');
	$(this).parent().parent().parent().hide();
	$.post("<?php echo WEB_LINK; ?>assets/includes/one_ajax.php?type=approve", {id:id, data: $(this).data('obj') , hash:'<?php echo $random_hash; ?>'}, function(){});
});
$('.approve-machine').on('click' , '.reject-item' , function() {
	var id = $(this).data('id');
	$(this).parent().parent().parent().hide();
	$.post("<?php echo WEB_LINK; ?>assets/includes/one_ajax.php?type=reject", {id:id, data: $(this).data('obj') , hash:'<?php echo $random_hash; ?>'}, function(){});
});

$('.approve-machine').on('click' , '.approve-report-item' , function() {
	var id = $(this).data('id');
	if(confirm('<?php echo $lang['admin-reports-approve_report-alert']; ?>')) {
		$(this).parent().parent().parent().hide();
		$.post("<?php echo WEB_LINK; ?>assets/includes/one_ajax.php?type=approve-report", {id:id, data: $(this).data('obj') , report_id: $(this).data('report_id'),  hash:'<?php echo $random_hash; ?>'}, function(){});
	}
});
$('.approve-machine').on('click' , '.reject-report-item' , function() {
	var id = $(this).data('id');
	if(confirm('<?php echo $lang['admin-reports-reject_report-alert']; ?>')) {
		$(this).parent().parent().parent().hide();
		$.post("<?php echo WEB_LINK; ?>assets/includes/one_ajax.php?type=reject-report", {id:id, data: $(this).data('obj') , report_id: $(this).data('report_id') , hash:'<?php echo $random_hash; ?>'}, function(){});
	}
});

$('.toggle-code').click(function() {
	$('.custom-summernote').summernote({
			callbacks : {
	            onImageUpload: function(image) {
					sendFile(image[0] , $(this).attr("class").split(' ')[0]);
				}
			}
        });
	$('.toggle-code').toggleClass('active');
	$('.untoggle-code').toggleClass('active');
});
$('.untoggle-code').click(function() {
	$('.custom-summernote').summernote('destroy');
	$('.toggle-code').toggleClass('active');
	$('.untoggle-code').toggleClass('active');
});

$('.toggle-code2').click(function() {
	$('.custom-summernote2').summernote({
			callbacks : {
	            onImageUpload: function(image) {
					sendFile(image[0] , $(this).attr("class").split(' ')[0]);
				}
			}
        });
	$('.toggle-code2').toggleClass('active');
	$('.untoggle-code2').toggleClass('active');
});
$('.untoggle-code2').click(function() {
	$('.custom-summernote2').summernote('destroy');
	$('.toggle-code2').toggleClass('active');
	$('.untoggle-code2').toggleClass('active');
});

$('.toggle-code3').click(function() {
	$('.custom-summernote3').summernote({
			callbacks : {
	            onImageUpload: function(image) {
					sendFile(image[0] , $(this).attr("class").split(' ')[0]);
				}
			}
        });
	$('.toggle-code3').toggleClass('active');
	$('.untoggle-code3').toggleClass('active');
});
$('.untoggle-code3').click(function() {
	$('.custom-summernote3').summernote('destroy');
	$('.toggle-code3').toggleClass('active');
	$('.untoggle-code3').toggleClass('active');
});

$('.toggle-code4').click(function() {
	$('.custom-summernote4').summernote({
			callbacks : {
	            onImageUpload: function(image) {
					sendFile(image[0] , $(this).attr("class").split(' ')[0]);
				}
			}
        });
	$('.toggle-code4').toggleClass('active');
	$('.untoggle-code4').toggleClass('active');
});
$('.untoggle-code4').click(function() {
	$('.custom-summernote4').summernote('destroy');
	$('.toggle-code4').toggleClass('active');
	$('.untoggle-code4').toggleClass('active');
});

$('<div id="loading_wrap"><div class="com_loading"><center><img src="<?php echo WEB_LINK; ?>assets/img/loading.gif" /> Loading ...</center></div></div>').appendTo('body');
function sendFile(image,obj_class) {
	$("#loading_wrap").fadeIn("fast");

	data = new FormData();
	data.append("data", 'summernote-inline-uploader');
	data.append("id", <?php echo $current_user->id; ?>);
	data.append("hash", '<?php echo $random_hash; ?>');
	data.append("img", image);
	$.ajax({
		data: data,
		type: "POST",
		url: "<?php echo WEB_LINK ?>assets/includes/one_ajax.php?type=upl_img",
		cache: false,
		contentType: false,
		processData: false,
		success: function(url) {
			$("." + obj_class).summernote("insertImage", url);
			$("#loading_wrap").fadeOut("fast");
		},
		error: function(data) {
			console.log(data);
		}
	});
}




	$(function () {
	  $('[data-toggle="tooltip"]').tooltip()
	})
	
	function readURL(input,targetid) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function (e) {
				$("#" + targetid).attr('src', e.target.result);
			}

			reader.readAsDataURL(input.files[0]);
		}
	}

	$("#img1_upl").change(function(){
		readURL(this, 'img1');
	});

	
	</script>
	
	
<?php require_once('assets/includes/bottom.php'); ?>