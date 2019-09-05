<div class="col-md-2 hidden-sm hidden-xs">
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
		
		<?php if($current_user->id != '1000') { ?>
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