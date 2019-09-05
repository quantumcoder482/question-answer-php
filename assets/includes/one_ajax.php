<?php
require_once("route.php");
if ($session->is_logged_in() != true ) {
	if ($settings['public_access'] == '1') {
		$current_user = User::get_specific_id(1000);
	} else {
		redirect_to($url_mapper['login/']); 
	}
} else {
	$current_user = User::get_specific_id($session->admin_id);
}

$group = $current_user->prvlg_group;
if(!isset($settings['site_lang'])) { $settings['site_lang'] = 'English'; }
require_once($current."/lang/lang.{$settings['site_lang']}.php");

if (isset($_GET['type']) && !empty($_GET['type']) && isset($_POST['hash']) && !empty($_POST['hash']) && isset($_POST['data']) && !empty($_POST['data']) && isset($_POST['id']) && is_numeric($_POST['id'])) {
	$id = $db->escape_value($_POST['id']);
	$type = $db->escape_value($_GET['type']);
	$hash = $db->escape_value($_POST['hash']);
	$data = $db->escape_value($_POST['data']);
	
	switch($type) {
		###############################################################
		case 'mention' :
			
			$result = User::find_username( $data , $current_user->id, " LIMIT 5");
			$return = Array();
			foreach($result as $r) {
				$e = array();
				$e['name'] = $r->username;
				$e['link'] =  $url_mapper['users/view']. $r->id .'/';
				
				array_push($return, $e);
			}
			
			if(!empty($return)) {$json = json_encode($return);
				echo $json;
			} else { return false; }
			
		break;
		###############################################################
		case 'check_notifications' :
		
			$notif = Notif::count_everything(" AND user_id = '{$current_user->id}' AND viewed = 0 ");
			$return = Array("count" => false, "menu" => false);
			if($notif) { //Send Count & Menu
				$return['count'] = $notif;
				
				$notifications = Notif::get_everything(" AND user_id = '{$current_user->id}' AND viewed = 0 ORDER BY created_at DESC LIMIT 10 ");
				$menu = array();
				
				foreach($notifications as $n ) {
					$string = str_replace('\\' , '' , $n->msg);
					
					/*if (strlen($string) > 50) {
						$stringCut = substr($string, 0, 50);
						$string = substr($stringCut, 0, strrpos($stringCut, ' '))."..."; 
					}*/
					$link = $n->link;
					if(strpos($link , '#')) {	//There's a hash!
						$linkarr = explode('#' , $link);
						$link = $linkarr[0] . "&notif={$n->id}#" . $linkarr[1];
					} else {
						$link .= "&notif={$n->id}";
					}
					
					$e = array(
						'string' => $string,
						'link' => $link
					);
					array_push($menu, $e);
				}
				
				$return['menu'] = $menu;
			}
			
			$json = json_encode($return);
			echo $json;
			
		break;
		###############################################################
		case 'follow' :
		
			$classname = ucfirst($data);
			$found = $classname::get_specific_id($id);
			
			if($found) {
				
				//Check prev like..
				$prev_follow = FollowRule::get_for_obj($data , $id, $current_user->id);
				if(!$prev_follow) {
					//Create like..
					$like = New FollowRule();
					$like->user_id = $current_user->id;
					$like->obj_id = $id;
					$like->obj_type = $data;
					$like->follow_date = strftime("%Y-%m-%d %H:%M:%S", time());
					$like->create();
					
					###############
					## FOLLOW NOTIF ##
					###############
					
					if($classname == 'User') {
						$notif_link = $url_mapper['users/view']. $current_user->id.'/';
						$str = $lang['notif-user-follow']; $str = str_replace('[NAME]' , $current_user->f_name , $str); $str = str_replace('[LINK]' , $url_mapper['users/view'] . $current_user->id , $str); 
						$notif_msg = $str;
						$notif_user = $id;
						$receiver = User::get_specific_id($notif_user);
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						$award = Award::send_award($notif_user,$notif_msg . ", {$lang['notif-award']} <b>1</b> {$lang['notif-point']}" );
						$receiver->award_points(1);
						##########
						## MAILER ##
						##########
						$msg = $notif_msg . "<br>Check it out at ". $notif_link;
						$title = 'New Follow';
						if($receiver && is_object($receiver)) {
							Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
						}
					} elseif($classname == 'Question') {
						$receiver = User::get_specific_id($found->user_id);
						if(URLTYPE == 'slug') {
							$url_type = $found->slug;
						} else {
							$url_type = $found->id;
						}
						$str= $lang['notif-q_f_award']; $str = str_replace('[LINK]' , $url_mapper['users/view'].$current_user->id , $str ); $str = str_replace('[Q_LINK]' , $url_mapper['questions/view'] . $url_type, $str ); $str = str_replace('[NAME]' , $current_user->f_name , $str );
						$award = Award::send_award($id, "{$str} , {$lang['notif-award']} <b>1</b> {$lang['notif-point']}" );
						$receiver->award_points(1);
						
						$notif_link = $url_mapper['users/view']. $current_user->id.'/';
						$notif_msg = $str;
						$notif_user = $found->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						##########
						## MAILER ##
						##########
						$msg = $notif_msg . "<br>Check it out at ". $notif_link;
						$title = 'New Follow';
						if($receiver && is_object($receiver)) {
							Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
						}
						
					}
					$found->follows +=1;
					$found->update();
				}
			}
		break;
		
		case 'unfollow' :
			$classname = ucfirst($data);
			$found = $classname::get_specific_id($id);
			if($found) {
				//Check prev like..
				$prev_likes = FollowRule::get_for_obj($data , $id, $current_user->id);
				if($prev_likes) {
					$prev_likes->delete();
					$found->follows -=1;
					$found->update();
				}
			}
		break;
		
		###############################################################
		case 'like' :
			$classname = ucfirst($data);
			$found = $classname::get_specific_id($id);
			if($found) {
				//Check prev like..
				$prev_likes = LikeRule::get_for_obj($data , "like" , $id, $current_user->id);
				if(!$prev_likes) {
					//Create like..
					$like = New LikeRule();
					$like->user_id = $current_user->id;
					$like->obj_id = $id;
					$like->obj_type = $data;
					$like->type = 'like';
					$like->like_date = strftime("%Y-%m-%d %H:%M:%S", time());
					$like->create();
					
					if($classname == 'Question') {
						if(URLTYPE == 'slug') {
							$url_type = $found->slug;
						} else {
							$url_type = $found->id;
						}
						$receiver = User::get_specific_id($found->user_id);
						$str= $lang['notif-q_l_award']; $str = str_replace('[LINK]' , $url_mapper['users/view'].$current_user->id , $str ); $str = str_replace('[Q_LINK]' , $url_mapper['questions/view'] . $url_type, $str ); $str = str_replace('[NAME]' , $current_user->f_name , $str );
						$award = Award::send_award($found->user_id,"{$str} , {$lang['notif-award']} <b>1</b> {$lang['notif-point']}" );
						$receiver->award_points(1);
						
						#######
						# NOTIF #
						#######
						if(URLTYPE == 'slug') {$url_type = $found->slug;} else {$url_type = $found->id;}
						$notif_link = $url_mapper['questions/view'].$url_type;
						$notif_msg = $str;
						$notif_user = $found->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						
					} elseif($classname == 'Answer') {
						$q = Question::get_specific_id($found->q_id);
						if(URLTYPE == 'slug') {
							$url_type = $q->slug;
						} else {
							$url_type = $q->id;
						}
						$receiver = User::get_specific_id($found->user_id);
						$str= $lang['notif-a_l_award']; $str = str_replace('[LINK]' , $url_mapper['users/view'].$current_user->id , $str ); $str = str_replace('[Q_LINK]' , $url_mapper['questions/view'] . $url_type . "#answer-{$found->id}" , $str ); $str = str_replace('[NAME]' , $current_user->f_name , $str );
						$award = Award::send_award($found->user_id,"{$str} , {$lang['notif-award']} <b>1</b> {$lang['notif-point']}" );
						$receiver->award_points(1);
						
						#######
						# NOTIF #
						#######
						if(URLTYPE == 'slug') {$url_type = $found->slug;} else {$url_type = $found->id;}
						$notif_link = $url_mapper['questions/view'].$url_type .'#answer-'.$id;
						$notif_msg = $str;
						$notif_user = $found->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						
					}
					
					$found->likes +=1;
					$found->update();
					
				}
			}
		break;
		
		case 'unlike' :
			$classname = ucfirst($data);
			$found = $classname::get_specific_id($id);
			if($found) {
				//Check prev like..
				$prev_likes = LikeRule::get_for_obj($data , "like" , $id, $current_user->id);
				if($prev_likes) {
					//Create like..
					$prev_likes->delete();
					$found->likes -=1;
					$found->update();
				}
			}
		break;
		
		###############################################################
		case 'approve' :
			$classname = ucfirst($data);
			$found = $classname::get_specific_id($id);
			if($found) {
					
					###############
					## APPROVE NOTIF ##
					###############
					if($classname == 'Question') {
						if(URLTYPE == 'slug') {$url_type = $found->slug;} else {$url_type = $found->id;}
						$notif_link = $url_mapper['questions/view'].$url_type;
						$str = $lang['notif-q_publish']; $str = str_replace('[TITLE]' , $found->title , $str);
						$notif_msg = $str;
						$notif_user = $found->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						
						##########
						## MAILER ##
						##########
						$msg = $notif_msg . "<br>Check it out at ". $notif_link;
						$title = 'Question Approved';
						$receiver = User::get_specific_id($notif_user);
						if($receiver && is_object($receiver)) {
							Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
						}
					} elseif($classname == 'Answer') {
						$q = Question::get_specific_id($found->q_id);
						if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
						$notif_link = $url_mapper['questions/view'].$url_type.'#answer-'.$found->id;
						$str = $lang['notif-a_publish']; $str = str_replace('[TITLE]' , $q->title , $str);
						$notif_msg = $str;
						$notif_user = $found->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						##########
						## MAILER ##
						##########
						$msg = $notif_msg . "<br>Check it out at ". $notif_link;
						$title = 'Answer Approved';
						$receiver = User::get_specific_id($notif_user);
						if($receiver && is_object($receiver)) {
							Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
						}
					}
					
					$found->published =1;
					$found->update();
			}
		break;
		
		case 'reject' :
			$classname = ucfirst($data);
			$found = $classname::get_specific_id($id);
			if($found) {
					###############
					## APPROVE NOTIF ##
					###############
					if($classname == 'Question') {
						if(URLTYPE == 'slug') {$url_type = $found->slug;} else {$url_type = $found->id;}
						$notif_link = $url_mapper['questions/view'].$url_type;
						$str = $lang['notif-q_reject']; $str = str_replace('[TITLE]' , $found->title , $str);
						$notif_msg = $str;
						$notif_user = $found->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						##########
						## MAILER ##
						##########
						$msg = $notif_msg . "<br>Check it out at ". $notif_link;
						$title = 'Question Rejected';
						$receiver = User::get_specific_id($notif_user);
						if($receiver && is_object($receiver)) {
							Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
						}
					} elseif($classname == 'Answer') {
						$q = Question::get_specific_id($found->q_id);
						if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
						$notif_link = $url_mapper['questions/view'].$url_type.'#answer-'.$found->id;
						$str = $lang['notif-a_reject']; $str = str_replace('[TITLE]' , $q->title , $str);
						$notif_msg = $str;
						$notif_user = $found->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						##########
						## MAILER ##
						##########
						$msg = $notif_msg . "<br>Check it out at ". $notif_link;
						$title = 'Answer Rejected';
						$receiver = User::get_specific_id($notif_user);
						if($receiver && is_object($receiver)) {
							Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
						}
					}
					
					$found->delete();
			}
		break;
		
		###############################################################
		case 'approve-report' :
			$classname = ucfirst($data);
			$report = Report::get_specific_id($_POST['report_id']);
			$found = $classname::get_specific_id($id);
			if($found) {
					
					####################
					## APPROVE REPORT NOTIF ##
					####################
					if($classname == 'Question') {
						if(URLTYPE == 'slug') {$url_type = $found->slug;} else {$url_type = $found->id;}
						//$notif_link = $url_mapper['questions/view'].$url_type;
						$notif_link = $url_mapper['pages/view'].'terms';
						
						$str = $lang['notif-report-q_publisher-approve'];
						$str = str_replace('[TITLE]' , "<a href='{$notif_link}'>".$found->title."</a>", $str);
						//$str = str_replace('[CONTENT]' , strip_tags($found->content), $str);
						$notif_msg = $str;
						$notif_user = $found->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						##########
						## MAILER ##
						##########
						$msg = $notif_msg;
						$title = 'Your content was removed based on users reports';
						$receiver = User::get_specific_id($notif_user);
						if($receiver && is_object($receiver)) {
							Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
						}
						
						
						$str = $lang['notif-report-q_reporter-approve']; $str = str_replace('[TITLE]' , "<a href='{$notif_link}'>".$found->title."</a>" , $str);
						//$str = str_replace('[CONTENT]' , strip_tags($found->content), $str);
						$notif_msg = $str;
						$notif_user = $report->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						##########
						## MAILER ##
						##########
						$msg = $notif_msg;
						$title = 'Content was removed based on your report';
						$receiver = User::get_specific_id($notif_user);
						if($receiver && is_object($receiver)) {
							Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
						}
						
						
					} elseif($classname == 'Answer') {
						$q = Question::get_specific_id($found->q_id);
						if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
						//$notif_link = $url_mapper['questions/view'].$url_type.'#answer-'.$found->id;
						$notif_link = $url_mapper['pages/view'].'terms';
						
						$str = $lang['notif-report-a_publisher-approve']; $str = str_replace('[TITLE]' , "<a href='{$notif_link}'>".$q->title."</a>" , $str);
						//$str = str_replace('[CONTENT]' , strip_tags($found->content), $str);
						$notif_msg = $str;
						$notif_user = $found->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						##########
						## MAILER ##
						##########
						$msg = $notif_msg;
						$title = 'Your content was removed based on users reports';
						$receiver = User::get_specific_id($notif_user);
						if($receiver && is_object($receiver)) {
							Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
						}
						
						$str = $lang['notif-report-a_reporter-approve']; $str = str_replace('[TITLE]' , "<a href='{$notif_link}'>".$q->title."</a>" , $str);
						//$str = str_replace('[CONTENT]' , strip_tags($found->content), $str);
						$notif_msg = $str;
						$notif_user = $report->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						##########
						## MAILER ##
						##########
						$msg = $notif_msg;
						$title = 'Content was removed based on your report';
						$receiver = User::get_specific_id($notif_user);
						if($receiver && is_object($receiver)) {
							Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
						}
						
						
					}
					
					if($classname == 'Question') { //Delete answers!
						$answers = Answer::get_answers_for($found->id);
						foreach($answers as $a) {
							$a->delete();
						}
					}
					$found->delete();
					
					$report->result = 'approved';
					$report->update();
			}
		break;
		
		case 'reject-report' :
			$classname = ucfirst($data);
			$report = Report::get_specific_id($_POST['report_id']);
			$found = $classname::get_specific_id($id);
			if($found) {
					###################
					## REJECT REPORT NOTIF ##
					###################
					if($classname == 'Question') {
						if(URLTYPE == 'slug') {$url_type = $found->slug;} else {$url_type = $found->id;}
						$notif_link = $url_mapper['questions/view'].$url_type;
						$str = $lang['notif-report-q_reporter-reject']; $str = str_replace('[TITLE]' , "<a href='{$notif_link}'>".$found->title."</a>" , $str);
						$notif_msg = $str;
						$notif_user = $report->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						##########
						## MAILER ##
						##########
						$msg = $notif_msg . "<br>Check it out at ". $notif_link;
						$title = 'Report reviewd, content not removed';
						$receiver = User::get_specific_id($notif_user);
						if($receiver && is_object($receiver)) {
							Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
						}
					} elseif($classname == 'Answer') {
						$q = Question::get_specific_id($found->q_id);
						if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
						$notif_link = $url_mapper['questions/view'].$url_type.'#answer-'.$found->id;
						$str = $lang['notif-report-q_reporter-reject']; $str = str_replace('[TITLE]' , "<a href='{$notif_link}'>".$q->title."</a>" , $str);
						$notif_msg = $str;
						$notif_user = $report->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						##########
						## MAILER ##
						##########
						$msg = $notif_msg . "<br>Check it out at ". $notif_link;
						$title = 'Report reviewd, content not removed';
						$receiver = User::get_specific_id($notif_user);
						if($receiver && is_object($receiver)) {
							Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
						}
					}
					
					//$found->delete();
					$report->result = 'rejected';
					$report->update();
			}
		break;
		
		###############################################################
		case 'dislike' :
			$classname = ucfirst($data);
			$found = $classname::get_specific_id($id);
			if($found) {
				//Check prev like..
				$prev_likes = LikeRule::get_for_obj($data , "dislike" , $id, $current_user->id);
				if(!$prev_likes) {
					//Create like..
					$like = New LikeRule();
					$like->user_id = $current_user->id;
					$like->obj_id = $id;
					$like->obj_type = $data;
					$like->type = 'dislike';
					$like->like_date = strftime("%Y-%m-%d %H:%M:%S", time());
					$like->create();
					
					$found->dislikes +=1;
					$found->update();
				}
			}
		break;
		
		case 'undislike' :
			$classname = ucfirst($data);
			$found = $classname::get_specific_id($id);
			if($found) {
				//Check prev like..
				$prev_likes = LikeRule::get_for_obj($data , "dislike" , $id, $current_user->id);
				if($prev_likes) {
					//Create like..
					$prev_likes->delete();
					$found->dislikes -=1;
					$found->update();
				}
			}
		break;
		
		###############################################################
		case 'upl_img' :
			if ($_FILES['img']['name']) {
				if (!$_FILES['img']['error']) {
					$files = '';
					$img_id = 0;
					$f = 0;
					$target = $_FILES['img'];
					$upload_problems = 0;
					
						$file = "file";
						$string = $$file . "{$f}";
						$$string = new File();	
							if(!empty($_FILES['img']['name'])) {
								$$string->ajax_attach_file($_FILES['img']);
								if ($$string->save()) {
									$img_id = $$string->id;
									$img_cont = File::get_specific_id($img_id);
									echo UPL_FILES."/".$img_cont->image_path(); 
								} else {
									$upl_msg = "Upload Error! ";	
									$upl_msg .= join(" " , $$string->errors);
								}
							}
				} else {
				  echo  $message = 'Ooops!  Your upload triggered the following error:  '.$_FILES['img']['error'];
				}
			}
		break;
		
		###############################################################
		case 'q_suggestions' :
			
			$result = Question::find( $data , 'title' , " LIMIT 5");
			$return = Array();
			if(!empty($result)) {
				foreach($result as $r) {
					if(URLTYPE == 'slug') {
						$slug = $r->slug;
					} else {
						$slug = $r->id;
					}				
					$q = array(
						'title' => $r->title,
						'slug' => $slug,
						'full' => "Question: {$r->title}"
					);
					array_push($return, $q);
				}
			} else {
				$q = array(
						'title' => 'No Results Found!',
						'slug' => '',
						'full' => "No Results Found!"
					);
				array_push($return, $q);
			}
			
			$json = json_encode($return);
			echo $json;
		
		break;
		###############################################################
		case 'tags_suggestions' :
			
			$result = Tag::find($data , "name" , "LIMIT 5");
			$return = Array();
			
			foreach($result as $r ) {
				$q = array(
					'tag' => $r->name
				);
				array_push($return, $q);
			}
			
			$json = json_encode($return);
			echo $json;
		
		break;
		###############################################################
		case 'read_msg' :
			
			if(!EMail::check_id_existance($id)) {
				echo "<h4 style=\"color:red; font-family:Century Gothic\" ><center>Error! This page can't be accessed directly! please try again using main program #item-selector</center></h4>";
			}
			
			if(!EMail::check_ownership($id, $current_user->id)) {
				echo "<h4 style=\"color:red; font-family:Century Gothic\" ><center>Error! This page can't be accessed directly! please try again using main program #item-ownership</center></h4>";
			}
			
			$mymsg = 0;
			
			$mail_msg = EMail::get_specific_id($id);
			$last_reply = Reply::get_last_reply_for($id);
			
			if($last_reply) {
				if ($last_reply->sender == $current_user->id) {
					$mymsg = 1;
				}
			}
			
			if ($data =="received" && $mymsg == 0) { $mail_msg->read_msg(); }
			
			
		break;
		
		
		###############################################################
		default : 
			echo "<h4 style=\"color:red; font-family:Century Gothic\" ><center>Error! This page can't be accessed directly! please try again using main program #switch</center></h4>";
			die();
		break;
	}
	
} else {
	
	echo "<h4 style=\"color:red; font-family:Century Gothic\" ><center>Error! This page can't be accessed directly! please try again using main program #intro</center></h4>";
	die();
}
?>