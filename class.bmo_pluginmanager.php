<?php
/*
Copyright (c) 2013 Esscotti Ltd, All Rights Reserved
http://www.blockmanagementonline.com/ - Block Management Online
*/

$bmo_db_version = "1.0";

class bmo_pluginmanager {

	function __construct(){
		add_action('init', array($this, 'add_header_code'));		
		add_action('wp_head', array($this, 'add_ajaxurl'));	
	}
	
	

	public function add_ajaxurl() { 
		$html = '<script type="text/javascript">';
		$html .= 'var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '";';
		$html .= 'var nonce = "'.wp_create_nonce( 'bmo_ajax_action-nonce' ).'";';
		$html .= '</script>';
		echo $html;
	}
	
	function add_header_code() {
		
		wp_enqueue_script('jquery');
		// wp_enqueue_script('jquery-ui-core');
		// wp_enqueue_script('jquery-ui-button');
		// wp_enqueue_script('jquery-ui-datepicker');
		// wp_enqueue_script('jquery-ui-dialog');

		$plugloc = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
		wp_enqueue_script('bmo-script', $plugloc.'js/bmo.js');
		if (!is_admin()) {  
			wp_enqueue_style('bmo-style', $plugloc.'css/styles.css');
		}
	}
	
	function add_footer_code() {

	}

	public function install_plugin () {
		global $wpdb;

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		$sql = "CREATE TABLE ".$wpdb->prefix."bmo_user_sessions (
				id INT unsigned NOT NULL AUTO_INCREMENT,
				`sessionvars` text,
				`sessionid` varchar(45) DEFAULT NULL,
				`accountid` varchar(45) DEFAULT NULL,
				`timestamp` timestamp NULL DEFAULT NULL,
				UNIQUE KEY id (id)
			);";


		dbDelta($sql);
		
		add_option("bmo_db_version", $bmo_db_version);
	}
	
	
}
	
?>