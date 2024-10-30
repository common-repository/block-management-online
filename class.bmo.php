<?php
/*
Copyright (c) 2013 Esscotti Ltd, All Rights Reserved
http://www.blockmanagementonline.com/ - Block Management Online
*/
class bmo {
	
	
	public function get_bmo_document($permalink) {
		global $bmo_session;

		$url = bmo_options::get_option('bmo_portal_url').'documents/view.php';
		if (!empty($_GET['block'])) {
			$url .= '?block='.$_GET['block'];
			$ampersand = true;
		}
		if (!empty($_GET['permalink'])) {
			if ($ampersand === true) {
				$url .= '&';
			} else {
				$url .= '?';
			}
			$url .= 'permalink='.$_GET['permalink'];
		}

		$ch = curl_init();							// init curl			
		curl_setopt($ch, CURLOPT_URL, $url);		// Set the URL to work with	
		curl_setopt($ch, CURLOPT_HEADER, 1);		
		curl_setopt($ch, CURLOPT_COOKIEFILE, "");
		curl_setopt($ch, CURLOPT_COOKIE,  $bmo_session->bmo_cookies);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$store = curl_exec($ch);	// execute the request (the login)
		curl_close($ch);	

		preg_match_all('|Set-Cookie: (.*);|U', $store, $results);   
		$bmo_session->bmo_cookies = implode(';', $results[1]);
		$bmo_session->save_session_vars();
		
		$document = array();
		$document['filename'] = 'filename.pdf';
		if (($pos = stripos($store, "\r\n\r\n")) === false) {
			$document['content'] = $store;
		} else {
			$document['content'] = substr($store, $pos+4);
		}	
		return $document;
	}
	public function get_bmo_statement($property) {
		global $bmo_session;

		$url = bmo_options::get_option('bmo_portal_url').'accounting/statement.php';
		if (!empty($_GET['block'])) {
			$url .= '?block='.$_GET['block'];
			$ampersand = true;
		}
		if (!empty($_GET['property'])) {
			if ($ampersand === true) {
				$url .= '&';
			} else {
				$url .= '?';
			}
			$url .= 'property='.$_GET['property'];
		}

		$ch = curl_init();							// init curl			
		curl_setopt($ch, CURLOPT_URL, $url);		// Set the URL to work with	
		curl_setopt($ch, CURLOPT_HEADER, 1);		
		curl_setopt($ch, CURLOPT_COOKIEFILE, "");
		curl_setopt($ch, CURLOPT_COOKIE,  $bmo_session->bmo_cookies);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$store = curl_exec($ch);	// execute the request (the login)
		curl_close($ch);	

		preg_match_all('|Set-Cookie: (.*);|U', $store, $results);   
		$bmo_session->bmo_cookies = implode(';', $results[1]);
		$bmo_session->save_session_vars();
		
		$document = array();
		$document['filename'] = 'filename.pdf';
		if (($pos = stripos($store, "\r\n\r\n")) === false) {
			$document['content'] = $store;
		} else {
			$document['content'] = substr($store, $pos+4);
		}	
		return $document;
	}
	public function get_bmo_invoice($permalink) {
		global $bmo_session;

		$url = bmo_options::get_option('bmo_portal_url').'accounting/invoice.php';
		if (!empty($_GET['block'])) {
			$url .= '?block='.$_GET['block'];
			$ampersand = true;
		}
		if (!empty($_GET['permalink'])) {
			if ($ampersand === true) {
				$url .= '&';
			} else {
				$url .= '?';
			}
			$url .= 'permalink='.$_GET['permalink'];
		}

		$ch = curl_init();							// init curl			
		curl_setopt($ch, CURLOPT_URL, $url);		// Set the URL to work with	
		curl_setopt($ch, CURLOPT_HEADER, 1);		
		curl_setopt($ch, CURLOPT_COOKIEFILE, "");
		curl_setopt($ch, CURLOPT_COOKIE,  $bmo_session->bmo_cookies);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$store = curl_exec($ch);	// execute the request (the login)
		curl_close($ch);	

		preg_match_all('|Set-Cookie: (.*);|U', $store, $results);   
		$bmo_session->bmo_cookies = implode(';', $results[1]);
		$bmo_session->save_session_vars();
		
		$document = array();
		$document['filename'] = 'filename.pdf';
		if (($pos = stripos($store, "\r\n\r\n")) === false) {
			$document['content'] = $store;
		} else {
			$document['content'] = substr($store, $pos+4);
		}	
		return $document;
	}
	
	
	public function bmo_ajax_action() {
		global $bmo_session;
		
		if ($bmo_session->login_session_ok) {

			switch ($_GET['pp']) {
			case 'documents/view':	// view pdf document
				$permalink = $_GET['permalink'];
				$document = $this->get_bmo_document($permalink);
				
				if (($pos = strrpos($document['filename'], '.')) !== false) {
					$extension = strtolower(substr($document['filename'], $pos));
				} else {
					$extension = '';
				}
				switch ($extension) {
				case ".png":
					$mimetype = "image/png";
					break;
				case ".jpg":
				case ".jpeg":
					$mimetype = "image/jpeg";
					break;
				case ".gif":
					$mimetype = "image/gif";
					break;
				case ".pdf":
					$mimetype = "application/pdf";
					break;
				case ".doc":
					$mimetype = "application/msword";
					break;
				case ".xls":
					$mimetype = "application/vnd.ms-excel";
					break;
				case ".txt":
					$mimetype = "text/plain";
					break;
				case ".zip":
					$mimetype = "application/zip";
					break;
				default: 
					$mimetype = "application/octet-stream";
				}

				header('Content-Type: '.$mimetype);
//				header('Content-Disposition: attachment; filename="'.$document['filename'].'"');
				header('Content-Disposition: inline; filename="'.$document['filename'].'"');
//				header('Content-Length: '.strlen($document['filename']));
				
				echo $document['content'];
				break;
			case 'accounting/statement':	// view pdf statement
				$property = $_GET['property'];
				$document = $this->get_bmo_statement($property);
				
				if (($pos = strrpos($document['filename'], '.')) !== false) {
					$extension = strtolower(substr($document['filename'], $pos));
				} else {
					$extension = '';
				}
				$mimetype = "application/pdf";

				header('Content-Type: '.$mimetype);
//				header('Content-Disposition: attachment; filename="'.$document['filename'].'"');
				header('Content-Disposition: inline; filename="'.$document['filename'].'"');
//				header('Content-Length: '.strlen($document['filename']));
				
				echo $document['content'];
				break;
			case 'accounting/invoice':	// view pdf invoice
				$permalink = $_GET['permalink'];
				$document = $this->get_bmo_invoice($permalink);
				
				if (($pos = strrpos($document['filename'], '.')) !== false) {
					$extension = strtolower(substr($document['filename'], $pos));
				} else {
					$extension = '';
				}
				$mimetype = "application/pdf";

				header('Content-Type: '.$mimetype);
//				header('Content-Disposition: attachment; filename="'.$document['filename'].'"');
				header('Content-Disposition: inline; filename="'.$document['filename'].'"');
//				header('Content-Length: '.strlen($document['filename']));
				
				echo $document['content'];
				break;
			case 'cmd3':		// ajax command example 3
				$param1 = $_GET['param1'];
				echo json_encode($param1);
				break;
			default:
				echo "document not found";
				die;
				break;
			}
			die;
		} else {
			echo "User not logged in";
			die;
		}
		
	}
	
