<?php
//URL MAPPER
$url_mapper = array();
$url_mapper['error/404/'] = WEB_LINK.'error/404/';
$url_mapper['login/'] = WEB_LINK.'login/';
$url_mapper['logout/'] = WEB_LINK.'logout/';
$url_mapper['index/'] = WEB_LINK.'index/';
$url_mapper['feed/'] = WEB_LINK.'feed/';
$url_mapper['search/'] = WEB_LINK.'search/';
$url_mapper['notifications/'] = WEB_LINK.'notifications/';
$url_mapper['leaderboard/'] = WEB_LINK.'leaderboard/';
$url_mapper['admin/'] = WEB_LINK.'admin/';

$url_mapper['questions/create'] = WEB_LINK. 'post/create/';
$url_mapper['questions/update'] = WEB_LINK. 'post/update/';
$url_mapper['questions/approve'] = WEB_LINK. 'questions/approve/';

$url_mapper['pages/view'] = WEB_LINK. 'page/';

$url_mapper['questions/view'] = WEB_LINK. 'questions/read/';
$url_mapper['questions/delete'] = WEB_LINK. 'post/delete/';

$url_mapper['answers/edit'] = WEB_LINK. 'questions/read/';
$url_mapper['answers/delete'] = WEB_LINK. 'questions/read/';
$url_mapper['answers/approve'] = WEB_LINK. 'questions/read/';

$url_mapper['users/create'] = WEB_LINK. 'users/create/';
$url_mapper['users/update'] = WEB_LINK. 'users/';
$url_mapper['users/view'] = WEB_LINK. 'users/';
$url_mapper['users/delete'] = WEB_LINK. 'users/';

?>