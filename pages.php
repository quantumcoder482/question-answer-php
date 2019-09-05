<?php require_once('assets/includes/route.php');
if(!isset($settings['site_lang'])) { $settings['site_lang'] = 'English'; }
require_once($current."/lang/lang.{$settings['site_lang']}.php");

if(isset($_GET['notif']) && is_numeric($_GET['notif'])) {
	$notification = Notif::get_specific_id($db->escape_value($_GET['notif']));
	if($notification && $notification->user_id == $current_user->id) {
		$notification->read();
	}
}

if (isset($_GET['page']) && $_GET['page'] != '' ) {
		$page_slug= $db->escape_value($_GET['page']);
			switch($page_slug) {
				case 'about-us':
					$curpage = MiscFunction::get_function("about-us");
					$title = $lang['pages-about-title'];
				break;
				
				case 'contact-us':
					$curpage = MiscFunction::get_function("contact-us");
					$title = $lang['pages-contact-title'];
				break;
				
				case 'privacy-policy':
					$curpage = MiscFunction::get_function("privacy-policy");
					$title = $lang['pages-privacy-title'];
				break;
				
				case 'terms':
					$curpage = MiscFunction::get_function("terms");
					$title = $lang['pages-terms-title'];
				break;
				
				default :
					redirect_to($url_mapper['error/404/']);
				break;
			}
		
} else {
	redirect_to($url_mapper['error/404/']);
}



if(isset($_POST['send_email'])) {
	
	if($_POST['hash'] == $_SESSION[$elhash]){
		
		if(isset($_POST['g-recaptcha-response'])) {
          $captcha=$_POST['g-recaptcha-response'];

        if(!$captcha){
			$msg = $lang['alert-captcha_error'];
			redirect_to($url_mapper['pages/view'].'contact-us&edit=fail&msg=' .$msg);
        }
        $response=json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$captcha_info['secret']}&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
        if($response['success'] == false){
			$lang['pages-about-title'];
			redirect_to($url_mapper['pages/view'].'contact-us&edit=fail&msg=' .$msg);
        } else {
				
				$name = strip_tags(trim($db->escape_value($_POST['name'])));
				$email = strip_tags(trim($db->escape_value($_POST['email'])));
				$title = strip_tags(trim($db->escape_value($_POST['title'])));
				$body = strip_tags(trim($db->escape_value($_POST['body'])));
				
				$time = strftime("%Y-%m-%d at %I:%M %p",time());
				
				##########
				## MAILER ##
				##########
				$msg = "Someone contacted you via your contact-us form on <a href='".WEB_LINK."'>".APPNAME."</a>";
				$msg .= "<br><br>Message Details<br><br><ul>";
				$msg .= "<li><b>Name: </b> {$name}</li>";
				$msg .= "<li><b>Email: </b> {$email}</li>";
				$msg .= "<li><b>Message Title: </b> {$title}</li>";
				$msg .= "<li><b>Message Details: </b> {$body}</li>";
				$msg .= "<li><b>Sent at: </b> {$time}</li>";
				$msg .="</ul><br><br>Please reply as soon as possible.";
				
				$title = 'New Contact-Us Message';
				
				if(Mailer::send_mail_to($curpage->msg , 'Admin' , $msg , $title)) {					
					$msg = $lang['pages-contact-success'];
					redirect_to($url_mapper['pages/view']."contact-us&edit=success&msg={$msg}");
				} else {
					$msg = $lang['pages-contact-fail'];
					redirect_to($url_mapper['pages/view']."contact-us&edit=fail&msg={$msg}");
				}
        }

		} else {
			$msg = $lang['alert-captcha_error'];
			redirect_to($url_mapper['pages/view'].'contact-us&edit=fail&msg=' .$msg);
		}
	} else {
		$msg = $lang['alert-auth_error'];
		redirect_to($url_mapper['pages/view'].'contact-us&edit=fail&msg=' .$msg);
	}

}





require_once('assets/includes/header.php');
require_once('assets/includes/navbar.php');

?>
<div class="container">		

<div class="row">
	<?php require_once('assets/includes/lt_sidebar.php'); ?>
	
	<div class="col-md-8">
	
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
				
				echo "<h2 class='page-header'>{$title}</h2>";
				
				$content = str_replace('\\','', $curpage->value);
				$content = str_replace('<script','', $content);
				$content = str_replace('</script>','', $content);
				echo $content;
			
			
				if($curpage->msg) {
				?>
					<form method="post" action="<?php echo $url_mapper['pages/view'] . $page_slug; ?>">
					<div class="form-group">
						<label for="name"><?php echo $lang['pages-contact-name']; ?></label>
						<input type="text" class="form-control" name="name" id="name" value="" required>
					</div>
					<div class="form-group">
						<label for="email"><?php echo $lang['pages-contact-email']; ?></label>
						<input type="email" class="form-control" name="email" id="email" value="" required>
					</div>
					<div class="form-group">
						<label for="title"><?php echo $lang['pages-contact-msg_title']; ?></label>
						<input type="text" class="form-control" name="title" id="title" value="" required>
					</div>
					
					
					<div class="form-group">
						<label for="summernote"><?php echo $lang['pages-contact-msg_details']; ?></label><br>
						<textarea id="" class="form-control" rows="10" name="body"></textarea>
					</div>
					
					<div class="form-group">
						<label for="g-captcha"><?php echo $lang['pages-contact-captcha']; ?></label><br>
						<div class="g-recaptcha" data-sitekey="<?php echo $captcha_info['sitekey']; ?>" ></div>
					</div>
					
					<div class="modal-footer">
						<br/>
						<center>
							<input class="btn btn-success" type="submit" name="send_email" value="<?php echo $lang['pages-contact-send']; ?>">
						</center>
						<?php 
							$_SESSION[$elhash] = $random_hash;
							echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>";
						?>
					</div>
				</form>
		
		<?php
				}
		?>

		
	</div>
	<?php require_once('assets/includes/rt_sidebar.php'); ?>
</div>
	<?php require_once('assets/includes/footer.php'); ?>
    </div> <!-- /container -->
    <?php require_once('assets/includes/preloader.php'); ?>
	<script src="<?php echo WEB_LINK; ?>assets/plugins/summernote/summernote.js"></script>
	<script src='https://www.google.com/recaptcha/api.js'></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
	<script>
    $(document).ready(function() {
        
	if(window.location.hash) {
	  scrollToId(window.location.hash);
	}
	
	$(document).ready(function(){
		$("img").addClass("img-responsive");
	});
	</script>
	<?php require_once('assets/includes/like-machine.php'); ?>
	
<?php require_once('assets/includes/bottom.php'); ?>