<?php
/*
Copyright (c) 2013 Esscotti Ltd, All Rights Reserved
http://www.blockmanagementonline.com/ - Block Management Online
*/
class bmo_session {

	public $login_session_ok;
	public $sessionvars;
	public $bmo_cookies;
	
	public $usermessages;
	
	function __construct(){
	
		$this->sessionvars = array();
		$this->usermessages = array();

		$action = $_GET['action'];
	
		add_action('init', array($this, 'update_session'));

	}
	
	public function do_logout($redirect=true) {
		global $bmo_options;
		$session_max_age = $bmo_options->get_option('bmo_session_timeout_seconds');
		setcookie('bmo_session', '', time()-$session_max_age, "/");
		$this->login_session_ok = false;
		if ($redirect) {
			header ('Location: '.$bmo_options->get_option('bmo_logout_url'));
			exit;
		}
	}
	
	public function do_login($redirect = true) {
		global $wpdb, $bmo_options;

		srand((double)microtime()*1000000); 
		$accountid  = rand(1000000, 9999999);	// just pick a random account id...
		$password 	= $_POST['auth_password'];
		
		$session_max_age = $bmo_options->get_option('bmo_session_timeout_seconds');

		// Check if account exists for specified account reference... 
		if ($this->check_login($accountid, $password) === false) {
			$this->log('Sign in failed. E-mail or password not recognised.');
			return false;
		}

		$this->sessionvars['accountid'] 	= $accountid;
		$this->sessionvars['cookies'] 		= $this->bmo_cookies;
		
		// Login! Create a new session and delete any old user sessions.  
		srand((double)microtime()*1000000);   // Session ID is a random number...
		$sessionid = rand(1000000, 9999999);
		
		// insert new session...
		$query = $wpdb->prepare("insert into ".$wpdb->prefix."bmo_user_sessions set sessionvars=%s,sessionid=%s,accountid=%s", $this->slw_serial($this->sessionvars), $sessionid, $accountid);
		if ($wpdb->query($query) === false) {
			$this->log('Sign in failed. Failed to create new session.');
			setcookie('bmo_session', '', time()-$session_max_age, '/');
			return false;
		}
		
		if (setcookie('bmo_session', $sessionid, time()+$session_max_age, '/') === false) {
			$this->log("setcookie failed");
		}
			
		$this->login_session_ok = true;
		
		if ($redirect) {
			header ('Location: '.$bmo_options->get_option('bmo_login_url'));
			exit;
		}
	}

	public function save_session_vars() {
		global $wpdb;
		$this->sessionvars['cookies'] 		= $this->bmo_cookies;
		$query = $wpdb->prepare("update ".$wpdb->prefix."bmo_user_sessions set sessionvars=%s where accountid=%s", $this->slw_serial($this->sessionvars), $this->sessionvars['accountid']);
		if ($wpdb->query($query) === false) {
			return false;
		} else {
			return true;
		}
	}
	
	public function check_login($accountid, $password) {
		return true;
	}
	
