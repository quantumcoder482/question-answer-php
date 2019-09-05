<?php require_once("assets/includes/Library/Loader.php");
if(!isset($settings['site_lang'])) { $settings['site_lang'] = 'English'; }
require_once($current."/lang/lang.{$settings['site_lang']}.php"); ?>
<?php $title="404 Page Not Found"; require_once('assets/includes/header.php'); ?>
<?php require_once('assets/includes/navbar.php'); ?>

<div class="container">		

<div class="row">
	<?php require_once('assets/includes/lt_sidebar.php'); ?>
	<div class="col-md-8">
		
		<br><br><br>
		<center><img src="<?php echo WEB_LINK; ?>assets/img/404.png" ><br>
		<h2>Page Not Found!</h2><hr>
		May be you'll find what you're looking for here:<br>
		
		<form action="<?php echo $url_mapper['questions/create'] ?>" method="post" role="form" enctype="multipart/form-data">
			<div class="input-group searchbox hidden-sm hidden-xs" style="width:80%">
			  <input type="text" name="title" class="searchbox-field form-control typeahead" placeholder="What's in your mind ?">
			  <span class="input-group-btn">
				<button class="btn btn-default" type="submit">Ask!</button>
			  </span>
			</div><!-- /input-group -->
			<?php 
				$_SESSION[$elhash] = $random_hash;
				echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>";
			?>
		</form>
		</center>
		
	</div>
	
	<?php require_once('assets/includes/rt_sidebar.php') ?>
	
</div>
	<?php require_once('assets/includes/footer.php'); ?>
    </div> <!-- /container -->
	
    <?php require_once('assets/includes/preloader.php'); ?>
	<?php require_once('assets/includes/like-machine.php'); ?>
	
<?php require_once('assets/includes/bottom.php'); ?>