
<div class="col-md-2 hidden-sm hidden-xs">
	<i class="glyphicon glyphicon-tasks"></i>&nbsp;&nbsp;<?php echo $lang['index-sidebar-feeds']; ?>
	<hr>
	<ul class="feed-ul">
		<?php
			$current = '';
			if(!isset($_GET['feed']) || $_GET['feed'] == '' ) {
				$current = 'current';
			}
		?>
		<li><a href="<?php echo $url_mapper['index/']; ?>" class="<?php echo $current; ?> col-md-12"><?php echo $lang['index-sidebar-top']; ?></a></li>
		<li>&nbsp;</li>
		<center><b><?php echo $lang['index-sidebar-trending']; ?></b></center>
		<?php $tags = Tag::get_trending(' LIMIT 5 ');
			if($tags) {
				foreach($tags as $tag) {
					$current = '';
					if(isset($_GET['feed']) && $_GET['feed'] != '' ) {
						if($_GET['feed'] == $tag->name ) {
							$current = 'current';
						}
					}
			?>
				<li><a href="<?php echo $url_mapper['feed/'] . $tag->name; ?>/" class="<?php echo $current; ?> col-md-12"><?php echo $tag->name; ?></a></li>
			<?php
				}
			}
		?>
		<li>&nbsp;</li>
		<center><b><?php echo $lang['index-sidebar-subscriptions']; ?></b></center>
		<?php $tags = FollowRule::get_subscriptions('tag',$current_user->id , 'user_id' , 'LIMIT 20');
			if($tags) {
				foreach($tags as $tag) {
					$tag = Tag::get_specific_id($tag->obj_id);
					$current = '';
					if(isset($_GET['feed']) && $_GET['feed'] != '' ) {
						if($_GET['feed'] == $tag->name ) {
							$current = 'current';
						}
					}
			?>
				<li><a href="<?php echo $url_mapper['feed/'] . $tag->name; ?>/" class="<?php echo $current; ?> col-md-12"><?php echo $tag->name; ?></a></li>
			<?php
				}
			}
		?>
	</ul>
	
	<?php if(isset($admanager2->value) && $admanager2->value != '' && $admanager2->value != '&nbsp;' ) { echo '<br style="clear:both"><hr>'.str_replace('\\','',$admanager2->value); } ?>
	</div>
	