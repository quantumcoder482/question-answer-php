<?php require_once('assets/includes/route.php');

if(isset($_GET['notif']) && is_numeric($_GET['notif'])) {
	$notification = Notif::get_specific_id($db->escape_value($_GET['notif']));
	if($notification && $notification->user_id == $current_user->id) {
		$notification->read();
	}
}
if (isset($_GET['data']) && $_GET['data'] != '' ) {
		$data = $db->escape_value($_GET['data']);
		if(URLTYPE == 'id') {
			$q = Question::get_specific_id($data);
		} else {
			$q = Question::get_slug($data);
		}
		
		if($q) {
			if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
			
			$title= strip_tags($q->title);
			$user = User::get_specific_id($q->user_id);
			if($user->avatar) {
				$img = File::get_specific_id($user->avatar);
				$quser_avatar = WEB_LINK."assets/".$img->image_path();
				$quser_avatar_path = UPLOADPATH."/".$img->image_path();
				if (!file_exists($quser_avatar_path)) {
					$quser_avatar = WEB_LINK.'assets/img/avatar.png';
				}
			} else {
				$quser_avatar = WEB_LINK.'assets/img/avatar.png';
			}
			
			if($q->anonymous) {
				$quser_avatar = WEB_LINK.'assets/img/avatar.png';
			}
			
			$q->view_q();
		
		} else {
			redirect_to($url_mapper['error/404/']);
		}
} else {
	redirect_to($url_mapper['error/404/']);
}


if (isset($_POST['submit_report'])) {
	
	if($_POST['hash'] != $_SESSION[$elhash] ) {
		redirect_to($url_mapper['index/']);
	}
	
	$id = $_POST['id'];
	$obj_type = $_POST['obj_type'];
	$info = $_POST['info'];
	
	$report = new Report();
	$report->obj_id = $id;
	$report->obj_type = $obj_type;
	$report->info = profanity_filter(strip_tags($info));
	$report->user_id = $current_user->id;
	$report->report_date = strftime("%Y-%m-%d %H:%M:%S" , time());
	
	if($report->create()) {
		$msg = $lang['alert-report_success'];

		if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
			redirect_to($url_mapper['questions/view']."{$url_type}&edit=success&msg={$msg}");
	} else {
		$msg = $lang['alert-report_failed'];
		redirect_to($url_mapper['questions/create']."&edit=fail&msg={$msg}");
	}
	
}

if (isset($_GET['id']) && $_GET['id'] != '' && isset($_GET['type']) && $_GET['type'] != '' && isset($_GET['hash']) && $_GET['hash'] != '' ) {
	
	if($_GET['hash'] != $_SESSION[$elhash] ) {
		redirect_to($url_mapper['index/']);
	}

	switch($_GET['type']) {

		case 'approve' :
			
			if(!$current_user->can_see_this("pending.update",$group)) {
				$msg = $lang['alert-restricted'];
				if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
				redirect_to($url_mapper['questions/view'].$url_type."&edit=fail&msg={$msg}");
			}
			$id = $db->escape_value($_GET['id']);
			$q = Question::get_specific_id($id);
			if($q) {
				$q->publish();
				
				###############
				## APPROVE NOTIF ##
				###############
				if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
				$notif_link = $url_mapper['questions/view'].$url_type;
				$str = $lang['notif-q_publish']; $str = str_replace('[TITLE]' , $q->title , $str);
				$notif_msg = $str;
				$notif_user = $q->user_id;
				$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
				##########
				## MAILER ##
				##########
				$msg = $notif_msg . "<br>Check it out at " . $notif_link;
				$title = 'Question Approved';
				$receiver = User::get_specific_id($notif_user);
				if($receiver && is_object($receiver)) {
					Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
				}
			}
			
		break;
		
		case 'approve_answer' :
			
			if(!$current_user->can_see_this("pending.update",$group)) {
				$msg = $lang['alert-restricted'];
				if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
				redirect_to($url_mapper['questions/view'].$url_type."&edit=fail&msg={$msg}");
			}
			
			$data = $db->escape_value($_GET['id']);
			$edited_answer = Answer::get_specific_id($data);
			if($edited_answer) {
				$edited_answer->publish();
				
				###############
				## APPROVE NOTIF ##
				###############
				if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
				$notif_link = $url_mapper['questions/view'].$url_type.'#answer-'.$edited_answer->id;
				$str = $lang['notif-a_publish']; $str = str_replace('[TITLE]' , $q->title , $str);
				$notif_msg = $str;
				$notif_user = $edited_answer->user_id;
				$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
				
				##########
				## MAILER ##
				##########
				$msg = $notif_msg . "<br>Check it out at " . $notif_link;
				$title = 'Answer Approved';
				$receiver = User::get_specific_id($notif_user);
				if($receiver && is_object($receiver)) {
					Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
				}
				
			}
			
		break;
		
		case 'edit_answer' :
			if(!$current_user->can_see_this("answers.update",$group)) {
				$msg = $lang['alert-restricted'];
				if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
				redirect_to($url_mapper['questions/view'].$url_type."&edit=fail&msg={$msg}");
			}
			$data = $db->escape_value($_GET['id']);
			$edited_answer = Answer::get_specific_id($data);
			if($edited_answer) {
				$edit_answer_mode = true;
			}
			
		break;
		
		case 'delete_answer' :
			if(!$current_user->can_see_this("answers.delete",$group)) {
				$msg = $lang['alert-restricted'];
				if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
				redirect_to($url_mapper['questions/view'].$url_type."&edit=fail&msg={$msg}");
			}
			$data = $db->escape_value($_GET['id']);
			$edited_answer = Answer::get_specific_id($data);
			
			if($edited_answer && $edited_answer->user_id == $current_user->id ||  $edited_answer && $current_user->prvlg_group == '1' ) {
				if($edited_answer->delete()) {
					$msg = $lang['alert-delete_success'];
					if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
					redirect_to($url_mapper['questions/view'].$url_type."&edit=success&msg={$msg}");
				} else {
					$msg = $lang['alert-delete_failed'];
					if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
					redirect_to($url_mapper['questions/view'].$url_type."&edit=fail&msg={$msg}");
				}
			}
		break;
	}
}

