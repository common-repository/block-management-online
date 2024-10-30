<?php
/*
Copyright (c) 2013 Esscotti Ltd, All Rights Reserved
http://www.blockmanagementonline.com/ - Block Management Online
*/
class bmo_options {

	function __construct(){

		add_action('admin_menu', array($this, 'options_page'));
		
	}
	
	function options_page() {
		if (function_exists('add_options_page')) {
			add_options_page('Block Management Online', 'Block Management Online', 10, 'block-management-online', array($this, 'options_subpanel'));
		}
	}

	function options_subpanel() {
		global $wp_version;
		$plugloc = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
		
		if (isset($_POST['update_options'])) {
			
			if ( function_exists('check_admin_referer') ) {
				check_admin_referer('action_options');
			}

			if ($_POST['bmo_portal_name'] != "")  {
				update_option('bmo_portal_name', stripslashes(strip_tags($_POST['bmo_portal_name'])));
			}
			if ($_POST['bmo_portal_url'] != "")  {
				update_option('bmo_portal_url', stripslashes(strip_tags($_POST['bmo_portal_url'])));
			}

			echo '<div class="updated"><p>Options saved.</p></div>';
		}

	 ?>
		<div class="wrap">
		<h2>Block Management Online Options</h2>

		<a target="_blank" href="http://www.blockmanagementonline.com/"><img alt="Block Management Online" title="Block Management Online" align="right" src="<?php echo $plugloc; ?>/img/admin-page-badge.png"/></a>

		<form name="ft_main" method="post">
	<?php
		if (function_exists('wp_nonce_field')) {
			wp_nonce_field('action_options');
		}
		$plugloc = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	?>
		<h3>General settings</h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="bmo_portal_name">Name of portal</label></th>
				<td><input name="bmo_portal_name" type="text" id="bmo_portal_name" value="<?php echo self::get_option('bmo_portal_name'); ?>" size="40" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bmo_portal_url">Portal Address (including http://)</label></th>
				<td><input name="bmo_portal_url" type="text" id="bmo_portal_url" value="<?php echo self::get_option('bmo_portal_url'); ?>" size="40" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bmo_login_url">Login location</label></th>
				<td><input name="bmo_login_url" type="text" id="bmo_login_url" value="<?php echo self::get_option('bmo_login_url'); ?>" size="40" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="bmo_logout_url">Logout location</label></th>
				<td><input name="bmo_logout_url" type="text" id="bmo_logout_url" value="<?php echo self::get_option('bmo_logout_url'); ?>" size="40" /></td>
			</tr>
		</table>

		<p class="submit">
		<input type="hidden" name="action" value="update" />
		<input type="submit" name="update_options" class="button" value="<?php _e('Save Changes', 'Localization name') ?> &raquo;" />
		</p>

		</form>
		</div>
	<?php
	}
	
	static function get_option($option) {
		$value = get_option($option);

		if ($value !== false) { 
			return $value;
		}
		// Option did not exist in database so return default values...
		switch ($option) {
		case "bmo_portal_name":
			return 'Default Portal Name';
		case "bmo_session_timeout_seconds":
			return 12000;
		case "bmo_portal_url":
			return 'http://plugin.blockmanagementonline.com/';
		case "bmo_login_url":
			return '/portal/';
		case "bmo_logout_url":
			return '/';
		} 
		return '';
	}
	
}

?>