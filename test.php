<?php
$str ='<p><a href="http://localhost/sandbox/pearls/users/1002/" class="mentionned" target="_blank">@test</a>&nbsp;<a href="http://localhost/sandbox/pearls/users/1001/" class="mentionned" target="_blank">@michael2</a>&nbsp;@raalspd</p>';

preg_match_all('/(^|\s|&nbsp;)(@\w+)/', strip_tags($str), $mentions);
$results = array_unique($mentions[0]);
print_r($results);