require_once('assets/includes/header.php');
if(isset($_POST['add_a'])) {
	if($_POST['hash'] == $_SESSION[$elhash]){
		unset($_SESSION[$elhash]);
		
			if(!$current_user->can_see_this("answers.create",$group)) {
				$msg = $lang['alert-restricted'];
				if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
				redirect_to($url_mapper['questions/view'].$url_type."&edit=fail&msg={$msg}");
			}
			
			$content = profanity_filter($_POST['title']);
			
			if(isset($_POST['edit_id'])) { 		//edit_comment mode ..
				if(!Answer::check_id_existance($db->escape_value($_POST['edit_id']))) {
					if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
					redirect_to($url_mapper['questions/view'] . "{$url_type}");
				}
				$answer = Answer::get_specific_id($db->escape_value($_POST['edit_id']));
				if($current_user->id == $answer->user_id || $current_user->prvlg_group == '1') {	  //Ownership..
					$answer->updated_at = strftime("%Y-%m-%d %H:%M:%S" , time());
					$answer->content = $content;
					if($answer->update()) {
						$msg = $lang['questions-answer-update_success'];
						
					//Mentions
					preg_match_all('/(^|\s|&nbsp;)(@\w+)/', strip_tags($content), $mentions);
					$mention_results = array_unique($mentions[0]);

					if(isset($mention_results) && is_array($mention_results)) {
						
						foreach($mention_results as $r) {
							
							$new_r = trim(str_replace('@','',$r));
							$new_r = trim(str_replace('&nbsp;','',$new_r));
							
							$usrs = User::find($new_r , 'username' , ' LIMIT 1');
							if($usrs) {
								foreach($usrs as $u) {
							
									if($u && $u->id != 0 && $u->id != $current_user->id) {
										$str = $lang['notif-a_mention']; $str = str_replace("[NAME]" , $current_user->f_name, $str); $str = str_replace("[TITLE]" , $q->title , $str);
										$mention_notif_msg = $str;
										$notif_user = $u[0]->id;
										$notif = Notif::send_notification($notif_user,$mention_notif_msg,$notif_link);
										##########
										## MAILER ##
										##########
										$msg = $mention_notif_msg . "<br>" . $notif_link;
										$title = 'New Mention For You';
										$receiver = User::get_specific_id($notif_user);
										if($receiver && is_object($receiver)) {
											Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
										}
									}
								}
							}
						}
					}
						
						if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
						redirect_to($url_mapper['questions/view'] . "{$url_type}&edit=success&msg={$msg}");
					} else {
						$msg = $lang['questions-answer-update_failed'];
						if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
						redirect_to($url_mapper['questions/view'] . "{$url_type}&edit=fail&msg={$msg}");
					}
				}
			} else {
					
				$a = New Answer();
				$a->user_id = $current_user->id;
				$a->q_id = $q->id;
				$a->created_at = strftime("%Y-%m-%d %H:%M:%S" , time());
				$a->content = $content;
				
				
				if($settings['a_approval'] == '0' || $settings['a_approval'] == '1' && $current_user->prvlg_group == '1' || $settings['a_approval'] == '1' && $current_user->can_see_this("answers.power",$group) ) {
					$a->published = 1;
				}
				
				if($a->create()) {
					
					###############
					## FOLLOW NOTIF ##
					###############
					if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
					$notif_link = $url_mapper['questions/view'].$url_type.'#answer-'.$a->id;
					$str = $lang['notif-a_publish-follow']; $str = str_replace("[NAME]" , $current_user->f_name, $str); $str = str_replace("[TITLE]" , $q->title , $str);
					$notif_msg = $str;
					
					//Mentions
					preg_match_all('/(^|\s|&nbsp;)(@\w+)/', strip_tags($content), $mentions);
					$mention_results = array_unique($mentions[0]);
					
					print_r($mention_results);
				
					if(isset($mention_results) && is_array($mention_results)) {
						
						foreach($mention_results as $r) {
							
							$new_r = trim(str_replace('@','',$r));
							$new_r = trim(str_replace('&nbsp;','',$new_r));
							
							$usrs = User::find($new_r , 'username' , ' LIMIT 1');
							if($usrs) {
								foreach($usrs as $u) {
									
									if($u && $u->id != 0 && $u->id != $current_user->id) {
										$str = $lang['notif-a_mention']; $str = str_replace("[NAME]" , $current_user->f_name, $str); $str = str_replace("[TITLE]" , $q->title , $str);
										$mention_notif_msg = $str;
										$notif_user = $u->id;
										$notif = Notif::send_notification($notif_user,$mention_notif_msg,$notif_link);
										##########
										## MAILER ##
										##########
										$msg = $mention_notif_msg . "<br>" . $notif_link;
										$title = 'New Mention For You';
										$receiver = User::get_specific_id($notif_user);
										if($receiver && is_object($receiver)) {
											Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
										}
									}
								}
							}
						}
						
					}
					
					//Question owner
					if($q->user_id != $a->user_id) {
						$notif_user = $q->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						##########
						## MAILER ##
						##########
						$msg = $notif_msg . "<br>Check it out at " . $notif_link;
						$title = 'New Answer Posted';
						$receiver = User::get_specific_id($notif_user);
						if($receiver && is_object($receiver)) {
							Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
						}
					}
					//Question followers
					$user_followers = FollowRule::get_subscriptions('question',$q->id , 'obj_id' , "" );
					if($user_followers) {
						foreach($user_followers as $uf) {
							$notif_user = $uf->user_id;
							if($q->user_id != $uf->user_id && $notif_user != $current_user->id ) {
								$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
								##########
								## MAILER ##
								##########
								$msg = $notif_msg . "<br>Check it out at " . $notif_link;
								$title = 'New Answer Posted';
								$receiver = User::get_specific_id($notif_user);
								if($receiver && is_object($receiver)) {
									Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
								}
							}
						}
					}
					$q->answers +=1;
					$q->update();
					//$id = mysql_insert_id();
					
					$msg = $lang['questions-answer-create_success'];
					
					if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
					redirect_to($url_mapper['questions/view'] . "{$url_type}&edit=success&msg={$msg}");
				} else {
					$msg = $lang['questions-answer-create_failed'];
					if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
					redirect_to($url_mapper['questions/view'] . "{$url_type}&edit=fail&msg={$msg}");
				}
				
			}
			
			
        }
}
require_once('assets/includes/navbar.php');


