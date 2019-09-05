<?php require_once('assets/includes/route.php'); 
if ($session->is_logged_in() != true ) { redirect_to($url_mapper['login/']); }

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

defined('APPNAME') ? null : define ('APPNAME' , $settings['site_name']);
defined('APPSLOGAN') ? null : define ('APPSLOGAN' , $settings['site_description']);
defined('URLTYPE') ? null : define ('URLTYPE' , $settings['url_type']);

if(isset($_SESSION[$elhash]) && $_SESSION[$elhash] != "") { 
	$random_hash = $_SESSION[$elhash];
} else {
	$random_hash = uniqid();
	$_SESSION[$elhash] = $random_hash;
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title><?php echo $title . " | " . APPNAME; ?></title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo WEB_LINK; ?>assets/css/bootstrap.css?v=1.01" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?php echo WEB_LINK; ?>assets/css/custom.css?v=1.01" rel="stylesheet">

	<script src="https://use.fontawesome.com/48d68862e7.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="<?php echo WEB_LINK; ?>assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">
	<link href="<?php echo WEB_LINK; ?>assets/plugins/summernote/summernote.css" rel="stylesheet">
	<link href="<?php echo WEB_LINK; ?>assets/plugins/tagsinput/bootstrap-tagsinput.css" rel="stylesheet">
	<link href="<?php echo WEB_LINK; ?>assets/plugins/typeahead/typeaheadjs.css?v=1.01" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
	<link href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css" rel="stylesheet" >
	
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>



<?php require_once('assets/includes/navbar.php'); ?>

<div class="container">		

<div class="row">
	<?php require_once('assets/includes/lt_sidebar.php'); ?>
	<div class="col-md-8">
		
		<br><br><br>
		<center><img src="<?php echo WEB_LINK; ?>assets/img/closed.png" ><br>
		<h2>Site Closed!</h2><hr>
		<?php echo $settings['closure_msg']; ?>
		</center>
		
	</div>
	
	<?php require_once('assets/includes/rt_sidebar.php') ?>
	
</div>
	<?php require_once('assets/includes/footer.php'); ?>
    </div> <!-- /container -->

<?php require_once('assets/includes/bottom.php'); ?>