<script type="text/javascript">
$('.question-like-machine').on('click' , '.upvote' , function() {
	var id = $(this).attr('name');
	var count = $(this).attr('value');
	count++;
	$(this).attr('value', count);
	$(this).html('<i class="glyphicon glyphicon-thumbs-up"></i> ' + $(this).data('lbl-active') + ' | ' + count);
	$(this).removeClass('upvote');
	$(this).addClass('active');
	$(this).closest('div').find("a.downvote").addClass('disabled');
	$(this).addClass('undo-upvote');
	$.post("<?php echo WEB_LINK; ?>assets/includes/one_ajax.php?type=like", {id:id, data: $(this).data('obj') , hash:'<?php echo $random_hash; ?>'}, function(){});
});
$('.question-like-machine').on('click' , '.undo-upvote' , function() {
	var id = $(this).attr('name');
	var count = $(this).attr('value');
	count--;
	$(this).attr('value', count);
	$(this).html('<i class="glyphicon glyphicon-thumbs-up"></i> ' + $(this).data('lbl') + ' | ' + count);
	$(this).addClass('upvote');
	$(this).removeClass('active');
	$(this).closest('div').find("a.downvote").removeClass('disabled');
	$(this).removeClass('undo-upvote');
	$.post("<?php echo WEB_LINK; ?>assets/includes/one_ajax.php?type=unlike", {id:id, data: $(this).data('obj') , hash:'<?php echo $random_hash; ?>'}, function(){});
});

$('.question-like-machine').on('click' , '.downvote' , function() {
	var id = $(this).attr('name');
	var count = $(this).attr('value');
	count++;
	$(this).attr('value', count);
	$(this).html('<i class="glyphicon glyphicon-thumbs-down"></i> ' + $(this).data('lbl-active') + ' | ' + count);
	$(this).removeClass('downvote');
	$(this).addClass('active');
	$(this).closest('div').find("a[class*='upvote']").addClass('disabled');
	$(this).addClass('undo-downvote');
	$.post("<?php echo WEB_LINK; ?>assets/includes/one_ajax.php?type=dislike", {id:id, data: $(this).data('obj') , hash:'<?php echo $random_hash; ?>'}, function(){});
});
$('.question-like-machine').on('click' , '.undo-downvote' , function() {
	var id = $(this).attr('name');
	var count = $(this).attr('value');
	count--;
	$(this).attr('value', count);
	$(this).html('<i class="glyphicon glyphicon-thumbs-down"></i> ' + $(this).data('lbl') + ' | ' + count);
	$(this).addClass('downvote');
	$(this).removeClass('active');
	$(this).closest('div').find("a.upvote").removeClass('disabled');
	$(this).removeClass('undo-downvote');
	$.post("<?php echo WEB_LINK; ?>assets/includes/one_ajax.php?type=undislike", {id:id, data: $(this).data('obj') , hash:'<?php echo $random_hash; ?>'}, function(){});
});

$('.question-like-machine, .name').on('click' , '.follow' , function() {
	var id = $(this).attr('name');
	var count = $(this).attr('value');
	count++;
	$(this).attr('value', count);
	$(this).html('<i class="fa fa-user-plus"></i> ' + $(this).data('lbl-active') + ' | ' + count);
	$(this).removeClass('follow');
	$(this).addClass('active');
	$(this).addClass('unfollow');
	$.post("<?php echo WEB_LINK; ?>assets/includes/one_ajax.php?type=follow", {id:id, data: $(this).data('obj') , hash:'<?php echo $random_hash; ?>'}, function(){});
});

$('.question-like-machine, .name').on('click' , '.unfollow' , function() {
	var id = $(this).attr('name');
	var count = $(this).attr('value');
	count--;
	$(this).attr('value', count);
	$(this).html('<i class="fa fa-user-plus"></i> ' + $(this).data('lbl') + ' | ' + count);
	$(this).addClass('follow');
	$(this).removeClass('active');
	$(this).removeClass('unfollow');
	$.post("<?php echo WEB_LINK; ?>assets/includes/one_ajax.php?type=unfollow", {id:id, data: $(this).data('obj') , hash:'<?php echo $random_hash; ?>'}, function(){});
});
</script>
