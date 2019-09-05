<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="<?php echo WEB_LINK; ?>assets/js/vendor/jquery.min.js"><\/script>')</script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="<?php echo WEB_LINK; ?>assets/plugins/typeahead/typeahead.js"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="<?php echo WEB_LINK; ?>assets/js/ie10-viewport-bug-workaround.js"></script>
<script type="text/javascript">var PATH = '<?php echo WEB_LINK; ?>';</script>
<script src="<?php echo WEB_LINK; ?>assets/plugins/Emoji/jquery.emotions.js"></script>
<script src="<?php echo WEB_LINK; ?>assets/plugins/quickfit/jquery.quickfit.js"></script>
<script src="<?php echo WEB_LINK; ?>assets/js/jquery.slugit.js"></script>

<script type="text/javascript">
		
		/*$(".searchbox-field").focusin(function() {
			$(".overlay").fadeIn(100);
		});*/

		$(".searchbox-field").focusout(function() {
			$(".overlay").fadeOut(100);
		});

var fittedwidth = $('.title').width();
$('.quickfit').quickfit({ max: 22, min: 15, width: fittedwidth, truncate: false});
$('.typeahead').typeahead({
  hint: true,
  highlight: true,
  minLength: 2
},{
    name: 'title',
	displayKey: 'full',
	source: function (query, process) {
		$.ajax({
			url: '<?php echo WEB_LINK; ?>assets/includes/one_ajax.php?type=q_suggestions',
			type: 'POST',
			dataType: 'JSON',
			data: 'id=<?php echo $current_user->id; ?>&data=' + query + '&hash="<?php echo $random_hash; ?>"',
			success: function(data) {
				process(data);
				$(".overlay").fadeIn(100);
			},
			error: function(data) {
				console.log(data);
			}
		});
	}
}).on('typeahead:selected', function (obj, datum) {
	
	if(datum.length !== 0 && datum.slug !== '' ) {
		window.location.href = '<?php echo $url_mapper['questions/view']; ?>' + datum.slug;
	}
});

$('.typeahead').focus();

function scrollToAnchor(aid){
    var aTag = $("a[name='"+ aid +"']");
    $('html,body').animate({scrollTop: aTag.offset().top},'slow');
}
function scrollToId(aid){
	var aid = aid.split('#')[1];
    var aTag = $("[id='"+ aid +"']");
    $('html,body').animate({scrollTop: eval(aTag.offset().top - 50)},'slow');
}

$('.col-md-9').emotions();
$('.modal-body').emotions();

$('.open_div').click(function() {
	var link = $(this).data('link');
	$('#'+link).modal('show')
});
$('.open_link').click(function() {
	var link = $(this).data('link');
	window.location.href = link;
});

<?php if ($session->is_logged_in() == true ) { ?>
setInterval(function() {
		$.post("<?php echo WEB_LINK; ?>assets/includes/one_ajax.php?type=check_notifications", {id: 1 , data: 1,  hash:'<?php echo $random_hash; ?>'}, function(data){
			
			var test = $.parseJSON(data);
			if(test.count) {
				$(".count-ajax-receptor").html("<span class='label label-danger'>"+ test.count +"</span>");
			}
			if(test.menu) {
				$(".menu-ajax-receptor").html('');
				$.each( test.menu , function( i, l ){
					$(".menu-ajax-receptor").append("<li style='padding:10px;color:black;border-bottom:1px solid #ededed;cursor:pointer' onclick=\"location.href='"+ test.menu[i].link +"';\"><i class='fa fa-globe'></i>&nbsp;&nbsp;"+ test.menu[i].string +"</li>");
				});
			}
		});
}, 4000);
<?php } ?>
</script>