$upvote_class = 'upvote';
$downvote_class = 'downvote';

$upvote_txt = $lang['btn-like'];
$liked = LikeRule::check_for_obj('question' , "like" , $q->id, $current_user->id);
if($liked) {
	$upvote_txt = $lang['btn-liked'];
	$upvote_class = 'active undo-upvote';
	$downvote_class = 'downvote disabled';
}

$downvote_txt = $lang['btn-dislike'];
$disliked = LikeRule::check_for_obj('question' , "dislike" , $q->id, $current_user->id);
if($disliked) {
	$downvote_txt = $lang['btn-disliked'];
	$upvote_class = 'upvote disabled';
	$downvote_class = 'active undo-downvote';
}


$q_follow_class = 'follow';
$follow_txt = $lang['btn-follow'];
$followed = FollowRule::check_for_obj('question' , $q->id, $current_user->id);
if($followed) {
	$follow_txt = $lang['btn-followed'];
	$q_follow_class = 'active unfollow';
}

?>
<div class="container">	

<div class="row">
	
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

	
		<?php
			@$tags = explode(",",$q->feed); 
			if(is_array($tags)) {
				foreach($tags as $tag) {
		?>
		<a href="<?php echo $url_mapper['feed/'].$tag; ?>/" class="label label-default"><?php echo $tag; ?></a>
			<?php }} ?>
		<?php if($q->published == 0) { ?><p class="label label-danger"><i class="fa fa-eye-slash"></i> <?php echo $lang['questions-pending-tag']; ?></p><?php } ?>
		
		<h1 class="title"><b><?php echo strip_tags($q->title); ?></b></h1>
		
		<p class="footer">
			
			<?php 
			if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
			
			if($current_user->can_see_this('questions.interact', $group)) {
			?>
			<div class="btn-group question-like-machine" role="group" aria-label="question-tools">

			<?php if($q->user_id != $current_user->id) { ?><a href="#me" class="btn btn-sm btn-default <?php echo $upvote_class; ?>" name="<?php echo $q->id; ?>" value="<?php echo $q->likes; ?>" data-obj="question" data-lbl="<?php echo $lang['btn-like']; ?>" data-lbl-active="<?php echo $lang['btn-liked']; ?>"  ><i class="glyphicon glyphicon-thumbs-up"></i> <?php echo $lang['btn-likes']; ?> | <?php echo $q->likes; ?></a>
			<a href="#me" class="btn btn-sm btn-default <?php echo $downvote_class; ?>" name="<?php echo $q->id; ?>" value="<?php echo $q->dislikes; ?>" data-obj="question" data-lbl="<?php echo $lang['btn-dislike']; ?>" data-lbl-active="<?php echo $lang['btn-disliked']; ?>"  ><i class="glyphicon glyphicon-thumbs-down"></i> <?php echo $lang['btn-dislikes']; ?> | <?php echo $q->dislikes; ?></a><?php } else { ?>
			
			<a href="#me" class="btn btn-sm btn-default disabled" ><i class="glyphicon glyphicon-thumbs-up"></i> <?php echo $lang['btn-likes']; ?> | <?php echo $q->likes; ?></a>
			<a href="#me" class="btn btn-sm btn-default disabled" ><i class="glyphicon glyphicon-thumbs-down"></i> <?php echo $lang['btn-likes']; ?> | <?php echo $q->dislikes; ?></a>
			
			<?php } if($current_user->can_see_this("answers.create",$group)) { ?><a href="#me" id="answer-btn" class="btn btn-sm btn-default"><i class="glyphicon glyphicon-comment"></i> <?php echo $lang['btn-answers']; ?> | <?php echo $q->answers; ?></a><?php } ?>
			<a href="#me" class="btn btn-sm btn-default"><i class="glyphicon glyphicon-eye-open"></i> <?php echo $lang['btn-views']; ?> | <?php echo $q->views; ?></a>
			
			
			<?php if($q->user_id != $current_user->id) { ?><a href="#me" class="btn btn-sm btn-default <?php echo $q_follow_class; ?>" name="<?php echo $q->id; ?>" value="<?php echo $q->follows; ?>" data-obj="question" data-lbl="<?php echo $lang['btn-follow']; ?>" data-lbl-active="<?php echo $lang['btn-followed']; ?>" ><i class="fa fa-user-plus"></i> <?php echo $follow_txt; ?> | <?php echo $q->follows; ?></a><?php } else { ?>
				<a href="#me" class="btn btn-sm btn-default disabled" ><i class="fa fa-user-plus"></i> <?php echo $follow_txt; ?> | <?php echo $q->follows; ?></a>
			<?php } ?>
			
			
			
			<div class="btn-group">
				<button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
				<?php echo $lang['btn-tools']; ?> <span class="caret"></span></button>
				<ul class="dropdown-menu" role="menu" style="width:100px; background-color:white">
					<?php if($q->user_id == $current_user->id || $current_user->prvlg_group == '1') { ?>
						<?php if($q->published == 0) { ?>
						<?php if($current_user->can_see_this('pending.update' , $group)) { ?>
							<li><a href="<?php echo $url_mapper['questions/approve'] . $url_type."&id={$q->id}&hash={$random_hash}"; ?>" ><?php echo $lang['questions-approve']; ?></a></li>
							<li role="separator" class="divider"></li>
						<?php } ?>
						<?php } ?>
					
						<?php if($current_user->can_see_this('questions.update' , $group)) { ?><li><a href="<?php echo $url_mapper['questions/update']. $url_type."&hash={$random_hash}"; ?>" ><?php echo $lang['questions-edit']; ?></a></li><?php } ?>
						<?php if($current_user->can_see_this('questions.delete' , $group)) { ?><li><a href="<?php echo $url_mapper['questions/delete']. $url_type."&hash={$random_hash}"; ?>" onClick="return confirm('Are you sure you want to delete this question?');" ><?php echo $lang['questions-delete']; ?></a></li><?php } ?>
					<li role="separator" class="divider"></li>
					<?php } ?>
					<?php $reported = Report::check_for_obj('question' , $q->id, $current_user->id); ?>
					<?php if(!$reported) { ?>
					<li><a href="#report-q" data-toggle="modal" ><?php echo $lang['questions-report']; ?></a></li>
					<?php } else { ?>
					<li style='color:#a0a0a0; text-align:center; padding:5px'><?php echo $lang['questions-report-reported']; ?></li>
					<?php } ?>
				</ul>
					<?php if(!$reported) { ?>
					<!-- Modal -->
					<div class="modal fade in" id="report-q" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
					  <div class="modal-dialog modal-lg" role="document">
						<div class="modal-content">
						  <div class="modal-header">
							<h5 class="modal-title" id="exampleModalLabel"><?php echo $lang['questions-report']; ?></h5>
						  </div>
						  <form action="<?php  echo $url_mapper['questions/view']; echo $url_type; ?>" method="POST" >
						  <div class="modal-body">
								<div class="form-group">
									<div class="flag_reasons clearfix">
										<div class="radio">
										  <label>
											<input type="radio" name="info" id="optionsRadios1" value="Harassment: Not respectful towards a person or group" checked>
											<b>Harassment:</b> <span class="light_gray">Not respectful towards a person or group</span>
										  </label>
										</div>
										<div class="radio">
										  <label>
											<input type="radio" name="info" id="optionsRadios2" value="Spam: Undisclosed promotion for a link or product">
											<b>Spam:</b> <span class="light_gray">Undisclosed promotion for a link or product</span>
										  </label>
										</div>
										<div class="radio">
										  <label>
											<input type="radio" name="info" id="optionsRadios3" value="Irrelevant: Does not address question that was asked">
											<b>Irrelevant:</b> <span class="light_gray">Does not address question that was asked</span>
										  </label>
										</div>
										<div class="radio">
										  <label>
											<input type="radio" name="info" id="optionsRadios4" value="Plagiarism: Reusing content without attribution (link and blockquotes)">
											<b>Plagiarism:</b> <span class="light_gray">Reusing content without attribution (link and blockquotes)</span></label>
										  </label>
										</div>
										<div class="radio">
										  <label>
											<input type="radio" name="info" id="optionsRadios5" value="Joke Answer: Not a sincere answer">
												<b>Joke Answer:</b> <span class="light_gray">Not a sincere answer</span>
										  </label>
										</div>
										<div class="radio">
										  <label>
											<input type="radio" name="info" id="optionsRadios6" value="Poorly Written: Bad formatting, grammar, and spelling">
											<b>Poorly Written:</b> <span class="light_gray">Bad formatting, grammar, and spelling</span>
										  </label>
										</div>
										<div class="radio">
										  <label>
											<input type="radio" name="info" id="optionsRadios7" value="Incorrect: Substantially incorrect and/or incorrect primary conclusions">
											<b>Incorrect:</b> <span class="light_gray">Substantially incorrect and/or incorrect primary conclusions</span>
										  </label>
										</div>
								</div>
								</div>
								
						  </div>
						  
						  <div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $lang['btn-cancel']; ?></button>
							<button type="submit" name="submit_report" class="btn btn-primary"><?php echo $lang['btn-submit']; ?></button>
						  </div>
							<?php 
								$_SESSION[$elhash] = $random_hash;
								echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>";
								echo "<input type=\"hidden\" name=\"id\" value=\"".$q->id."\" readonly/>";
								echo "<input type=\"hidden\" name=\"obj_type\" value=\"question\" readonly/>";
							?>
						  </form>
						</div>
					  </div>
					</div>
					<?php } ?>
				
			  </div>
			
			</div>
			<?php } ?>
			
			<hr>
		</p>
		
		<p class="publisher">
			<img src="<?php echo $quser_avatar; ?>" class="img-circle" style="float:<?php echo $lang['direction-left']; ?>;width:46px;margin-<?php echo $lang['direction-right']; ?>:10px">
			<p class="name">
				
				<?php if($q->anonymous) { echo $lang['user-anonymous']; } else { ?>
				
				<b><a href="<?php echo $url_mapper['users/view'] . $q->user_id; ?>/" style="color:black"><?php echo $user->f_name . " " . $user->l_name; ?></a></b><?php if($user->comment) { echo " " . $user->comment; } ?>
				
				<?php if($q->user_id != $current_user->id && $current_user->can_see_this('users.follow' , $group) ) { ?>
				<?php
				$u_follow_class = 'follow';
				$follow_txt = $lang['btn-follow'];
				$followed = FollowRule::check_for_obj('user' , $user->id, $current_user->id);
				if($followed) {
					$follow_txt = $lang['btn-followed'];
					$u_follow_class = 'active unfollow';
				}
				?>
				&nbsp;&nbsp;<a href="#me" class="btn btn-sm btn-default <?php echo $u_follow_class; ?>" name="<?php echo $user->id; ?>" value="<?php echo $q->follows; ?>" data-obj="User" data-lbl="<?php echo $lang['btn-follow']; ?>" data-lbl-active="<?php echo $lang['btn-followed']; ?>" ><i class="fa fa-user-plus"></i> <?php echo $follow_txt; ?> | <?php echo $user->follows; ?></a>
				<?php } ?>
				
				<?php } ?>
				
				<br><small style="color:#999">@<?php echo $user->username; ?> | <?php if($q->updated_at != "0000-00-00 00:00:00") { echo $lang['index-question-updated'] . ' ' . date_ago($q->updated_at); } else { echo $lang['index-question-created'] . ' ' . date_ago($q->created_at); }?></small>
				
				<!-- Go to www.addthis.com/dashboard to customize your tools --> <div class="addthis_inline_share_toolbox"></div>
				
			</p>
		</p>
		
		<br>
		
		<p class="question-content">
			<?php 
				$content = str_replace('\\','',$q->content);
				$content = str_replace('<script','',$content);
				$content = str_replace('</script>','',$content);
				echo profanity_filter($content);
			?>
		</p>
		
		<?php 
		
		$per_page = "10";
		if (isset($_GET['page']) && is_numeric($_GET['page']) ) {
				$page= $_GET['page'];
		} else {
				$page=1;
		}
		
		$total_count = Answer::count_answers_for($q->id, '');
		$pagination = new Pagination($page, $per_page, $total_count);		
		
		$str = " LIMIT {$per_page} OFFSET {$pagination->offset()} ";
		if($current_user->id == 1000) {
			$str = ' LIMIT 1 ';
		}
		
		$answers = Answer::get_answers_for($q->id, $str); 
		$t = 1 + (($page - 1) * $per_page);
		if($answers) {
			foreach($answers as $a) {
				
			if($a->published == '1' || $a->published == '0' && $current_user->id == $a->user_id || $a->published == '0' && $current_user->prvlg_group == '1' ) {
				
				$user = User::get_specific_id($a->user_id);
				if($user->avatar) {
					$img = File::get_specific_id($user->avatar);
					$quser_avatar = WEB_LINK."assets/".$img->image_path();
					$quser_avatar_path = UPLOADPATH."/".$img->image_path();
					if (!file_exists($quser_avatar_path)) {
						$quser_avatar = WEB_LINK.'assets/img/avatar.png';
					}
				} else {
					$quser_avatar = WEB_LINK.'assets/img/avatar.png';
				}
				
				
				$upvote_class = 'upvote';
				$downvote_class = 'downvote';

				$upvote_txt = '';
				$liked = LikeRule::check_for_obj('answer' , "like" , $a->id, $current_user->id);
				if($liked) {
					$upvote_txt = '';
					$upvote_class = 'active undo-upvote';
					$downvote_class = 'downvote disabled';
				}

				$downvote_txt = '';
				$disliked = LikeRule::check_for_obj('answer' , "dislike" , $a->id, $current_user->id);
				if($disliked) {
					$downvote_txt = '';
					$upvote_class = 'upvote disabled';
					$downvote_class = 'active undo-downvote';
				}

				
		?>
		
		<?php if(isset($admanager1->msg) && $admanager1->msg != '' && $admanager1->msg != '&nbsp;' ) { echo '<hr style="margin-bottom:5px">'.str_replace('\\','',$admanager1->msg).'<hr style="margin-top:5px">'; } else { echo '<hr>'; } ?>
		
		<div class="question-element" id="answer-<?php echo $a->id; ?>">
		<div class="publisher">
			<?php if($a->published == 0) { ?><p class="label label-danger"><i class="fa fa-eye-slash"></i> <?php echo $lang['questions-pending-tag']; ?></p><?php } ?>
			<img src="<?php echo $quser_avatar; ?>" class="img-circle" style="float:<?php echo $lang['direction-left']; ?>;width:46px;margin-<?php echo $lang['direction-right']; ?>:10px">
			<p class="name">
				<b><a href="<?php echo $url_mapper['users/view'] . $a->user_id; ?>/"><?php echo $user->f_name . " " . $user->l_name; ?></a></b><?php if($user->comment) { echo " " . $user->comment; } ?>
				
				<?php if($a->user_id != $current_user->id && $current_user->can_see_this('users.follow' , $group) ) { ?>
				<?php
				$u_follow_class = 'follow';
				$follow_txt = $lang['btn-follow'];
				$followed = FollowRule::check_for_obj('user' , $user->id, $current_user->id);
				if($followed) {
					$follow_txt = $lang['btn-followed'];
					$u_follow_class = 'active unfollow';
				}
				?>
				&nbsp;&nbsp;<a href="#me" class="btn btn-sm btn-default <?php echo $u_follow_class; ?>" name="<?php echo $user->id; ?>" value="<?php echo $user->follows; ?>" data-obj="User" data-lbl="<?php echo $lang['btn-follow']; ?>" data-lbl-active="<?php echo $lang['btn-followed']; ?>" ><i class="fa fa-user-plus"></i> <?php echo $follow_txt; ?> | <?php echo $user->follows; ?></a>
				<?php } ?>
				<br><small>@<?php echo $user->username; ?> | <?php if($a->updated_at != "0000-00-00 00:00:00") { echo $lang['index-question-updated'] . ' ' . date_ago($a->updated_at); } else { echo $lang['index-question-created'] . ' ' . date_ago($a->created_at); }?></small>
			</p>
		</div><br>
		<p class="question-content">
			<?php $content = str_replace('\\','',$a->content);
				$content = str_replace('<script','',$content);
				$content = str_replace('</script>','',$content);
				echo profanity_filter($content); ?>
		</p>
		
		<p class="footer">
			<?php if($current_user->can_see_this('questions.interact' , $group)) { ?>
			<div class="btn-group question-like-machine">
			
				<?php if($a->user_id != $current_user->id) { ?><a href="#me" class="btn btn-sm btn-default <?php echo $upvote_class; ?>" name="<?php echo $a->id; ?>" value="<?php echo $a->likes; ?>" data-obj="answer" data-lbl="" data-lbl-active="" ><i class="glyphicon glyphicon-thumbs-up"></i> | <?php echo $a->likes; ?></a>
				<a href="#me" class="btn btn-sm btn-default <?php echo $downvote_class; ?>" name="<?php echo $a->id; ?>" value="<?php echo $a->dislikes; ?>" data-obj="answer" data-lbl="" data-lbl-active="" ><i class="glyphicon glyphicon-thumbs-down"></i> | <?php echo $a->dislikes; ?></a><?php } else { ?>
				
				<a href="#me" class="btn btn-sm btn-default disabled" ><i class="glyphicon glyphicon-thumbs-up"></i> <?php echo $lang['btn-likes']; ?> | <?php echo $a->likes; ?></a>
				<a href="#me" class="btn btn-sm btn-default disabled" ><i class="glyphicon glyphicon-thumbs-down"></i> <?php echo $lang['btn-dislikes']; ?> | <?php echo $a->dislikes; ?></a>
				<?php } ?>
				<div class="btn-group">
					<button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
					<?php echo $lang['btn-tools']; ?> <span class="caret"></span></button>
					<ul class="dropdown-menu" role="menu" style="width:100px; background-color:white">
						<?php if($a->user_id == $current_user->id || $current_user->prvlg_group == '1') { ?>
							<?php if($a->published == 0) { ?>
							<?php if($current_user->can_see_this('pending.update' , $group)) { ?>
							<li><a href="<?php echo $url_mapper['answers/approve'] . $url_type . "&type=approve_answer&id={$a->id}&hash={$random_hash}"; ?>" >Approve Answer</a></li>
							<li role="separator" class="divider"></li>
							<?php } ?>
							<?php } ?>
						
							<?php if($current_user->can_see_this('answers.update' , $group)) { ?><li><a href="<?php echo $url_mapper['answers/edit'] . $url_type; ?>&type=edit_answer&id=<?php echo $a->id; ?>&hash=<?php echo $random_hash; ?>#answer-question" >Edit</a></li><?php } ?>
							<?php if($current_user->can_see_this('answers.delete' , $group)) { ?><li><a href="<?php echo $url_mapper['answers/delete'] . $url_type; ?>&type=delete_answer&id=<?php echo $a->id; ?>&hash=<?php echo $random_hash; ?>" onclick="return confirm('Are you sure you want to delete this answer?');">Delete</a></li><?php } ?>
						
						<li role="separator" class="divider"></li>
						<?php } ?>
						<?php $reported = Report::check_for_obj('answer' , $a->id, $current_user->id); ?>
						<?php if(!$reported) { ?>
						<li><a href="#report-a-<?php echo $a->id; ?>" data-toggle="modal" ><?php echo $lang['questions-answer-report']; ?></a></li>
						<?php } else { ?>
						<li style='color:#a0a0a0; text-align:center; padding:5px'><?php echo $lang['questions-report-reported']; ?></li>
						<?php } ?>
					</ul>
					
					
					<?php if(!$reported) { ?>
					<!-- Modal -->
					<div class="modal fade in" id="report-a-<?php echo $a->id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
					  <div class="modal-dialog modal-lg" role="document">
						<div class="modal-content">
						  <div class="modal-header">
							<h5 class="modal-title" id="exampleModalLabel"><?php echo $lang['questions-answer-report']; ?></h5>
						  </div>
						  <form action="<?php  echo $url_mapper['questions/view']; echo $url_type; ?>" method="POST" >
						  <div class="modal-body">
								<div class="form-group">
									<div class="flag_reasons clearfix">
										<div class="radio">
										  <label>
											<input type="radio" name="info" id="optionsRadios1" value="Harassment: Not respectful towards a person or group" checked>
											<b>Harassment:</b> <span class="light_gray">Not respectful towards a person or group</span>
										  </label>
										</div>
										<div class="radio">
										  <label>
											<input type="radio" name="info" id="optionsRadios2" value="Spam: Undisclosed promotion for a link or product">
											<b>Spam:</b> <span class="light_gray">Undisclosed promotion for a link or product</span>
										  </label>
										</div>
										<div class="radio">
										  <label>
											<input type="radio" name="info" id="optionsRadios3" value="Irrelevant: Does not address question that was asked">
											<b>Irrelevant:</b> <span class="light_gray">Does not address question that was asked</span>
										  </label>
										</div>
										<div class="radio">
										  <label>
											<input type="radio" name="info" id="optionsRadios4" value="Plagiarism: Reusing content without attribution (link and blockquotes)">
											<b>Plagiarism:</b> <span class="light_gray">Reusing content without attribution (link and blockquotes)</span></label>
										  </label>
										</div>
										<div class="radio">
										  <label>
											<input type="radio" name="info" id="optionsRadios5" value="Joke Answer: Not a sincere answer">
												<b>Joke Answer:</b> <span class="light_gray">Not a sincere answer</span>
										  </label>
										</div>
										<div class="radio">
										  <label>
											<input type="radio" name="info" id="optionsRadios6" value="Poorly Written: Bad formatting, grammar, and spelling">
											<b>Poorly Written:</b> <span class="light_gray">Bad formatting, grammar, and spelling</span>
										  </label>
										</div>
										<div class="radio">
										  <label>
											<input type="radio" name="info" id="optionsRadios7" value="Incorrect: Substantially incorrect and/or incorrect primary conclusions">
											<b>Incorrect:</b> <span class="light_gray">Substantially incorrect and/or incorrect primary conclusions</span>
										  </label>
										</div>
								</div>
								</div>
						  </div>
						  
						  <div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $lang['btn-cancel']; ?></button>
							<button type="submit" name="submit_report" class="btn btn-primary"><?php echo $lang['btn-submit']; ?></button>
						  </div>
							<?php 
								$_SESSION[$elhash] = $random_hash;
								echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>";
								echo "<input type=\"hidden\" name=\"id\" value=\"".$a->id."\" readonly/>";
								echo "<input type=\"hidden\" name=\"obj_type\" value=\"answer\" readonly/>";
							?>
						  </form>
						</div>
					  </div>
					</div>
					<?php } ?>
					
					
					
					
					
				  </div>
			</div>
			<?php } ?>
		</p>
		
		
		</div>
		<?php 
		}$t++; }
		
		if(isset($pagination) && $pagination->total_pages() > 1) {
		?>
		<div class="pagination btn-group">
		
				<?php
				if ($pagination->has_previous_page()) {
					$page_param = $url_mapper['questions/view'].$url_type.'&page=';
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
						$page_param = $url_mapper['questions/view'].$url_type.'&page=';
						$page_param .= $p;
						echo "<a href=\"{$page_param}\" class=\"btn btn-default\" type=\"button\">{$p}</a>";
					}
				}
				if($pagination->has_next_page()) {
					$page_param = $url_mapper['questions/view'].$url_type.'&page=';
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
		
		}
		
		if($current_user->id == 1000) {
		?>
		<div class="col-md-12" style='margin-top:-100px;padding:0'>
			<a href="<?php echo $url_mapper['login/']; ?>"><img src="<?php echo WEB_LINK; ?>/assets/img/login-notice.png" style='width:100%'></a>
			<p style="background-color:white;height:50px" class="col-md-12">
			</p>
		</div>
		<?php
		}
		?>
		<hr>
		
		<?php if($current_user->can_see_this("answers.create",$group)) { ?>
		<a name="answer-question" id="answer-question"></a>
		<form action="<?php echo $url_mapper['questions/view']. $url_type; ?>" method="post" role="form" enctype="multipart/form-data" class="facebook-share-box">
		<div class="">
			<ul class="post-types">
				<li class="post-type">
					<p class="publisher">
						<img src="<?php echo $user_avatar; ?>" class="img-circle" style="float:<?php echo $lang['direction-left']; ?>;width:46px;margin-<?php echo $lang['direction-right']; ?>:10px">
						<p class="name">
							<a href="<?php echo $url_mapper['users/view'] . $current_user->id; ?>/"><?php echo $current_user->f_name . ' ' . $current_user->l_name; ?></a>
						</p>
					</p>
				</li>
			</ul>
			<div class="share">
				<div class="arrow"></div>
				<div class="panel panel-default">
                      <div class="panel-body">
                        <div class="">
                            <textarea name="title" cols="40" rows="10" class="summernote" style="height: 62px; overflow: hidden;" placeholder="What's on your mind ?" required>
							<?php if(isset($edit_answer_mode)) { echo str_replace('\\' , '' , $edited_answer->content); } ?>
							</textarea> 
						</div>
                      </div>
						<div class="panel-footer">
							<div class="form-group">
								<?php if(isset($edit_answer_mode)) { $answer_value=$lang['questions-answer-update']; } else { $answer_value= $lang['questions-answer-create']; } ?>
								<input type="submit" name="add_a" value="<?php echo $answer_value; ?>" style="" class="btn btn-default">
								<?php 
									$_SESSION[$elhash] = $random_hash;
									if(isset($edit_answer_mode)) { echo "<input type=\"hidden\" name=\"edit_id\" value=\"".$edited_answer->id."\" readonly/>"; }
									echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>";
								?>
							</div>									
						</div>
                    </div>
			</div>
			</div>
		</form>
		<?php } ?>
	</div>
	
	<div class="col-md-3 hidden-sm hidden-xs">
		<i class="glyphicon glyphicon-globe"></i>&nbsp;&nbsp;<?php echo $lang['index-sidebar-welcome']; ?> <?php echo $current_user->f_name; ?>!
		<hr>
		<ul class="feed-ul">
			<li><a href="<?php echo $url_mapper['pages/view']; ?>about-us" class="col-md-12"><?php echo $lang['pages-about-title']; ?></a></li>
			<li><a href="<?php echo $url_mapper['pages/view']; ?>contact-us" class="col-md-12"><?php echo $lang['pages-contact-title']; ?></a></li>
			<li><a href="<?php echo $url_mapper['pages/view']; ?>privacy-policy" class="col-md-12"><?php echo $lang['pages-privacy-title']; ?></a></li>
			<li><a href="<?php echo $url_mapper['pages/view']; ?>terms" class="col-md-12"><?php echo $lang['pages-terms-title']; ?></a></li>
			<li><a href="<?php echo $url_mapper['leaderboard/']; ?>" class="col-md-12"><?php echo $lang['pages-leaderboard-title']; ?></a></li>
		</ul>
		<?php if(isset($admanager2->msg) && $admanager2->msg != '' && $admanager2->msg != '&nbsp;' ) { echo "<br style='clear:both'><hr>".str_replace('\\','',$admanager2->msg)."<hr><br style='clear:both'>"; } else { echo "<br style='clear:both'><br style='clear:both'><br style='clear:both'>";} ?>
		<i class="glyphicon glyphicon-warning-sign"></i>&nbsp;&nbsp;<?php echo $lang['index-sidebar-related_questions']; ?>
		<hr>
		
		<ul class="feed-ul">
			<?php
				$questions = Question::get_related_questions_for($q->feed ," LIMIT 10 " );
				if($questions) {
					foreach($questions as $q) {
						if(URLTYPE == 'slug') {
							$url_type = $q->slug;
						} else {
							$url_type = $q->id;
						}
						
						$string=strip_tags($q->title);
						if (strlen($string) > 25) {
							$stringCut = substr($string, 0, 25);
							$string = substr($stringCut, 0, strrpos($stringCut, ' '))."..."; 
						}
						
						?>
						<li><a href="<?php echo $url_mapper['questions/view']; echo $url_type; ?>" class="col-md-12"><?php echo $string; ?></a></li>
						<?php
					}
				}
			?>
		</ul>
		
		<?php if($current_user->id != '1000') { ?>
		<br style='clear:both'><br style='clear:both'><br style='clear:both'>
		<i class="glyphicon glyphicon-question-sign"></i>&nbsp;&nbsp;<?php echo $lang['index-sidebar-your_questions']; ?>
		<hr>
		<ul class="feed-ul">
			<?php
				$total_count = Question::count_questions_for($current_user->id," ");
				$questions = Question::get_questions_for($current_user->id ," LIMIT 5 " );
				if($questions) {
					foreach($questions as $q) {
						if(URLTYPE == 'slug') {
							$url_type = $q->slug;
						} else {
							$url_type = $q->id;
						}
						
						$string= strip_tags($q->title);
						if (strlen($string) > 15) {
							$stringCut = substr($string, 0, 15);
							$string = substr($stringCut, 0, strrpos($stringCut, ' '))."..."; 
						}
						
						?>
						<li><a href="<?php echo $url_mapper['questions/view']; echo $url_type; ?>" class="col-md-12"><?php echo $string; ?></a></li>
						<?php
					}
				if($total_count > 5) {
				?>
				<li><a href="<?php echo $url_mapper['users/view'].$current_user->id ?>/section=questions" class="col-md-12">View +<?php echo ($total_count - 5); ?> more</a></li>
				<?php
				}
				}
			?>
		</ul>
		<br style='clear:both'><br style='clear:both'><br style='clear:both'>
		<i class="glyphicon glyphicon-comment"></i>&nbsp;&nbsp;<?php echo $lang['index-sidebar-your_answers']; ?>
		<hr>
		<ul class="feed-ul">
			<?php 
				$total_count = Answer::count_answers_for_user($current_user->id," ");
				$answers = Answer::get_answers_for_user($current_user->id ," LIMIT 5 " );
				
				if($answers) {
					foreach($answers as $a) {
						$q = Question::get_specific_id($a->q_id);
						if(URLTYPE == 'slug') {
							$url_type = $q->slug;
						} else {
							$url_type = $q->id;
						}
						
						$string=strip_tags($q->title);
						if (strlen($string) > 15) {
							$stringCut = substr($string, 0, 15);
							$string = substr($stringCut, 0, strrpos($stringCut, ' '))."..."; 
						}
						
						?>
						<li><a href="<?php echo $url_mapper['questions/view']; echo $url_type; ?>#answer-<?php echo $a->id; ?>" class="col-md-12"><?php echo $string; ?></a></li>
						<?php
					}
				if($total_count > 5) {
				?>
				<li><a href="<?php echo $url_mapper['users/view'].$current_user->id ?>/section=answers" class="col-md-12">View +<?php echo ($total_count - 5); ?> more</a></li>
				<?php
				}
				}
			?>
		</ul>
		<?php } ?>
		
	</div>
	
</div>
	<?php require_once('assets/includes/footer.php'); ?>
    </div> <!-- /container -->
    <?php require_once('assets/includes/preloader.php'); ?>
	<script src="<?php echo WEB_LINK; ?>assets/plugins/summernote/summernote.js"></script>
	<script src='https://www.google.com/recaptcha/api.js'></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
	<script>
    $(document).ready(function() {
        $('.summernote').summernote({
			height: 150,
			callbacks : {
	            onImageUpload: function(image) {
					sendFile(image[0]);
				}
			},
			hint: {
					mentions: function(keyword, callback) {
						$.ajax({
							dataType: 'json',
							data: {id:<?php echo $current_user->id; ?>, data: keyword , hash:'<?php echo $random_hash; ?>'},
							type: "POST",
							url: "<?php echo WEB_LINK ?>assets/includes/one_ajax.php?type=mention",
							async: true, //This works but freezes the UI
							success:function(data) {
							  console.log(data); 
							}
						}).done(callback);
					},
					match: /\B@(\w*)$/,
					search: function (keyword, callback) {
						this.mentions(keyword, callback); //callback must be an array
					},
					template: function (item) {
						return item.name;
					},
					content: function (item) {
						return $('<a href="'+ item.link +'" class="mentionned" target="_blank">@' + item.name + '</a>')[0];
					}
			  }
        });
		
		function sendFile(image) {
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
                    $('#summernote').summernote("insertImage", url);
				},
				error: function(data) {
					console.log(data);
				}
            });
        }
		$('select').select2();
	});
	$('a#answer-btn').click(function(){
		scrollToAnchor('answer-question');
		$('.note-editable').trigger('focus');
	});
	
	if(window.location.hash) {
	  scrollToId(window.location.hash);
	}
	
	$(document).ready(function(){
		$("img").addClass("img-responsive");
	});
		
	
	</script>
	<?php require_once('assets/includes/like-machine.php'); ?>
	
<?php require_once('assets/includes/bottom.php'); ?>