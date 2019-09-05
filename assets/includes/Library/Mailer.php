<?php
require_once("Loader.php");
require_once("mail_template/class.phpmailer.php");
require_once("mail_template/phpmailer.lang-en.php");

class Mailer {
	
		public static function send_mail_to($receiver, $receiver_name, $msg, $title) {
		global $db;
		
		$to = $db->escape_value($receiver);
		$to_name = $db->escape_value($receiver_name);
		$link = WEB_LINK;
		
		$logo_link = UPL_FILES.'/upl_files';

		$from = "no-reply@" .   str_replace('www.', '', $_SERVER['HTTP_HOST']);

		$mail= new PhpMailer();

		$mail->CharSet="windows-1256";
		$mail->Subject = iconv('windows-1256', 'utf-8', $title);

		$mail->FromName = 'Pearls! Questions & Answers platform';
		$mail->From = $from;
		$mail->AddAddress($to,$to_name);

		$body = file_get_contents(LIBRARY.'/mail_template/template1.html');

		$body = str_replace('[logo_link]', $logo_link, $body);
		$body = str_replace('[msg_header]', $title, $body);
		$body = str_replace('[msg_body]', $msg, $body);
		$body = str_replace('[msg_link]', WEB_LINK , $body);
		
		$mail->Body = $body;
		$mail->AltBody = $msg;

		$mail->IsHTML(true);
		return $mail->Send();
		}



	}

?>
