<body>

    <!-- Fixed navbar -->
    <nav class="navbar navbar-default navbar-fixed-top" style="z-index:1112 !important">
      <div class="container">
        <div class="navbar-header" style="">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          
		  
		  <a class="navbar-brand" href="<?php echo $url_mapper['index/']; ?>" style="color:#b92b27;font-size:25px"><?php echo APPNAME; ?></a>
        </div>
		
		<?php 
			$navsec = 'index';
			
			if($page == 'users.read') {
				$navsec = 'users';
			}
			
			if($page == 'admin.read') {
				$navsec = 'admin';
			}
			
			if($page == 'index.read') {
				$navsec = 'index';
			}
			
			if(isset($_GET['notifications']) && $_GET['notifications'] == 'true') {
				$navsec = 'notifications';
			}
		?>
		
        <div id="navbar" class="navbar-collapse collapse" >
		
		<ul class="nav navbar-nav navbar-<?php echo $lang['direction-right']; ?>" >
            <li <?php if($navsec == 'index') { echo ' class="current" '; } ?>><a href="<?php echo $url_mapper['index/']; ?>"><i class="glyphicon glyphicon-list-alt"></i>&nbsp;&nbsp;<?php echo $lang['index-read-button']; ?></a></li>
            <?php if($current_user->can_see_this('index.notifications', $group)) { ?><li class="dropdown ">
				<a href="<?php echo $url_mapper['notifications/']; ?>" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" <?php if($navsec == 'notifications') { echo ' class="current" '; } ?> ><i class="glyphicon glyphicon-bullhorn"></i>&nbsp;&nbsp;<?php echo $lang['index-notification-button'];
				$notif = Notif::count_everything(" AND user_id = '{$current_user->id}' AND viewed = 0 ");
				if($notif) {
					echo "&nbsp;&nbsp;<span class='count-ajax-receptor' style='cursor:pointer'><span class='label label-danger'>{$notif}</span></span>";
				} else {
					echo "&nbsp;&nbsp;<span class='count-ajax-receptor' style='cursor:pointer'></span>";
				}
				?></a>
				
				<ul class="dropdown-menu" >
					<li class="dropdown-header" >
						<a href="<?php echo $url_mapper['notifications/']; ?>"><b><?php echo $lang['index-notification-see_all']; ?> ></b></a>
					</li>
					<ul class="dropdown-menu-list withScroll menu-ajax-receptor" data-height="220" style="width:400px;list-style-type:none;cursor:pointer">
						<?php 
							$notif = Notif::get_everything(" AND user_id = '{$current_user->id}' AND viewed = 0 ORDER BY created_at DESC LIMIT 8 ");
							if($notif) {
								foreach($notif as $n) {
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
									
									echo "<li style='padding:10px;color:black;border-bottom:1px solid #ededed' onclick=\"location.href='{$link}';\"><i class='fa fa-globe'></i>&nbsp;&nbsp;{$string}</li>";
								}
							} else {
						?>
						<h3 style="color:#b0b0b0"><center><i class="glyphicon glyphicon-bullhorn"></i><br><?php echo $lang['index-notification-no_notifications']; ?></center></h3><br><br>
						<?php } ?>
					</ul>
				</ul>
				
			</li><?php } ?>
			<?php if($current_user->can_see_this('admin.read', $group)) { ?><li <?php if($navsec == 'admin') { echo ' class="current" '; } ?> ><a href="<?php echo $url_mapper['admin/']; ?>"><i class="glyphicon glyphicon-wrench"></i>&nbsp;&nbsp;<?php echo $lang['index-admin-button']; ?></a></li> <?php } ?>
			<?php 
			
			if($current_user->id == '1000') {
				$user_avatar = WEB_LINK.'assets/img/avatar.png';
			?>
				<li><a href="<?php echo $url_mapper['login/']; ?>"><i class="glyphicon glyphicon-off"></i>&nbsp;&nbsp;<?php echo $lang['index-user-login']; ?></a></li>
			<?php
			} else {
				//global user avatar
				if($current_user->avatar) {
					$img = File::get_specific_id($current_user->avatar);
					$user_avatar = WEB_LINK."assets/".$img->image_path();
					$user_avatar_path = UPLOADPATH."/".$img->image_path();
					if (!file_exists($user_avatar_path)) {
						$user_avatar = WEB_LINK.'assets/img/avatar.png';
					}
				} else {
					$user_avatar = WEB_LINK.'assets/img/avatar.png';
				} ?>
				
			
			<li><a href="<?php echo $url_mapper['pages/view']; ?>about-us" class="visible-xs"><i class="fa fa-question-circle"></i>&nbsp;&nbsp;<?php echo $lang['pages-about-title']; ?></a></li>
			<li><a href="<?php echo $url_mapper['pages/view']; ?>contact-us" class="visible-xs"><i class="fa fa-comments-o"></i>&nbsp;&nbsp;<?php echo $lang['pages-contact-title']; ?></a></li>
			<li><a href="<?php echo $url_mapper['leaderboard/']; ?>" class="visible-xs"><i class="fa fa-trophy"></i>&nbsp;&nbsp;<?php echo $lang['pages-leaderboard-title']; ?></a></li>
			
			
			<li class="dropdown" ><a href="<?php echo $url_mapper['users/view']. $current_user->id; ?>/" class="dropdown-toggle" data-toggle="dropdown" id="dropdownMenu1" ><img src="<?php echo $user_avatar; ?>" class="img-circle" style="float:<?php echo $lang['direction-left']; ?>; height:30px; width:auto; margin-top:-4px; ">&nbsp;&nbsp;<?php echo $current_user->f_name . ' ' . strtoupper(substr($current_user->l_name,0,1)); ?></a>
			  <ul class="dropdown-menu" aria-labelledby="dropdownMenu1" >
				<?php if($current_user->can_see_this('admin.read', $group)) { ?><li><a href="<?php echo $url_mapper['admin/']; ?>"><?php echo $lang['index-user-admin']; ?></a></li><?php } ?>
				<li><a href="<?php echo $url_mapper['users/view'] . $current_user->id; ?>/"><?php echo $lang['index-user-profile']; ?></a></li>
				<li><a href="<?php echo $url_mapper['users/view'] . $current_user->id; ?>/section=edit&hash=<?php echo $random_hash; ?>"><?php echo $lang['index-user-settings']; ?></a></li>
				<li role="separator" class="divider"></li>
				<li><a href="<?php echo $url_mapper['logout/']; ?>"><?php echo $lang['index-user-logout']; ?></a></li>
			  </ul>
			</li>
			<?php } ?>
			
          </ul>
		  
		  <form action="<?php echo $url_mapper['questions/create'] ?>" method="post" role="form" enctype="multipart/form-data">
				<div class="input-group searchbox hidden-sm hidden-xs col-lg-5 col-md-3" style="">
				  <input type="text" name="title" class="searchbox-field form-control typeahead" placeholder="<?php echo $lang['index-search-title']; ?>" autofocus>
				  <span class="input-group-btn">
					<button class="btn btn-default" type="submit"><?php echo $lang['index-search-button']; ?>!</button>
				  </span>
				</div><!-- /input-group -->
				<?php 
					$_SESSION[$elhash] = $random_hash;
					echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>";
				?>
			</form>
			
		  
		  
        </div><!--/.nav-collapse -->
      </div>
	  	
	<?php 
	if($settings['site_status']== "0" && $current_user->prvlg_group == "1" ) {
		echo "<div class='col-md-12 ' style='background-color:#b92b27'><h4><center>Site Closed for public visitors, Accessible only by admins</center></h4></div>";
	}
	
	if(file_exists(ADMINPANEL.'/install/index.php')) {
		echo "<div class='col-md-12 ' style='background-color:#b92b27'><h4><center>Warning! installation folder detected.. You must delete it before using the script</center></h4></div>";
	}
	?>
    
	</nav>
	
	<div class="overlay" ></div>
	
	<form action="<?php echo $url_mapper['questions/create'] ?>" method="post" role="form" enctype="multipart/form-data" class="visible-xs visible-sm col-sm-12 " style="z-index:1111; clear:both;">
		<div class="input-group" style="z-index:1000">
		  <input type="text" name="title" class="searchbox-field form-control typeahead " placeholder="<?php echo $lang['index-search-title']; ?>" autofocus>
		  <span class="input-group-btn" style='z-index:1010;'>
			<button class="btn btn-default" type="submit"><?php echo $lang['index-search-button']; ?>!</button>
		  </span>
		</div><!-- /input-group -->
		<?php 
			$_SESSION[$elhash] = $random_hash;
			echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>";
		?>
	<br style="clear:both"><br style="clear:both">
	</form>
	