	function __construct(){
	
		// Setup the event handler for marking this post as read for the current user
		add_action('wp_ajax_bmo_ajax_action', array($this, 'bmo_ajax_action'));
		add_action('wp_ajax_nopriv_bmo_ajax_action', array($this, 'bmo_ajax_action'));

		if (!empty($_GET['document'])) {
			$document_url = admin_url('admin-ajax.php');
			switch ($_GET['pp']) {
			case 'documents/view': 
				$url = $document_url.'?action=bmo_ajax_action&pp='.$_GET['pp'].'&permalink='.$_GET['permalink'];
				break;
			case 'accounting/statement':
				$url = $document_url.'?action=bmo_ajax_action&pp='.$_GET['pp'].'&property='.$_GET['property'];
				break;
			case 'accounting/invoice':
				$url = $document_url.'?action=bmo_ajax_action&pp='.$_GET['pp'].'&permalink='.$_GET['permalink'];
				break;
			}
			header ('Location: '.$url);
			exit;
		}

	}

	function view($area, $page) {
		global $bmo_session;		
		
		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		}
		if (isset($_GET['area'])) {
			$area = $_GET['area'];
		}

//		if (!$bmo_session->login_session_ok) {
//			return self::get_page('login', array(), $area);
//		}
		
		$templatevars = array();
		$templatevars['login_session_ok'] = $bmo_session->login_session_ok;
		$templatevars['sessionvars'] = $bmo_session->sessionvars;
		$templatevars['portalpage'] = $_GET['pp'];

//		if (empty($page)) {
			$html = self::get_page('bmo', $templatevars, '');
//		} else {
//			$html = self::get_page($page, $templatevars, $area);
//		}

		return $html;
	}

	public static function templatepath() {
		return plugin_dir_path(__FILE__).'templates/';
	}

	public static function get_page($templatename, $templatevars=array(), $path) {
		global $bmo_session;
		
		if (empty($templatename)) {
			$templatename = 'index';
		}
		$path = strtr($path, '.', '');
		if (!empty($path)) {
			$path .= $path . '/';
		}
		$filename = self::templatepath() . $path . basename($templatename) . '.php';
		$templatevars['templatename'] = $filename;
		if (!file_exists($filename)) {
			$filename = self::templatepath() . 'errorpage.php';
		}

		$plugloc = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));

		$usermessage = '';
		foreach ($bmo_session->usermessages as $message) {
			switch ($message['level']) {
			case E_ERROR:
				$usermessage .= '<p class="error-message">Error: ';
				break;
			case E_WARNING:
				$usermessage .= '<p class="warning-message">Warning: ';
				break;
			case E_NOTICE:
				$usermessage .= '<p class="notice-message"> ';
				break;
			default:
				$usermessage .= '<p>';
				break;
			}
			$usermessage .= $message['message'];
			$usermessage .= '</p>';
		}
		
		ob_start();
		include($filename);
		$template = ob_get_contents();
		ob_end_clean();
		return $template;
	}
	
	
}




?>