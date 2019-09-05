<?php require_once("Library/Loader.php");
if(filesize($parent.'/config.php') == '0') { redirect_to('install/index.php'); }
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
if(!isset($settings['site_lang'])) { $settings['site_lang'] = 'English'; }
require_once($current."/lang/lang.{$settings['site_lang']}.php");
?>