	public function update_session() {
		global $wpdb, $bmo_options;
		
		$session_max_age = $bmo_options->get_option('bmo_session_timeout_seconds');

		// housekeeping: delete any previous sessions...
		$query = $wpdb->prepare("delete from ".$wpdb->prefix."bmo_user_sessions where UNIX_TIMESTAMP(timestamp)<%d", time()-$session_max_age);
		$wpdb->query($query);
		
		// Retrieve account information specified by cookie from database...
		$this->login_session_ok = false;
		$sessionid	= intval($_COOKIE['bmo_session']);
		

		// if no session cookie has been created before then create a random session... (the session will store the BMO cookies)
		if (empty($sessionid)) {
//echo "creating session";
			srand((double)microtime()*1000000); 
			$accountid  = rand(1000000, 9999999);	// just pick a random account id...
			$sessionid = rand(1000000, 9999999);

			$this->sessionvars['accountid'] 	= $accountid;
			$this->sessionvars['cookies'] 		= $this->bmo_cookies;
			
			// insert new session...
			$query = $wpdb->prepare("insert into ".$wpdb->prefix."bmo_user_sessions set sessionvars=%s,sessionid=%s,accountid=%s", $this->slw_serial($this->sessionvars), $sessionid, $accountid);
			if ($wpdb->query($query) === false) {
				$this->log('Sign in failed. Failed to create new session.');
				setcookie('bmo_session', '', time()-$session_max_age, '/');
				return false;
			}
			
			if (setcookie('bmo_session', $sessionid, time()+$session_max_age, '/') === false) {
				$this->log("setcookie failed");
			}
		}
	
//echo "using session id ".$sessionid;
		

		// Check session is up to date and ok...
		$query = $wpdb->prepare("select now()-timestamp as diff,sessionvars from ".$wpdb->prefix."bmo_user_sessions where sessionid=%d", $sessionid);
		$dbsession = $wpdb->get_results($query);
		
		if (($dbsession === null) || ($wpdb->num_rows !== 1)) {
			$query = $wpdb->prepare("insert into ".$wpdb->prefix."bmo_user_sessions set sessionvars=%s,sessionid=%s,accountid=%s", $this->slw_serial($this->sessionvars), $sessionid, $accountid);
			if ($wpdb->query($query) === false) {
				$this->log('Sign in failed. Failed to create new session.');
				setcookie('bmo_session', '', time()-$session_max_age, '/');
				return false;
			}
			// Check session is up to date and ok...
			$query = $wpdb->prepare("select now()-timestamp as diff,sessionvars from ".$wpdb->prefix."bmo_user_sessions where sessionid=%d", $sessionid);
			$dbsession = $wpdb->get_results($query);
		}

		$row = $dbsession[0];
		
		if (!empty($row->sessionvars)) {
			$this->sessionvars = $this->slw_unserial($row->sessionvars);
			if (!empty($this->sessionvars['cookies'])) {
				$this->bmo_cookies = $this->sessionvars['cookies'];
			}
		} else {
			$this->sessionvars = array();
		}
		
		// Check that session isn't too old match...
		if ($row->diff > $session_max_age) {
			$this->log('You have been logged out due to inactivity. Please log in again.');
			
			$query = $wpdb->prepare("delete from ".$wpdb->prefix."bmo_user_sessions where sessionid=%s", $sessionid);
			$wpdb->query($query);			
			
			$this->login_session_ok = false;
			return false;
		}
		
		$query = "update ".$wpdb->prefix."bmo_user_sessions set timestamp=now() where sessionid=".intval($sessionid);
		if ($wpdb->query($query) === false) {
			$this->log('Failed to update session: '.$sessionid);
			$this->login_session_ok = false;
			return false;
		}
		setcookie('bmo_session', $sessionid, time()+$session_max_age, "/"); //, 'www.bmoportal.co.uk');					
		$this->login_session_ok = true;
		return true;
	}
	
	/* Use as loglevel: E_ERROR, E_WARNING, E_NOTICE */
	public function log($msg, $level=E_NOTICE) {
		$logmsg = date('Y-m-d H:i:s');
		switch ($level) {
		case E_ERROR:
			$logmsg .= ' (error)';
			break;
		case E_WARNING:
			$logmsg .= ' (warning)';
			break;
		case E_NOTICE:
			$logmsg .= ' (notice)';
			break;
		default:
			$logmsg .= ' (unknown)';
			break;
		}
		
		$logmsg .= ': '. $msg;
		
		$this->usermessages[] = array('level'=>$level, 'message'=>$msg);
	}
	
	public function slw_serial ( $var = array(), $recur = FALSE ) {
	    if ( $recur ) {
	        foreach ( $var as $k => $v )
	        {
	            if ( is_array($v) ) {
	                $var[$k] = $this->slw_serial($v, 1);
	            } else {
	                $var[$k] = base64_encode($v);
	            }
	        }
	        return $var;
	    } else {
	        return serialize($this->slw_serial($var, 1));
	    }
	}
	   
	public function slw_unserial ( $var = FALSE, $recur = FALSE ) {
	    if ( $recur ) {
			if (is_array($var)) {
				foreach ( $var as $k => $v )
				{
					if ( is_array($v) ) {
						$var[$k] = $this->slw_unserial($v, 1);
					} else {
						$var[$k] = stripslashes(base64_decode($v));
					}
				}
			} 
	        return $var;
	    } else {
	        return $this->slw_unserial(unserialize($var), 1);
	    }
	}
	
}




?>