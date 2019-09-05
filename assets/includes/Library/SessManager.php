<?php
ini_set('max_execution_time', 300);

class SessManager {
	
	private $login_confirm = false;
	public $admin_id;
	public $email;
	public $message;
	
	function __construct() {
		//session_start();
		$this->check_login();
		$this->check_message();
    if($this->login_confirm) {
        //session_regenerate_id(true);
    } else {
      // actions to take right away if user is not logged in
    }
	}
	
  public function is_logged_in() {
    return $this->login_confirm;
  }
  
  
	public function login($user) {
    // database should find user based on username/password
    if($user){
      $this->admin_id = $_SESSION['elidbta3elbanyadamellyda5elelalo7etelta7akom'] = $user->id; 
	  //$_SESSION['chatusername'] = preg_replace('#\W#', '', $user->username);
      $this->login_confirm = true;
	   }
	}
  
 	public function logout() {
    unset($_SESSION['elidbta3elbanyadamellyda5elelalo7etelta7akom']);
    unset($this->admin_id);
    $this->login_confirm = false;
  }

	public function message($msg="") {
	  if(!empty($msg)) {
	    $_SESSION['doctorsays'] = $msg;
	  } else {
			return $this->message;
	  }
	}

	private function check_login() {
    if(isset($_SESSION['elidbta3elbanyadamellyda5elelalo7etelta7akom'])) {
      $this->admin_id = $_SESSION['elidbta3elbanyadamellyda5elelalo7etelta7akom'];
      $this->login_confirm = true;
    } else {
      unset($this->admin_id);
      $this->login_confirm = false;
    }
  }
  
	private function check_message() {
		if(isset($_SESSION['doctorsays'])) {
      $this->message = $_SESSION['doctorsays'];
      unset($_SESSION['doctorsays']);
    } else {
      $this->message = "";
    }
	}
	
}

$session = new SessManager();

?>