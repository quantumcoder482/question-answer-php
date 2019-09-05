<br><br><div class="master-footer">
	<a href="http://michael-designs.com" target="_blank">Michael Designs </a>&copy; 2017
</div>	
<?php if(isset($analytics_info) && is_array($analytics_info) ) { ?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', '<?php echo $analytics_info['UA']; ?>', 'auto');
  ga('send', 'pageview');
</script>
<?php } if(isset($addthis_info) && is_array($addthis_info) ) { ?>
<!-- Go to www.addthis.com/dashboard to customize your tools --> <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo $addthis_info['ra']; ?>"></script> 
<?php } ?>
